<?php
/**
 * User: Lennart
 * Date: 1/08/12
 * Time: 21:36
 */
include('connect.php');
class Seizoen
{
    private $id;
    private $seizoen;

    function __construct()
    {
        $this->db = new ConnectionSettings();
        $this->db->connect();
    }
    function create($seizoen){

        //Bestaat seizoen al of niet?
        $query = "SELECT count(id) AS aantal FROM intra_seizoen WHERE seizoen='$seizoen'";
        $resultaat = mysql_query($query);
        $aantal = @mysql_result($resultaat, 0, aantal);

        if(!$aantal) {

            //Pak de eindpunten en zet deze als basispunten!
            //Eerst: ID vorige seizoen ophalen
            $vorige_seizoen = $this->get_huidig_seizoen();
            $vorige_seizoen_id = $vorige_seizoen['id'];

            //Seizoen bestaat niet -> invullen in database
            $query = "INSERT INTO intra_seizoen SET seizoen='$seizoen'";
            mysql_query($query);

            //Haal de gegenereerde ID op.
            $huidig_seizoen = mysql_insert_id();

            //Haal de eindstand op van elke speler van vorig seizoen
            //En voeg nieuwe rij toe in spelerperseizoen
            $resultaat = mysql_query("SELECT speler_id, huidige__punten FROM intra_spelerperseizoen WHERE seizoen_id='$vorige_seizoen_id';");
            while($rij = mysql_fetch_array($resultaat)) {
                $insert_query = "
                    INSERT INTO
                        intra_spelerperseizoen
                    SET
                        speler_id = '{$rij['speler_id']}',
                        seizoen_id = '$huidig_seizoen',
                        basispunten = '{$rij['gemiddelde']}',
                        gespeelde_sets = 0,
                        gewonnen_sets = 0,
                        gespeelde_punten = 0,
                        gewonnen_punten = 0,
                        aanwezig = 0
                        ";

                $gelukt = mysql_query($insert_query);
                if(!$gelukt){
                    return FALSE;
                }
            }
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    function get_huidig_seizoen(){
        $query = mysql_query("SELECT id, seizoen FROM intra_seizoen ORDER BY id DESC LIMIT 1;");
        $resultaat = mysql_fetch_assoc($query);
        $this->id = ${resultaat}['id'];
        $this->seizoen = ${resultaat}['seizoen'];
        return TRUE;
    }





    function bereken_huidig_seizoen()
    {
        $this->get_huidig_seizoen();
        $speeldagen = $this->get_speeldagen_seizoen($this->id);
        $gemiddelde_verliezers_array = array();
        $speeldagnummer = 1;

        // Gemiddelde verliezers bepalen
        foreach($speeldagen as $key => $value)
        {
            $gemiddelde_verliezers = 0;
            $aantal_wedstrijden = 0;
            $speeldag = Speeldag::metId($key);
            $wedstrijden = $speeldag->get_wedstrijden_speeldag();

            foreach($wedstrijden as $wedstrijd)
            {
                $huidige_wedstrijd = new Wedstrijd();
                $huidige_wedstrijd = $wedstrijd;

                $score_array = $huidige_wedstrijd->bepaald_winnaar();
                $gemiddelde_verliezers += ${$score_array}['gemiddelde_verliezers'];
                $aantal_wedstrijden++;
            }

            $gemiddelde_verliezend_speeldag =  $gemiddelde_verliezers/$aantal_wedstrijden;
            $speeldag->gemiddeld_verliezend = $gemiddelde_verliezend_speeldag;
            $speeldag->update();
            $gemiddelde_verliezers_array[$speeldagnummer] = $speeldag;
            $speeldagnummer ++;
        }



        //Resultaat per speler bepalen
        $alle_spelers = new Speler();
        $alle_spelers = $alle_spelers->get_alle_spelers();
        foreach($alle_spelers as $speler_array)
        {
            $speler = new Speler();
            $speler = $speler_array;
            $seizoen_stats = $speler->get_seizoen_stats($this->id);

            $uitslag_array = array();
            // basispunt als beginwaarde zetten
            $uitslag_array[0] = ${seizoen_stats}['basispunten'];
            $speeldag = 1;
            $totaal = 0;
            $afwezig = 0;
            $afwezig_array = array();
            $gespeelde_sets = 0;
            $gewonnen_sets = 0;
            $gespeelde_punten = 0;
            $gewonnen_punten = 0;
            $gewonnen_spelletjes = 0;


            $wedstrijden_speler = $speler->get_wedstrijden($this->id);
            foreach($wedstrijden_speler as $wedstrijd_speler)
            {
                $wedstrijd = new Wedstrijd();
                $wedstrijd = $wedstrijd_speler;
                $winnaar_array = $wedstrijd->bepaal_winnaar();
                //Verder TO DO ....

            }


        }






    }

    private function get_speeldagen_seizoen($seizoen_id)
    {
        $query = sprintf("SELECT id, speeldagnummer from intra_speeldagen WHERE seizoen_id = '%s';", mysql_real_escape_string($seizoen_id));
        $resultaat = $query;
        $speeldagen = array();

        while( $speeldag = mysql_fetch_assoc($resultaat))
        {
            $speeldagen[$speeldag['id']] = $speeldag['speeldagnummer'];
        }

        return $speeldagen;

    }

    function trim_score($score1, $score2) {
        return ($score1 > 21 || $score2 > 21) ? 21/max($score1, $score2) * $score1 : $score1;
    }


}

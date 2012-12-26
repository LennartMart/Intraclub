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

        foreach($speeldagen as $key => $value)
        {
            $speeldag = new Speeldag();
            $wedstrijden = $speeldag->get_wedstrijden_speeldag($key);
            foreach($wedstrijden as $wedstrijd)
            {
                $winnaar_array = $this->bepaal_winnaar($wedstrijd);
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

    function bepaal_winnaar($wedstrijd) {
        $gewonnen_sets_team1 = 0;
        $gewonnen_sets_team2 = 0;
        $totaal_winnende_team = 0;
        $totaal_verliezende_team = 0;
        $aantal_sets_gespeeld = 0;

        if($wedstrijd->set1_1 > $wedstrijd->set1_2)
        {
            $gewonnen_sets_team1 ++;
        }
        else
        {
            $gewonnen_sets_team2++;
        }
        if($wedstrijd->set2_1 > $wedstrijd->set2_2)
        {
            $gewonnen_sets_team1 ++;
        }
        else
        {
            $gewonnen_sets_team2++;
        }
        if( $wedstrijd->set3_1 != 0 || $wedstrijd->set3_2 != 0)
        {
            $aantal_sets_gespeeld = 3;
            if($wedstrijd->set3_1 > $wedstrijd->set3_2)
            {
                $gewonnen_sets_team1 ++;
            }
            else
            {
                $gewonnen_sets_team2++;
            }
        }
        else{
            $aantal_sets_gespeeld = 2;
        }

        $winnaar = ($gewonnen_sets_team1 > $gewonnen_sets_team2) ? 1 : 2;

        $totaal_team1 = trim_score($wedstrijd->set1_1,$wedstrijd->set1_2) + trim_score($wedstrijd->set2_1, $wedstrijd->set2_2) + trim_score($wedstrijd->set3_1, $wedstrijd->set3_2);
        $totaal_team2 = trim_score($wedstrijd->set1_2,$wedstrijd->set1_1) + trim_score($wedstrijd->set2_2, $wedstrijd->set2_1) + trim_score($wedstrijd->set3_2, $wedstrijd->set3_1);

        if($winnaar == 1) {
            $getrimd_totaal_winnende_team = $totaal_team1;
            $getrimd_totaal_verliezende_team = $totaal_team2;
            $totaal_winnende_team = $wedstrijd->set1_1 + $wedstrijd->set2_1 + $wedstrijd->set3_1;
            $totaal_verliezende_team = $wedstrijd->set1_2 + $wedstrijd->set2_2 + $wedstrijd->set3_2;
            $id_winnaars = array($wedstrijd->team1_speler1, $wedstrijd->team1_speler2);
            $id_verliezers = array($wedstrijd->team2_speler1, $wedstrijd->team2_speler2);
        }
        else{
            $getrimd_totaal_winnende_team = $totaal_team2;
            $getrimd_totaal_verliezende_team = $totaal_team1;
            $totaal_winnende_team = $wedstrijd->set1_2 + $wedstrijd->set2_2 + $wedstrijd->set3_2;
            $totaal_verliezende_team = $wedstrijd->set1_1 + $wedstrijd->set2_1 + $wedstrijd->set3_1;
            $id_winnaars = array($wedstrijd->team2_speler1, $wedstrijd->team2_speler2);
            $id_verliezers = array($wedstrijd->team1_speler1, $wedstrijd->team1_speler2);
        }

        $return["winnaar"] = $winnaar;
        $return["aantal_sets"] = $aantal_sets_gespeeld;
        $return["totaal_winnaars"] = $totaal_winnende_team;
        $return["totaal_verliezers"] = $totaal_verliezende_team;
        $return["gemiddelde_winnaars"] = $getrimd_totaal_winnende_team / $aantal_sets_gespeeld;
        $return["gemiddelde_verliezers"] = $getrimd_totaal_verliezende_team / $aantal_sets_gespeeld;
        $return["id_winnaars"] = $id_winnaars;
        $return["id_verliezers"] = $id_verliezers;
        $return["aantal_punten"] = $totaal_verliezende_team + $totaal_winnende_team;

        return $return;
    }
}

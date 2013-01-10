<?php
/**
 * User: Lennart
 * Date: 1/08/12
 * Time: 21:36
 */
include('connect.php');
include('Speeldag.php');
include('Wedstrijd.php');
include('Speler.php');
class Seizoen
{
    private $id;
    private $seizoen;

    function __construct()
    {
        $this->db = new ConnectionSettings();
        $this->db->connect();
    }

    static function huidig_seizoen(){
        $instance = new self();
        $instance->get_huidig_seizoen();
        return $instance;
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
                $insert_query = sprintf("
                    INSERT INTO
                        intra_spelerperseizoen
                    SET
                        speler_id = '%s',
                        seizoen_id = '%s',
                        basispunten = '%s',
                        gespeelde_sets = 0,
                        gewonnen_sets = 0,
                        gespeelde_punten = 0,
                        gewonnen_punten = 0,
                        aanwezig = 0
                        ",$rij["speler_id"], $huidig_seizoen, $rij["gemiddelde"]);

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



    private function get_huidig_seizoen(){
        $query = mysql_query("SELECT id, seizoen FROM intra_seizoen ORDER BY id DESC LIMIT 1;");
        $resultaat = mysql_fetch_assoc($query);
        $this->id = $resultaat['id'];
        $this->seizoen = $resultaat['seizoen'];
        return TRUE;
    }

    function bereken_huidig_seizoen()
    {
        $this->get_huidig_seizoen();

        $speeldagen_seizoen = $this->get_speeldagen($this->id);

        $gemiddelde_verliezers_array = array();
        $speeldagnummer = 1;
        $ranking_spelers_alle_speeldagen = array();

        // Gemiddelde verliezers bepalen
        foreach($speeldagen_seizoen as $speeldag)
        {
            /* @var $speeldag Speeldag */
            $gemiddelde_verliezers = 0;
            $aantal_wedstrijden = 0;

            $ranking_spelers_alle_speeldagen[$speeldag->speeldagnummer] = array();
            $wedstrijden = $speeldag->get_wedstrijden();

            foreach($wedstrijden as $wedstrijd)
            {
                /* @var $wedstrijd Wedstrijd */
                $score_array = $wedstrijd->bepaald_winnaar();
                $gemiddelde_verliezers += $$score_array['gemiddelde_verliezers'];
                $aantal_wedstrijden++;
            }

            $gemiddelde_verliezend_speeldag =  $gemiddelde_verliezers/$aantal_wedstrijden;
            $speeldag->gemiddeld_verliezend = $gemiddelde_verliezend_speeldag;
            $speeldag->update();

            $gemiddelde_verliezers_array[$speeldagnummer] = $speeldag->gemiddeld_verliezend;
            $speeldagnummer ++;
        }

        $laatste_speeldag = $speeldagnummer -1;

        //MultiArray om alle spelertussenstanden in op te slaan


        //Resultaat per speler bepalen
        $alle_spelers = new Speler();
        $alle_spelers = $alle_spelers->get_alle_spelers();
        foreach($alle_spelers as $speler)
        {
            /* @var $speler Speler */
            $ranking_spelers_alle_speeldagen[$speeldag->speeldagnummer][$speler->id] = array();
            $seizoen_stats = $speler->get_seizoen_stats($this->id);

            $uitslag_array = array();
            // basispunt als beginwaarde zetten
            $uitslag_array[0] = $seizoen_stats['basispunten'];
            $speeldag = 1;
            $totaal = 0;
            $afwezig_array = array();
            $gespeelde_sets = 0;
            $gewonnen_sets = 0;
            $gespeelde_punten = 0;
            $gewonnen_punten = 0;
            $gewonnen_matchen = 0;

            $wedstrijden_speler = $speler->get_wedstrijden($this->id);
            foreach($wedstrijden_speler as $wedstrijd_speler)
            {
                /* @var $wedstrijd_speler Wedstrijd */
                $huidige_speeldag = Speeldag::metId($wedstrijd_speler->speeldag_id);
                while($huidige_speeldag > $speeldag)
                {
                    //Speler niet aanwezig op $speeldag
                    //Geef hem gemiddelde verliezers van die speeldag!
                    $uitslag_array[$speeldag] = $gemiddelde_verliezers_array[$speeldag];
                    $afwezig_array[$speeldag] = true;
                    $speeldag++;
                }
                // meerdere spelletjes gespeeld, OVERSLAAN
                if($speeldag > $huidige_speeldag) {
                    //Meermaals aanwezig op huidige speeldag
                }

                //We zitten goed!
                else if($speeldag == $huidige_speeldag) {
                    $winnaar_array = $wedstrijd_speler->bepaal_winnaar();
                    $gespeelde_punten += $winnaar_array["aantal_punten"];
                    $gespeelde_sets += $winnaar_array["aantal_sets"];
                    $afwezig_array[$speeldag] = false;

                    if(in_array($speler->id, $winnaar_array["id_winnaars"])) {
                        // speler heeft gewonnen!
                        $uitslag_array[$speeldag] = $winnaar_array["gemiddelde_punten_winnaars"];
                        $gewonnen_punten += $winnaar_array["totaal_punten_winnaars"];
                        $gewonnen_sets += 2;
                        $gewonnen_matchen++;
                    }
                    else {
                        // speler heeft verloren, jammer
                        $uitslag_array[$speeldag] = $winnaar_array["gemiddelde_punten_verliezers"];
                        $gewonnen_punten += $winnaar_array["totaal_punten_verliezers"];
                        $gewonnen_sets += $winnaar_array["aantal_sets"] - 2;
                    }

                    //Volgende speeldag...
                    $speeldag++;
                }
            }
            // laatste speeldagen niet aanwezig
            while($speeldag <= $laatste_speeldag) {
                $uitslag_array[$speeldag] = $gemiddelde_verliezers_array[$speeldag];
                $afwezig_array[$speeldag] = true;
                $speeldag++;
            }



            //We hebben nu $uitslag_array[speeldag] met gemiddelde voor elke speeldag en afwezig_array[speeldag] met afwezigheden...
            for($i = 1; i<=$laatste_speeldag; $i++){
                //start met basispunten => zitten in 0!
                $som =0;
                $aantal_speeldagen = 0;
                for($j =0; $j<=$i; $j++){
                    $som += $uitslag_array[$j];
                    $aantal_speeldagen++;
                }
                $id_speeldag = array_search($aantal_speeldagen -1, $speeldagen_seizoen);
                $tussenstand_speeldag = $som/($aantal_speeldagen-1);
                //REWRITE: scheiden van gemiddelde en afwezigheden!
                $ranking_spelers_alle_speeldagen[$aantal_speeldagen-1][$speler->id] = array( "afwezig" => $afwezig_array[$aantal_speeldagen-1],
                                                                                             "gemiddelde" => $tussenstand_speeldag
                                                                                            );

            }


            //Ten slotte: update seizoeninfo!


        }

        foreach($speeldagen_seizoen as $key => $value)
        {
            $tesorteren_array = $ranking_spelers_alle_speeldagen[$value];

        }






    }

    private function get_speeldagen($seizoen_id)
    {
        $query = sprintf("SELECT id, speeldagnummer from intra_speeldagen WHERE seizoen_id = '%s';", mysql_real_escape_string($seizoen_id));
        $resultaat = $query;
        $speeldagen = array();

        while( $rij = mysql_fetch_assoc($resultaat))
        {
            $speeldag = new Speeldag();
            $speeldag->vulop($rij);
            $speeldagen[] = $speeldag;
        }

        return $speeldagen;

    }




}

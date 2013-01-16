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
include('Spelers.php');
class Seizoen
{
    public $id;
    public $seizoen;

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
                $gemiddelde_verliezers += $score_array['gemiddelde_verliezers'];
                $aantal_wedstrijden++;
            }

            $gemiddelde_verliezend_speeldag =  $gemiddelde_verliezers/$aantal_wedstrijden;
            $speeldag->gemiddeld_verliezend = $gemiddelde_verliezend_speeldag;
            $speeldag->update();

            $gemiddelde_verliezers_array[$speeldagnummer] = $speeldag->gemiddeld_verliezend;
            $speeldagnummer ++;
        }

        $laatste_speeldag = $speeldagnummer -1;

        //Resultaat per speler bepalen
        $alle_spelers = new Spelers();
        $alle_spelers = $alle_spelers->get_spelers(true);

        foreach($alle_spelers as $speler)
        {
            /* @var $speler Speler */
            $ranking_spelers_alle_speeldagen[$speeldag->speeldagnummer][$speler->id] = array();
            $seizoen_stats = $speler->get_seizoen_stats($this->id);

            $uitslag_array = array();
            // basispunt als beginwaarde zetten
            $uitslag_array[0] = $seizoen_stats['basispunten'];
            $speeldag = 1;

            $seizoen_stats = array();
            $seizoen_stats["gespeelde_sets"] = 0;
            $seizoen_stats["gewonnen_sets"] = 0;
            $seizoen_stats["gespeelde_punten"] = 0;
            $seizoen_stats["gewonnen_punten"] = 0;
            $seizoen_stats["gewonnen_matchen"] = 0;

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
                    $speeldag++;
                }
                // meerdere spelletjes gespeeld, OVERSLAAN
                if($speeldag > $huidige_speeldag) {
                    //Meermaals aanwezig op huidige speeldag
                }

                //We zitten goed!
                else if($speeldag == $huidige_speeldag) {
                    $winnaar_array = $wedstrijd_speler->bepaal_winnaar();
                    $seizoen_stats["gespeelde_punten"] += $winnaar_array["aantal_punten"];
                    $seizoen_stats["gespeelde_sets"] += $winnaar_array["aantal_sets"];
                    $afwezig_array[$speeldag] = false;

                    if(in_array($speler->id, $winnaar_array["id_winnaars"])) {
                        // speler heeft gewonnen!
                        $uitslag_array[$speeldag] = $winnaar_array["gemiddelde_punten_winnaars"];
                        $seizoen_stats["gespeelde_punten"] += $winnaar_array["totaal_punten_winnaars"];
                        $seizoen_stats["gewonnen_sets"] += 2;
                        $seizoen_stats["gewonnen_matchen"]++;
                    }
                    else {
                        // speler heeft verloren, jammer
                        $uitslag_array[$speeldag] = $winnaar_array["gemiddelde_punten_verliezers"];
                        $seizoen_stats["gewonnen_punten"] += $winnaar_array["totaal_punten_verliezers"];
                        $seizoen_stats["gewonnen_sets"] += $winnaar_array["aantal_sets"] - 2;
                    }

                    //Volgende speeldag...
                    $speeldag++;
                }
            }
            // laatste speeldagen niet aanwezig
            while($speeldag <= $laatste_speeldag) {
                $uitslag_array[$speeldag] = $gemiddelde_verliezers_array[$speeldag];
                $speeldag++;
            }

            //We hebben nu $uitslag_array[speeldag] met gemiddelde voor elke speeldag van de speler
            //Geef speeldag  mee, samen met uitslag speeldag.
            //Ranking = 0, want we weten dit niet!
            //Hebben gemiddelde speeldag, MAAR MOETEN GEMIDDELDE TOT DIE SPEELDAG BEREKENEN! => done

            //  spelers rankschikken per speeldag en dan pas update_speeldagstats => enkel nog update_speeldagstats
            foreach($speeldagen_seizoen as $speeldag)
            {
                /* @var $speeldag Speeldag */
                $som =0;
                for($j =0; $j<=$speeldag->speeldagnummer; $j++){
                    $som += $uitslag_array[$j];
                }
                //Tussenstand speeldag delen door aantal speeldagen +1
                //+1 = basispunten
                $tussenstand_speeldag = $som/($speeldag->speeldagnummer +1);
                $ranking_spelers_alle_speeldagen[$speeldag->id][] = array('speler_id' => $speler->id, 'gemiddelde' => $tussenstand_speeldag);

            }
            //Ten slotte: update seizoeninfo!
            $speler->update_seizoenstats($seizoen_stats);

        }

        //Magic Sorting
        //Gebaseerd op http://php.net/manual/en/function.array-multisort.php
        //Deze foreach sorteert de spelers per speeldag
        foreach($speeldagen_seizoen as $speeldag)
        {
            /* @var $speeldag Speeldag */
            // Obtain a list of columns
            foreach ($ranking_spelers_alle_speeldagen[$speeldag->id] as $key => $row) {
                $speler_id[$key]  = $row['speler_id'];
                $gemiddelde[$key] = $row['gemiddelde'];
            }

            // Add $data as the last parameter, to sort by the common key
            array_multisort($gemiddelde, SORT_DESC,$data);

            foreach ($ranking_spelers_alle_speeldagen[$speeldag->id] as $key => $row) {
                //Update the speeldagstats
                $speler = new Speler();
                $speler->update_speeldagstats($row['speler_id'], $speeldag,$row['gemiddelde'],$key);
            }

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

<?php
/**
 * User: Lennart
 * Date: 1/08/12
 * Time: 21:36
 */
include('connect.php');
class Seizoen
{

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
        $resultaat = mysql_query("SELECT id, seizoen FROM intra_seizoen ORDER BY id DESC LIMIT 1;");
        return mysql_fetch_assoc($resultaat);
    }
}

<?php
/**
 * User: Lennart
 * Date: 1/08/12
 * Time: 14:09
 */

include('connect.php');
include('seizoen.php');
class Speler
{
    public $id = "";
    public $voornaam = "";
    public $achternaam = "";
    public $geslacht = "";
    public $jeugd = "";
    public $klassement = "";

    function __construct(){
        $this->db = new ConnectionSettings();
        $this->db->connect();
    }

    //Maak een nieuwe speler aan
    function create($data){

        //Insert een nieuwe rij in de spelerstabel
        $query = "
            INSERT INTO
                intra_spelers
            SET
                voornaam = '$data->voornaam',
                achternaam = '$data->achternaam',
                geslacht = '$data->geslacht',
                jeugd = '$data->jeugd',
                klassement_id= '$data->klassement'
                ";

        $result = mysql_query($query);
        if(!$result) {
            return FALSE;
        }
        else{
            //Haal de gegenereerde ID op.
            $speler_id = mysql_insert_id();
            $seizoen = new Seizoen();
            $huidig_seizoen = $seizoen->get_huidig_seizoen();
            $gemiddelde_overal = $this->get_gemiddelde_allespelers($huidig_seizoen);
            //Insert een rij voor de speler in het huidige seizoen
            $query = "
            INSERT INTO
                intra_spelerperseizoen
            SET
              speler_id = '$speler_id',
              seizoen_id = '$huidig_seizoen',
              basispunten = '$gemiddelde_overal',
              huidige_punten = '$gemiddelde_overal',
              gespeelde_sets = 0,
              gewonnen_sets = 0,
              gespeelde_punten = 0,
              gewonnen_punten = 0,
              aanwezig = 0,
              ";
            $result = mysql_query($query);
            if(!$result) {
                return FALSE;
            }
            return TRUE;
        }

    }
     
    //Hoort deze functie hier thuis?
    function get_gemiddelde_allespelers($seizoen_id){
        $query = "SELECT AVG(huidige_punten) as gemiddelde_alle from intra_spelerperseizoen where seizoen_id = '$seizoen_id';";
        $resultaat = mysql_query($query);
        return @mysql_result($resultaat, 0, gemiddelde_alle);
    }
    function get_basisinfo($speler_id){
        $query = "SELECT * from intra_spelers where id = '$speler_id';";
        $resultaat = mysql_query($query);

        //return assoc tabel
        return mysql_fetch_assoc($resultaat);
    }
    function get_seizoen_stats($speler_id, $seizoen_id){
        $query = "SELECT * from intra_spelerperseizoen where speler_id = '$speler_id' AND seizoen_id = '$seizoen_id';";
        $resultaat = mysql_query($query);

        //return assoc tabel
        return mysql_fetch_assoc($resultaat);
    }
    function get_speeldagstats($speler_id, $speeldag_id){
        $query = "SELECT * from intra_spelerperspeeldag where speler_id = '$speler_id' AND speeldag_id = '$speeldag_id';";
        $resultaat = mysql_query($query);

        //return assoc tabel
        return mysql_fetch_assoc($resultaat);
    }

    function update_basisinfo($data){
    }

    function update_seizoenstats($data){
    }

    function update_speeldagstats($data){
    }




}

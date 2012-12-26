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
        $query = sprintf("
            INSERT INTO
                intra_spelers
            SET
                voornaam = '%s',
                achternaam = '%s',
                geslacht = '%s',
                jeugd = '%s',
                klassement= '%s'
                ",
                mysql_real_escape_string(${data}['voornaam']),
                mysql_real_escape_string(${data}['achternaam']),
                mysql_real_escape_string(${data}['geslacht']),
                mysql_real_escape_string(${data}['jeugd']),
                mysql_real_escape_string(${data}['klassement']));

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
            $query = sprintf("
            INSERT INTO
                intra_spelerperseizoen
            SET
              speler_id = '%s',
              seizoen_id = '%s',
              basispunten = '%s',
              huidige_punten = '%s',
              gespeelde_sets = 0,
              gewonnen_sets = 0,
              gespeelde_punten = 0,
              gewonnen_punten = 0,
              aanwezig = 0,
              ",
                mysql_real_escape_string($speler_id),
                mysql_real_escape_string($huidig_seizoen),
                mysql_real_escape_string($gemiddelde_overal),
                mysql_real_escape_string($gemiddelde_overal));

            $result = mysql_query($query);
            if(!$result) {
                return FALSE;
            }
            return TRUE;
        }

    }

    function get_alle_spelers()
    {
        $resultaat = mysql_query("SELECT * from intra_spelers");
        $spelers = array();
        while($array_spelers = mysql_fetch_array($resultaat))
        {
            $speler = new Speler();
            $speler->vulop($array_spelers);
            array_push($spelers,$speler);
        }
        return $spelers;

    }
    //Hoort deze functie hier thuis?
    function get_gemiddelde_allespelers($seizoen_id){
        $query = sprintf("SELECT AVG(huidige_punten) as gemiddelde_alle from intra_spelerperseizoen where seizoen_id = '%s';",mysql_real_escape_string($seizoen_id)) ;
        $resultaat = mysql_query($query);
        return @mysql_result($resultaat, 0, gemiddelde_alle);
    }

    function get_basisinfo($speler_id){
        $query = sprintf("SELECT * from intra_spelers where id = '%s';", mysql_real_escape_string($speler_id));
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
        $query = "
            UPDATE
                intra_spelers
            SET
                voornaam = ${data}['voornaam'],
                achternaam = ${data}['achternaam'],
                geslacht = ${data}['geslacht'],
                jeugd = ${data}['jeugd'],
                klassement_id= ${data}['klassement']

            WHERE
                id = ${data}['id']
        ";

        return mysql_query($query);
    }

    function update_seizoenstats($data){
    }

    function update_speeldagstats($data){
    }

    private function vulop($speler)
    {
        $this->id = ${speler}['id'];
        $this->voornaam = ${speler}['voornaam'];
        $this->achternaam = ${speler}['achternaam'];
        $this->geslacht = ${speler}['geslacht'];
        $this->jeugd = ${speler}['jeugd'];
        $this->klassement = ${speler}['klassement'];
    }




}

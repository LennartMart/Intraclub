<?php
/**
 * User: Lennart
 * Date: 1/08/12
 * Time: 22:55
 */
include('connect.php');
class Speeldag
{
    function __construct(){
        $this->db = new ConnectionSettings();
        $this->db->connect();
    }
    function voeg_toe($data){

    }

    function get($speeldag_id){
        $resultaat = mysql_query("SELECT * FROM intra_speeldagen WHERE id= '$speeldag_id';");
        return mysql_fetch_assoc($resultaat);
    }

    function get_wedstrijden_speeldag($speeldag_id){
        //Verzamel de wedstrijden uit DB, vul de members van de Wedstrijd-objecten in en return een List<Wedstrijden>
        //TODO: deze code hier invullen.

    }

    function get_laatste_speeldag(){
        $resultaat = mysql_query("SELECT * FROM intra_speeldagen ORDER BY id DESC LIMIT 1;");
        return mysql_fetch_assoc($resultaat);
    }

    function update($speeldag){

    }

}

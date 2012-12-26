<?php
/**
 * User: Lennart
 * Date: 1/08/12
 * Time: 22:55
 */
include('connect.php');
include('Wedstrijd.php');
class Speeldag
{

    function __construct(){
        $this->db = new ConnectionSettings();
        $this->db->connect();
    }
    function voeg_toe($data){
        $query = "
            INSERT INTO
                intra_speeldagen
            SET
              speeldagnummer = ${data}['nummer'],
              seizoen_id = ${data}['seizoen_id'],
              gemiddeld_verliezend = 0,
              ";
        $result = mysql_query($query);
    }

    function get($speeldag_id){
        $resultaat = mysql_query("SELECT * FROM intra_speeldagen WHERE id= '$speeldag_id';");
        return mysql_fetch_assoc($resultaat);
    }

    function get_wedstrijden_speeldag($speeldag_id){
        //Verzamel de wedstrijden uit DB, vul de members van de Wedstrijd-objecten in en return een List<Wedstrijden>
        $resultaat = mysql_query("SELECT * FROM intra_wedstrijden WHERE speeldag_id= '$speeldag_id';");
        $wedstrijden = array();
        while($array_uitslagen = mysql_fetch_array($resultaat))
        {
            $wedstrijd = new Wedstrijd();
            $wedstrijd->vulop($array_uitslagen);
            array_push($wedstrijden, $wedstrijd);
        }
        return $wedstrijden;
    }

    function get_laatste_speeldag(){
        $resultaat = mysql_query("SELECT * FROM intra_speeldagen ORDER BY id DESC LIMIT 1;");
        return mysql_fetch_assoc($resultaat);
    }

    function update($speeldag){
        $query = "
            UPDATE intra_speeldagen
            set gemiddeld_verliezend = ${speeldag}['verliezend']
            WHERE id = ${speeldag}['speeldag_id']
        ";
        return mysql_query($query);
    }

}

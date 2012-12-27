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
    public $id;
    public $speeldagnummer;
    public $gemiddeld_verliezend;

    function __construct(){
        $this->db = new ConnectionSettings();
        $this->db->connect();
    }
    static function metId($speeldag_id){
        $instance = new self();
        $instance->get($speeldag_id);
        return $instance;
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
        $this->vulop($resultaat);
    }

    function get_wedstrijden_speeldag(){
        //Verzamel de wedstrijden uit DB, vul de members van de Wedstrijd-objecten in en return een List<Wedstrijden>
        $resultaat = mysql_query("SELECT * FROM intra_wedstrijden WHERE speeldag_id= '$this->id';");
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
    function vulop($data)
    {
        $this->id = ${data}['id'];
        $this->gemiddeld_verliezend = ${data}['gemiddeld_verliezend'];
        $this->speeldagnummer = ${data}['speeldagnummer'];
    }
    function update(){
        $query = sprintf("
            UPDATE intra_speeldagen
            set gemiddeld_verliezend = '%s'
            WHERE id = '%s';
        ",
            mysql_real_escape_string($this->gemiddeld_verliezend),
            mysql_real_escape_string($this->id))
        ;
        return mysql_query($query);
    }

}

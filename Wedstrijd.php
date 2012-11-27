<?php
/**
 * User: Lennart
 * Date: 1/08/12
 * Time: 22:55
 * To change this template use File | Settings | File Templates.
 */

include('connect.php');
class Wedstrijd
{
    private $wedstrijd_id;
    private $speeldag_id;
    private $team1_speler1;
    private $team1_speler2;
    private $team2_speler1;
    private $team2_speler2;
    private $set1_1;
    private $set1_2;
    private $set2_1;
    private $set2_2;
    private $set3_1;
    private $set3_2;

    function __construct(){
        $this->db = new ConnectionSettings();
        $this->db->connect();
    }
    function __construct($wedstrijd_id){
        $this->db = new ConnectionSettings();
        $this->db->connect();
        $this->get($wedstrijd_id);
    }
    function voeg_toe($data){
        //Beveiliging insert data!
        //Derde set = 0 indien niet gespeeld
        if($data['set3_1'] == "" && $data['set3_2'] == "")
        {
            $data['set3_1'] = 0;
            $data['set3_2'] = 0;
        }
        $insert_query = "INSERT INTO intra_wedstrijden 
                  SET (speeldag_id,team1_speler1,team1_speler2,team2_speler1,team2_speler2,set1_1,set1_2,set2_1,set2_2,set3_1,set3_2)
                  VALUES($data['speeldag_id'],$data['team1_speler1'],$data['team1_speler2'],$data['team2_speler1'],$data['team2_speler2'],$data['set1_1'],$data['set1_2'],$data['set2_1'],$data['set2_2'],$data['set3_1'],$data['set3_2']);";
        
        return mysql_query($insert_query);        
    }

    function get($wedstrijd_id){
        //Haal wedstrijd op en vul de members in
        $get_query = "SELECT from intra_wedstrijden WHERE wedstrijd_id = '$wedstrijd_id';";

        $gelukt = mysql_query($get_query);
        if( $gelukt )
        {
            $row = mysql_fetch_assoc($result)
            $this->wedstrijd_id = $row['wedstrijd_id'];
            $this->speeldag_id = $row['speeldag_id'];;
            $this->team1_speler1 = $row['team1_speler1'];
            $this->team1_speler2 = $row['team1_speler2'];
            $this->team2_speler1 = $row['team2_speler1'];
            $this->team2_speler2 = $row['team2_speler2'];
            $this->set1_1 = $row['set1_1'];
            $this->set1_2 = $row['set1_2'];
            $this->set2_1 = $row['set2_1'];
            $this->set2_2 = $row['set2_2'];
            $this->set3_1 = $row['set3_1'];
            $this->set3_2 = $row['set3_2'];
            return TRUE;
        }

        return FALSE;
    }
    function update($wedstrijd){

    }

}

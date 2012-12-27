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
    public $wedstrijd_id;
    public $speeldag_id;
    public $team1_speler1;
    public $team1_speler2;
    public $team2_speler1;
    public $team2_speler2;
    public $set1_1;
    public $set1_2;
    public $set2_1;
    public $set2_2;
    public $set3_1;
    public $set3_2;

    function __construct(){
        $this->db = new ConnectionSettings();
        $this->db->connect();
    }
    static function metId($wedstrijd_id){
        $instance = new self();
        $instance->get($wedstrijd_id);
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
                  VALUES(${data}['speeldag_id'],${data}['team1_speler1'],${data}['team1_speler2'],${data}['team2_speler1'],${data}['team2_speler2'],${data}['set1_1'],${data}['set1_2'],${data}['set2_1'],${data}['set2_2'],${data}['set3_1'],${data}['set3_2']);";
        
        return mysql_query($insert_query);        
    }

    function get($wedstrijd_id){
        //Haal wedstrijd op en vul de members in
        $get_query = "SELECT from intra_wedstrijden WHERE id = '$wedstrijd_id';";

        $gelukt = mysql_query($get_query);
        if( $gelukt )
        {
            $row = mysql_fetch_assoc($gelukt);
            $this->vulop($row);
            return TRUE;
        }

        return FALSE;
    }
     
    //input = associatieve tabel uit db
    //Kan gebruikt worden in speeldag bij alle wedstrijden op te halen
    function vulop($resultaat){
        $this->wedstrijd_id = $resultaat['id'];
        $this->speeldag_id = $resultaat['speeldag_id'];;
        $this->team1_speler1 = $resultaat['team1_speler1'];
        $this->team1_speler2 = $resultaat['team1_speler2'];
        $this->team2_speler1 = $resultaat['team2_speler1'];
        $this->team2_speler2 = $resultaat['team2_speler2'];
        $this->set1_1 = $resultaat['set1_1'];
        $this->set1_2 = $resultaat['set1_2'];
        $this->set2_1 = $resultaat['set2_1'];
        $this->set2_2 = $resultaat['set2_2'];
        $this->set3_1 = $resultaat['set3_1'];
        $this->set3_2 = $resultaat['set3_2'];
    }
    function update($wedstrijd){

    }

    function bepaal_winnaar() {
        $gewonnen_sets_team1 = 0;
        $gewonnen_sets_team2 = 0;
        $totaal_winnende_team = 0;
        $totaal_verliezende_team = 0;
        $aantal_sets_gespeeld = 0;

        if($this->set1_1 > $this->set1_2)
        {
            $gewonnen_sets_team1 ++;
        }
        else
        {
            $gewonnen_sets_team2++;
        }
        if($this->set2_1 > $this->set2_2)
        {
            $gewonnen_sets_team1 ++;
        }
        else
        {
            $gewonnen_sets_team2++;
        }
        if( $this->set3_1 != 0 || $this->set3_2 != 0)
        {
            $aantal_sets_gespeeld = 3;
            if($this->set3_1 > $this->set3_2)
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

        $totaal_team1 = trim_score($this->set1_1,$this->set1_2) + trim_score($this->set2_1, $this->set2_2) + trim_score($this->set3_1, $this->set3_2);
        $totaal_team2 = trim_score($this->set1_2,$this->set1_1) + trim_score($this->set2_2, $this->set2_1) + trim_score($this->set3_2, $this->set3_1);

        if($winnaar == 1) {
            $getrimd_totaal_winnende_team = $totaal_team1;
            $getrimd_totaal_verliezende_team = $totaal_team2;
            $totaal_winnende_team = $this->set1_1 + $this->set2_1 + $this->set3_1;
            $totaal_verliezende_team = $this->set1_2 + $this->set2_2 + $this->set3_2;
            $id_winnaars = array($this->team1_speler1, $this->team1_speler2);
            $id_verliezers = array($this->team2_speler1, $this->team2_speler2);
        }
        else{
            $getrimd_totaal_winnende_team = $totaal_team2;
            $getrimd_totaal_verliezende_team = $totaal_team1;
            $totaal_winnende_team = $this->set1_2 + $this->set2_2 + $this->set3_2;
            $totaal_verliezende_team = $this->set1_1 + $this->set2_1 + $this->set3_1;
            $id_winnaars = array($this->team2_speler1, $this->team2_speler2);
            $id_verliezers = array($this->team1_speler1, $this->team1_speler2);
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

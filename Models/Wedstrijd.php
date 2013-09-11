<?php

    require_once(__DIR__ . '/../connect.php');
    require_once(__DIR__ . '/../Interfaces/IWedstrijd.php');

    class Wedstrijd implements IWedstrijd
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

        function __construct()
        {
            $this->db = new ConnectionSettings();
            $this->db->connect();
        }
        /**
         * public get
         * @param $wedstrijd_id
         * @return bool true indien alles netjes ingevuld
         */
        public function get($wedstrijd_id)
        {
            //Haal wedstrijd op en vul de members in
            $get_query = "SELECT from intra_wedstrijden WHERE id = '$wedstrijd_id';";

            $gelukt = mysql_query($get_query);
            if ($gelukt) {
                $row = mysql_fetch_assoc($gelukt);
                $this->vulop($row);
                return TRUE;
            }

            return FALSE;
        }
        /**
         * Voegt een nieuwe speeldag toe aan de database.
         * @param $data : moet alle info bevatten van de twee teams + speeldag_id
         * @return bool true indien geslaagd, false bij mislukking
         */
        public function voeg_toe($data)
        {

            //Dubbel-Insert beveiliging

            $result = mysql_query(sprintf("SELECT * FROM intra_wedstrijden

                                                WHERE speeldag_id = '%s' AND
                                                team1_speler1 = '%s' AND
                                                team1_speler2 = '%s' AND
                                                team2_speler1 = '%s' AND
                                                team2_speler2 = '%s' AND
                                                set1_1 = '%s' AND
                                                set1_2 = '%s' AND
                                                set2_1 = '%s' AND
                                                set2_2 = '%s' AND
                                                set3_1 = '%s' AND
                                                set3_2 = '%s';",
                                mysql_real_escape_string($data['speeldag_id']),
                                mysql_real_escape_string($data['team1_speler1']),
                                mysql_real_escape_string($data['team1_speler2']),
                                mysql_real_escape_string($data['team2_speler1']),
                                mysql_real_escape_string($data['team2_speler2']),
                                mysql_real_escape_string($data['set1_1']),
                                mysql_real_escape_string($data['set1_2']),
                                mysql_real_escape_string($data['set2_1']),
                                mysql_real_escape_string($data['set2_2']),
                                mysql_real_escape_string($data['set3_1']),
                                mysql_real_escape_string($data['set3_2'])));
            $num_rows = mysql_num_rows($result);


            if ($num_rows == 0) {
                //Beveiliging insert data!
                //Derde set = 0 indien niet gespeeld
                //if ($data['set3_1'] == "" && $data['set3_2'] == "") {
                //    $data['set3_1'] = 0;
                //    $data['set3_2'] = 0;
                //}
                $insert_query = sprintf("INSERT INTO intra_wedstrijden
                                 SET
                                    speeldag_id = '%s',
                                    team1_speler1 = '%s',
                                    team1_speler2 = '%s',
                                    team2_speler1 = '%s',
                                    team2_speler2 = '%s',
                                    set1_1 = '%s',
                                    set1_2 = '%s',
                                    set2_1 = '%s',
                                    set2_2 = '%s',
                                    set3_1 = '%s',
                                    set3_2 = '%s';",
                                 mysql_real_escape_string($data['speeldag_id']),
                                 mysql_real_escape_string($data['team1_speler1']),
                                 mysql_real_escape_string($data['team1_speler2']),
                                 mysql_real_escape_string($data['team2_speler1']),
                                 mysql_real_escape_string($data['team2_speler2']),
                                 mysql_real_escape_string($data['set1_1']),
                                 mysql_real_escape_string($data['set1_2']),
                                 mysql_real_escape_string($data['set2_1']),
                                 mysql_real_escape_string($data['set2_2']),
                                 mysql_real_escape_string($data['set3_1']),
                                 mysql_real_escape_string($data['set3_2']));

                return mysql_query($insert_query);
            }

            return FALSE;
        }



        /**
         * Vult een object van Wedstrijd op
         * Handig bij alle get functies!
         * @param $resultaat
         */
        public function vulop($resultaat)
        {
            $this->wedstrijd_id = $resultaat['id'];
            $this->speeldag_id = $resultaat['speeldag_id'];
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

        public function update($wedstrijd)
        {
            //Beveiliging insert data!
            //Derde set = 0 indien niet gespeeld
            //if ($wedstrijd['set3_1'] == "" && $wedstrijd['set3_2'] == "") {
            //    $wedstrijd['set3_1'] = 0;
            //    $wedstrijd['set3_2'] = 0;
            //}
            $insert_query = sprintf("UPDATE intra_wedstrijden
                             SET
                                speeldag_id = '%s',
                                team1_speler1 = '%s',
                                team1_speler2 = '%s',
                                team2_speler1 = '%s',
                                team2_speler2 = '%s',
                                set1_1 = '%s',
                                set1_2 = '%s',
                                set2_1 = '%s',
                                set2_2 = '%s',
                                set3_1 = '%s',
                                set3_2 = '%s'
                             WHERE
                                id = '%s'",
                            mysql_real_escape_string(${wedstrijd}['speeldag_id']),
                            mysql_real_escape_string(${wedstrijd}['team1_speler1']),
                            mysql_real_escape_string(${wedstrijd}['team1_speler2']),
                            mysql_real_escape_string(${wedstrijd}['team2_speler1']),
                            mysql_real_escape_string(${wedstrijd}['team2_speler2']),
                            mysql_real_escape_string(${wedstrijd}['set1_1']),
                            mysql_real_escape_string(${wedstrijd}['set1_2']),
                            mysql_real_escape_string(${wedstrijd}['set2_1']),
                            mysql_real_escape_string(${wedstrijd}['set2_2']),
                            mysql_real_escape_string(${wedstrijd}['set3_1']),
                            mysql_real_escape_string(${wedstrijd}['set3_2']),
                            mysql_real_escape_string(${wedstrijd}['id']));

            return mysql_query($insert_query);
        }

        public function bepaal_winnaar()
        {
            $gewonnen_sets_team1 = 0;
            $gewonnen_sets_team2 = 0;
            $totaal_winnende_team = 0;
            $totaal_verliezende_team = 0;
            $aantal_sets_gespeeld = 0;

            if ($this->set1_1 > $this->set1_2) {
                $gewonnen_sets_team1++;
            } else {
                $gewonnen_sets_team2++;
            }
            if ($this->set2_1 > $this->set2_2) {
                $gewonnen_sets_team1++;
            } else {
                $gewonnen_sets_team2++;
            }
            if (($this->set3_1 != '' && $this->set3_2 != '') || ($this->set3_1 != 0 && $this->set3_2 != 0)) {
                $aantal_sets_gespeeld = 3;
                if ($this->set3_1 > $this->set3_2) {
                    $gewonnen_sets_team1++;
                } else {
                    $gewonnen_sets_team2++;
                }
            } else {
                $aantal_sets_gespeeld = 2;
            }

            $winnaar = ($gewonnen_sets_team1 > $gewonnen_sets_team2) ? 1 : 2;

            $totaal_team1 = $this->trim_score($this->set1_1, $this->set1_2) + $this->trim_score($this->set2_1, $this->set2_2) + $this->trim_score($this->set3_1, $this->set3_2);
            $totaal_team2 = $this->trim_score($this->set1_2, $this->set1_1) + $this->trim_score($this->set2_2, $this->set2_1) + $this->trim_score($this->set3_2, $this->set3_1);

            if ($winnaar == 1) {
                $getrimd_totaal_winnende_team = $totaal_team1;
                $getrimd_totaal_verliezende_team = $totaal_team2;
                $totaal_winnende_team = $this->set1_1 + $this->set2_1 + $this->set3_1;
                $totaal_verliezende_team = $this->set1_2 + $this->set2_2 + $this->set3_2;
                $id_winnaars = array($this->team1_speler1, $this->team1_speler2);
                $id_verliezers = array($this->team2_speler1, $this->team2_speler2);
            } else {
                $getrimd_totaal_winnende_team = $totaal_team2;
                $getrimd_totaal_verliezende_team = $totaal_team1;
                $totaal_winnende_team = $this->set1_2 + $this->set2_2 + $this->set3_2;
                $totaal_verliezende_team = $this->set1_1 + $this->set2_1 + $this->set3_1;
                $id_winnaars = array($this->team2_speler1, $this->team2_speler2);
                $id_verliezers = array($this->team1_speler1, $this->team1_speler2);
            }

            $return = array(
                "winnaar" => $winnaar,
                "aantal_sets" => $aantal_sets_gespeeld,
                "totaal_punten_winnaars" => $totaal_winnende_team,
                "totaal_punten_verliezers" => $totaal_verliezende_team,
                "gemiddelde_punten_winnaars" => $getrimd_totaal_winnende_team / $aantal_sets_gespeeld,
                "gemiddelde_punten_verliezers" => $getrimd_totaal_verliezende_team / $aantal_sets_gespeeld,
                "id_winnaars" => $id_winnaars,
                "id_verliezers" => $id_verliezers,
                "aantal_punten" => $totaal_verliezende_team + $totaal_winnende_team
            );
            return $return;
        }

        private function trim_score($score1, $score2)
        {
            return ($score1 > 21 || $score2 > 21) ? 21 / max($score1, $score2) * $score1 : $score1;
        }

    }

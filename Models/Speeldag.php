<?php

    require_once(__DIR__ . '/../connect.php');
    require_once(__DIR__ . '/../Interfaces/ISpeeldag.php');
    require_once(__DIR__ . '/Wedstrijd.php');

    class Speeldag implements ISpeeldag
    {
        public $id;
        public $seizoen_id;
        public $speeldagnummer;
        public $gemiddeld_verliezend;
        public $datum;
        public $is_berekend;

        function __construct()
        {
            $this->db = new ConnectionSettings();
            $this->db->connect();
        }



        /**
         * Voegt een nieuwe speeldag toe aan de database.
         * @param $data
         * @return false als het mislukt is OF als datum reeds bestaat
         */
        public function voeg_toe($data)
        {
            $seizoen = new Seizoen();
            $seizoen->get_huidig_seizoen();

            $result = mysql_query(sprintf("SELECT * FROM intra_speeldagen WHERE datum = '%s'", mysql_real_escape_string($data['datum'])));
            $num_rows = mysql_num_rows($result);

            if ($num_rows == 0) {
                $query = sprintf("
                    INSERT INTO
                        intra_speeldagen
                    SET
                      speeldagnummer = '%s',
                      seizoen_id = '%s',
                      datum = '%s',
                      gemiddeld_verliezend = 0,
                      is_berekend = 0
                      ",
                            mysql_real_escape_string($data['speeldagnummer']),
                            mysql_real_escape_string($seizoen->id),
                            mysql_real_escape_string($data['datum']));
                return mysql_query($query);
            }


            return FALSE;

        }

        /**
         * Private functie om speeldag op te halen.
         * @param $speeldag_id
         */
        public function get($speeldag_id)
        {
            $query = sprintf("SELECT * FROM intra_speeldagen WHERE id= '%s';", mysql_real_escape_string($speeldag_id));
            $resultaat = mysql_query($query);
            if($resultaat != FALSE)
            {
                $this->vulop(mysql_fetch_assoc($resultaat));
            }
        }


        /**
         * Get alle wedstrijden uit de db en return een lijst van wedstrijden.
         * @return Wedstrijd[]
         */
        public function get_wedstrijden()
        {
            $query = sprintf("SELECT * FROM intra_wedstrijden WHERE speeldag_id= '%s' ORDER BY id ASC;", mysql_real_escape_string($this->id));
            $resultaat = mysql_query($query);
            $wedstrijden = array();
            while ($array_uitslagen = mysql_fetch_array($resultaat)) {
                $wedstrijd = new Wedstrijd();
                $wedstrijd->vulop($array_uitslagen);
                $wedstrijden[] = $wedstrijd;
            }
            return $wedstrijden;
        }

        public function get_laatste_speeldag($seizoen_id = null)
        {
            if($seizoen_id == null)
            {
                $seizoen = new Seizoen();
                $seizoen->get_huidig_seizoen();
                $seizoen_id = $seizoen->id;
            }
            $resultaat = mysql_query(sprintf("SELECT * FROM intra_speeldagen WHERE seizoen_id = '%s' ORDER BY speeldagnummer DESC LIMIT 1;",$seizoen_id));
            $array_speeldag = mysql_fetch_array($resultaat);
            $this->vulop($array_speeldag);
        }

        public function get_laatste_berekende_speeldag($seizoen_id = null)
        {
            if($seizoen_id == null)
            {
                $seizoen = new Seizoen();
                $seizoen->get_huidig_seizoen();
                $seizoen_id = $seizoen->id;
            }
            $resultaat = mysql_query(sprintf("SELECT * FROM intra_speeldagen WHERE seizoen_id = '%s' AND is_berekend = 1 ORDER BY speeldagnummer DESC LIMIT 1;",$seizoen_id));
            $array_speeldag = mysql_fetch_array($resultaat);
            $this->vulop($array_speeldag);
        }
        /**
         * Om het Speeldag object keurig op te vullen
         * @param $data
         */
        public function vulop($data)
        {
            $this->id = $data['id'];
            $this->gemiddeld_verliezend = $data['gemiddeld_verliezend'];
            $this->speeldagnummer = $data['speeldagnummer'];
            $this->datum = $data['datum'];
            $this->seizoen_id = $data["seizoen_id"];
            $this->is_berekend = $data["is_berekend"];
        }

        /**
         * Update de huidige speeldag
         * Alles kan worden aangepast, behalve seizoen én id
         * @return bool true indien gelukt
         */
        public function update()
        {
            $query = sprintf("
            UPDATE intra_speeldagen
            SET gemiddeld_verliezend = '%s',speeldagnummer = '%s', datum = '%s', is_berekend = 1
            WHERE id = '%s';
        ",
                mysql_real_escape_string($this->gemiddeld_verliezend),
                mysql_real_escape_string($this->speeldagnummer),
                mysql_real_escape_string($this->datum),
                mysql_real_escape_string($this->id));

            return mysql_query($query);
        }
        public function update_gemiddeldverliezend()
        {
            $query = sprintf("
            UPDATE intra_speeldagen
            SET gemiddeld_verliezend = '%s', is_berekend = 1
            WHERE id = '%s';
        ",
                mysql_real_escape_string($this->gemiddeld_verliezend),
                mysql_real_escape_string($this->id));

            echo "$query <br/>";
            return mysql_query($query);
        }
    }

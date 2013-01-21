<?php
    include('../connect.php');
    include('../Interfaces/ISpeeldag.php');
    include('Wedstrijd.php');

    class Speeldag implements ISpeeldag
    {
        public $id;
        public $speeldagnummer;
        public $gemiddeld_verliezend;
        public $datum;

        function __construct()
        {
            $this->db = new ConnectionSettings();
            $this->db->connect();
        }

        /**
         * Creatie ingevuld speeldagobject
         * @static
         * @param $speeldag_id
         * @return Speeldag
         */
        static function metId($speeldag_id)
        {
            $instance = new self();
            $instance->get($speeldag_id);
            return $instance;
        }

        /**
         * Creatie van laatste speeldag
         * @static
         * @return Speeldag
         */
        static function laatsteSpeeldag()
        {
            $instance = new self();
            $instance->get_laatste_speeldag();
            return $instance;
        }

        /**
         * Voegt een nieuwe speeldag toe aan de database.
         * @param $data
         */
        function voeg_toe($data)
        {
            $query = sprintf("
            INSERT INTO
                intra_speeldagen
            SET
              speeldagnummer = '%s',
              seizoen_id = '%s',
              datum = '%s',
              gemiddeld_verliezend = 0,
              ",
                mysql_real_escape_string($data['speeldagnummer']),
                mysql_real_escape_string($data['seizoen_id']),
                mysql_real_escape_string($data['datum']));

            $result = mysql_query($query);
        }

        /**
         * Private functie om speeldag op te halen.
         * @param $speeldag_id
         */
        private function get($speeldag_id)
        {
            $query = sprintf("SELECT * FROM intra_speeldagen WHERE id= '%s';", $speeldag_id);
            $resultaat = mysql_query($query);
            $this->vulop($resultaat);
        }


        /**
         * Get alle wedstrijden uit de db en return een lijst van wedstrijden.
         * @return Wedstrijd[]
         */
        function get_wedstrijden()
        {
            $query = sprintf("SELECT * FROM intra_wedstrijden WHERE speeldag_id= '%s';", mysql_real_escape_string($this->id));
            $resultaat = mysql_query($query);
            $wedstrijden = array();
            while ($array_uitslagen = mysql_fetch_array($resultaat)) {
                $wedstrijd = new Wedstrijd();
                $wedstrijd->vulop($array_uitslagen);
                $wedstrijden[] = $wedstrijd;
            }
            return $wedstrijden;
        }

        private function get_laatste_speeldag()
        {
            $resultaat = mysql_query("SELECT * FROM intra_speeldagen ORDER BY id DESC LIMIT 1;");
            return mysql_fetch_assoc($resultaat);
        }

        /**
         * Om het Speeldag object keurig op te vullen
         * @param $data
         */
        function vulop($data)
        {
            $this->id = $data['id'];
            $this->gemiddeld_verliezend = $data['gemiddeld_verliezend'];
            $this->speeldagnummer = $data['speeldagnummer'];
            $this->datum = $data['datum'];
        }

        /**
         * Update de huidige speeldag
         * Alles kan worden aangepast, behalve seizoen én id
         * @return bool true indien gelukt
         */
        function update()
        {
            $query = sprintf("
            UPDATE intra_speeldagen
            set gemiddeld_verliezend = '%s' and  speeldagnummer = '%s' and datum = '%s'
            WHERE id = '%s';
        ",
                mysql_real_escape_string($this->gemiddeld_verliezend),
                mysql_real_escape_string($this->speeldagnummer),
                mysql_real_escape_string($this->datum),
                mysql_real_escape_string($this->id));
            return mysql_query($query);
        }

    }

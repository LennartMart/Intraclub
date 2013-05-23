<?php
    /**
     * User: Lennart
     * Date: 15-1-13
     */
    require_once(__DIR__ . '/../connect.php');
    require_once(__DIR__ . '/../Interfaces/ISpelers.php');
    require_once(__DIR__ . '/Speler.php');

    class Spelers implements ISpelers
    {
        function __construct()
        {
            $this->db = new ConnectionSettings();
            $this->db->connect();
        }

        public function get_spelers($is_lid)
        {
            if ($is_lid == true) {
                $query = "SELECT * from intra_spelers where is_lid = $is_lid;";
            } else {
                $query = "SELECT * from intra_spelers";
            }

            $resultaat = mysql_query($query);
            $spelers = array();
            while ($array_spelers = mysql_fetch_array($resultaat)) {
                $speler = new Speler();
                $speler->vulop($array_spelers);
                $spelers[] = $speler;
            }
            return $spelers;
        }

        public function get_gemiddelde_allespelers($seizoen_id)
        {
            $query = sprintf("SELECT AVG(huidige_punten) as gemiddelde_alle from intra_spelerperseizoen where seizoen_id = '%s';", mysql_real_escape_string($seizoen_id));
            $resultaat = mysql_query($query);
            return @mysql_result($resultaat, 0, gemiddelde_alle);
        }

        public function get_klassementen()
        {
            $query = mysql_query("SELECT Naam,Leeftijd FROM Namen");

        }
    }
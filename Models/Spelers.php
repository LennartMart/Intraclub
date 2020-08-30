<?php
    ('_JEXEC') or die;
    /**
     * User: Lennart
     * Date: 15-1-13
     */
    require_once(__DIR__ . '/../connect.php');
    require_once(__DIR__ . '/../Interfaces/ISpelers.php');
    require_once(__DIR__ . '/Speler.php');
    require_once(__DIR__ . '/Seizoen.php');

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
                $query = "SELECT * FROM intra_spelers WHERE is_lid = $is_lid ORDER BY voornaam, naam;";
            } else {
                $query = "SELECT * FROM intra_spelers ORDER BY voornaam, naam";
            }

            $resultaat = mysqli_query($query);
            $spelers = array();
            while ($array_spelers = mysqli_fetch_array($resultaat)) {
                $speler = new Speler();
                $speler->vulop($array_spelers);
                $spelers[] = $speler;
            }
            return $spelers;
        }

        //Index = id speler
        public function get_spelers_associative_array($is_lid){
            if ($is_lid == true) {
                $query = "SELECT * FROM intra_spelers WHERE is_lid = $is_lid ORDER BY voornaam, naam;";
            } else {
                $query = "SELECT * FROM intra_spelers ORDER BY voornaam, naam";
            }

            $resultaat = mysqli_query($query);
            $spelers = array();
            while ($array_spelers = mysqli_fetch_array($resultaat)) {
                $speler = new Speler();
                $speler->vulop($array_spelers);
                $spelers[$speler->id] = $speler;
            }
            return $spelers;
        }

        public function get_gemiddelde_allespelers($seizoen_id = null)
        {
            if($seizoen_id == null)
            {
                $seizoen = new Seizoen();
                $seizoen->get_huidig_seizoen();
                $seizoen_id = $seizoen->id;
            }
            $query = sprintf("SELECT AVG(basispunten) AS gemiddelde_alle FROM intra_spelerperseizoen WHERE seizoen_id = '%s';", mysqli_real_escape_string($seizoen_id));
            $resultaat = mysqli_query($query);
            $gemiddelde_alle =  mysqli_num_rows($resultaat);
            if($gemiddelde_alle == 0)
            {
                return 16;
            }
            else
            {
                return $gemiddelde_alle;
            }
        }

        public function get_klassementen()
        {
            $enums = array();
            $result=mysqli_query('SHOW COLUMNS FROM intra_spelers WHERE field=\'klassement\'');
            while ($row=mysqli_fetch_row($result))
            {
                foreach(explode("','",substr($row[1],6,-2)) as $v)
                {
                    $enums[] = $v;
                }
            }
            return $enums;
        }
    }
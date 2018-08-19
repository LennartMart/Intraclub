<?php
    require_once (__DIR__ . '/../connect.php');
    require_once (__DIR__ . '/../Model/Player.php');
    class PlayerRepository
    {
    
        function __construct()
        {
            $this->db = new ConnectionSettings();
            $this->db->connect();
        }
    
        function __destruct()
        {
            $this->db->close();
        }

        public function getAll(){
            $select_query = "SELECT * FROM intra_spelers ORDER BY voornaam, naam;";
            return $this->getAllQuery($select_query);
        }
        public function getAllMembers(){
            $select_query = "SELECT * FROM intra_spelers WHERE is_lid = true ORDER BY voornaam, naam;";
            return $this->getAllQuery($select_query);
        }

        public function getRankings()
        {
            $enums = array();
            $result = $this->db->mysqli->query('SHOW COLUMNS FROM intra_spelers WHERE field=\'klassement\'');
            while ($row = $result->fetch_row())
            {
                foreach(explode("','",substr($row[1],6,-2)) as $v)
                {
                    $enums[] = $v;
                }
            }
            return $enums;
        }

        private function getAllQuery($query){
            $result = $this->db->mysqli->query($query);
            $players = [];
            while($row = $result->fetch_array())
            {
                $player = new Player();
                $player->fill($row);
                $players[$player->id] = $player;
            }
            return $players; 
        }
    }
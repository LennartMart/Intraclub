<?php
    require_once (__DIR__ . '/../connect.php');
    require_once (__DIR__ . '/../Model/Season.php');
    class SeasonRepository
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
    
        function getById($id){
            $query = sprintf("SELECT from intra_seizoenen WHERE id = '%s';", 
                $this->db->mysqli->real_escape_string($id));
            $result = $this->db->mysqli->query($query);
            if ($result) {
                // fetch the result row.
                $data = $result->fetch_assoc();
                $season = new Season();
                $season->id = $data["id"];
                $season->seizoen = $data["seizoen"];
                return $season;
            }
            return new Season();
        }

        public function getAll(){
            $select_query = "SELECT *
                FROM intra_seizoen";
            $result = $this->db->mysqli->query($select_query);
            $seasons = [];
            while($row = $result->fetch_array())
            {
                $season = new Season();
                $season->id = $row["id"];
                $season->seizoen = $row["seizoen"];
                $seasons[$season->id] = $season;
            }
            return $seasons;
        }

        public function getCurrentSeason(){
            $query = "SELECT id, seizoen FROM intra_seizoen ORDER BY id DESC LIMIT 1;";
            $result = $this->db->mysqli->query($query);
            if ($result) {
                // fetch the result row.
                $data = $result->fetch_assoc();
                $season = new Season();
                $season->id = $data["id"];
                $season->seizoen = $data["seizoen"];
                return $season;
            }
            return new Season();
        }

        public function insert($season){
            $insert_query = sprintf("INSERT INTO intra_seizoen
                SET
                    seizoen = '%s';",
                $this->db->mysqli->real_escape_string($season));
            if( $this->db->mysqli->query($insert_query) === TRUE) {
                return true;
            } else {
                return false;
            }
        }
    }


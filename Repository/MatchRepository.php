<?php
    require_once (__DIR__ . '/../connect.php');
    require_once (__DIR__ . '/../Model/Match.php');
    class MatchRepository
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

        public function getById($id)
        {

            $query = sprintf("SELECT * FROM intra_wedstrijden IW WHERE id = '%s';", 
            $this->db->mysqli->real_escape_string($id));
            $result = $this->db->mysqli->query($query);
            if ($result) {
                // fetch the result row.
                $data = $result->fetch_assoc();
                $match = new Match();
                $match->fill($data);
                return $match;
            }
            return new Match();
        }
    }
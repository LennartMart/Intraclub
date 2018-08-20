<?php
    require_once (__DIR__ . '/../connect.php');
    require_once (__DIR__ . '/../Model/Round.php');
    class RoundRepository
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
            $query = sprintf("SELECT * FROM intra_speeldagen WHERE id= '%s';", $this->db->mysqli->real_escape_string($speeldag_id));
            return $this->getAndFill($query);
        }
        public function getLatestRoundOfSeason($seasonId)
        {
            $query = sprintf("SELECT * FROM intra_speeldagen WHERE seizoen_id = '%s' ORDER BY speeldagnummer DESC LIMIT 1;", 
                $this->db->mysqli->real_escape_string($seasonId));
            return $this->getAndFill($query);
        }
        public function getLatestCalculatedRoundOfSeason($seasonId)
        {
            $query = sprintf("SELECT * FROM intra_speeldagen WHERE seizoen_id = '%s' AND is_berekend = 1 ORDER BY speeldagnummer DESC LIMIT 1;", 
                $this->db->mysqli->real_escape_string($seasonId));
            return $this->getAndFill($query);
        }

        public function getBySeason($seasonId){
            $query = sprintf("SELECT * from intra_speeldagen WHERE seizoen_id = '%s' ORDER BY id  ASC;", 
                $this->db->mysqli->real_escape_string($seasonId));
            $resultaat = mysql_query($query);
            $rounds = array();
            while($row = $result->fetch_array())
            {
                $round = new Round();
                $maroundtch->fill($row);
                $rounds[$round->id] = $round;
            }
            return $rounds;
        }

        public function updateAverageLosing($roundId, $averageLosing)
        {
            $query = sprintf("
                UPDATE intra_speeldagen
                SET gemiddeld_verliezend = '%s', is_berekend = 1
                WHERE id = '%s';
                ",
                $this->db->mysqli->real_escape_string($averageLosing),
                $this->db->mysqli->real_escape_string($roundId));

            return $this->db->mysqli->query($query);
        }

        public function update($id, $date, $roundNumber, $averageLosing)
        {
            $query = sprintf("
                UPDATE intra_speeldagen
                SET gemiddeld_verliezend = '%s',speeldagnummer = '%s', datum = '%s', is_berekend = 1
                WHERE id = '%s';
                ",
                $this->db->mysqli->real_escape_string($averageLosing),
                $this->db->mysqli->real_escape_string($roundNumber),
                $this->db->mysqli->real_escape_string($date),
                $this->db->mysqli->real_escape_string($id));

            return $this->db->mysqli->query($query);
        }

        public function roundExists($date){
            $result = mysql_query(sprintf("SELECT * FROM intra_speeldagen WHERE datum = '%s'",
                $this->db->mysqli->real_escape_string($date)));
            return $result->num_rows > 0;
        }


        public function create($roundNumber, $seasonId, $date)
        {
            //ToDO: Add Validator! See RoundExists

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
                        $this->db->mysqli->real_escape_string($roundNumber),
                        $this->db->mysqli->real_escape_string($seasonId),
                        $this->db->mysqli->real_escape_string($date));
            return $this->db->mysqli->query($query);
        }

        private function getAndFill($query){
            $result = $this->db->mysqli->query($query);
            if ($result) {
                // fetch the result row.
                $data = $result->fetch_assoc();
                $round = new Round();
                $round->fill($data);
                return $round;
            }
            return new Round();
        }        
    }
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

        public function delete($id)
        {
            $deleteQuery = sprintf("DELETE FROM intra_wedstrijden WHERE id = '%s'",$this->db->mysqli->real_escape_string($id));

            return $this->db->mysqli->query($deleteQuery);
        }

        public function getMatchesByRound($roundId){
            $query = sprintf("SELECT * FROM intra_wedstrijden WHERE speeldag_id= '%s' ORDER BY id ASC;", $this->db->mysqli->real_escape_string($roundId));
            $resultaat = $this->db->mysqli->query($query);
            $matches = array();
            while($row = $result->fetch_array())
            {
                $match = new Match();
                $match->fill($row);
                $matches[$match->id] = $match;
            }
            return $matches;
        }

        public function getMatchesByPlayerAndSeason($playerId, $seasonId){
            $query = sprintf("SELECT * FROM  intra_wedstrijden iw
            INNER JOIN intra_speeldagen ispeel ON iw.speeldag_id = ispeel.id
                WHERE (
                        (
                        iw.team1_speler1='%s' OR
                        iw.team1_speler2='%s' OR
                        iw.team2_speler1='%s' OR
                        iw.team2_speler2='%s'
                        ) AND ispeel.seizoen_id = '%s'
                    )
                ORDER BY iw.id ASC;",
                $this->db->mysqli->real_escape_string($this->id),
                $this->db->mysqli->real_escape_string($this->id),
                $this->db->mysqli->real_escape_string($this->id),
                $this->db->mysqli->real_escape_string($this->id),
                $this->db->mysqli->real_escape_string($seizoen_id));
            $result = $this->db->mysqli->query($query);

            $matches = array();
            while($row = $result->fetch_array())
            {
                $match = new Match();
                $match->fill($row);
                $matches[$match->id] = $match;
            }

            return $matches;
        }
    }
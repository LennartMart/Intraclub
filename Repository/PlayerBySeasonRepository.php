<?php
    require_once (__DIR__ . '/../connect.php');
    class PlayerBySeasonRepository {
        function __construct()
        {
            $this->db = new ConnectionSettings();
            $this->db->connect();
        }
    
        function __destruct()
        {
            $this->db->close();
        }

        public function getAverageBySeason($seasonId){
            $query = sprintf("SELECT AVG(basispunten) AS gemiddelde_alle FROM intra_spelerperseizoen WHERE seizoen_id = '%s';", 
            $this->db->mysqli->real_escape_string($seasonId));
            $result = $this->db->mysqli->query($query);
            if ($result) {
                // fetch the result row.
                $data = $result->fetch_assoc();
                return $data["gemiddelde_alle"];
            }
            else
            {
                return 16;
            }
        }
        public function getByPlayerAndSeason($playerId, $seasonId)
        {
            $query = sprintf("SELECT * from intra_spelerperseizoen where speler_id = '%s' AND seizoen_id = '%s';",
                        $this->db->mysqli->real_escape_string(playerId),
                        $this->db->mysqli->real_escape_string($seasonId)
                            );
            $result = $this->db->mysqli->query($query);

            //return assoc tabel
            return $result->fetch_assoc();
        }
        public function update($playerId, $seasonId, $playedSets, $wonSets, $playedPoints, $wonPoints)
        {

            $query = sprintf("
            UPDATE
                intra_spelerperseizoen
            SET
                gespeelde_sets = '%s',
                gewonnen_sets = '%s',
                gespeelde_punten= '%s',
                gewonnen_punten = '%s'

            WHERE
                speler_id = '%s' and seizoen_id = '%s';
            ",
                $this->db->mysqli->real_escape_string($playedSets),
                $this->db->mysqli->real_escape_string($wonSets),
                $this->db->mysqli->real_escape_string($playedPoints),
                $this->db->mysqli->real_escape_string($wonPoints),
                $this->db->mysqli->real_escape_string($playerId),
                $this->db->mysqli->real_escape_string($seasonId));

            return $this->db->mysqli->query($query);
        }
    }
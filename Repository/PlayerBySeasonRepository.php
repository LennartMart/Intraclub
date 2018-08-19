<?php
    require_once (__DIR__ . '/../connect.php');
    class PlayerBySeasonRepository {

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
    }
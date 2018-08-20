<?php
    require_once (__DIR__ . '/../connect.php');
    class PlayerByRoundRepository {
        function __construct()
        {
            $this->db = new ConnectionSettings();
            $this->db->connect();
        }
    
        function __destruct()
        {
            $this->db->close();
        }

        public function update($playerId, $roundId, $playerAverage)
        {
            $query = sprintf("
                INSERT INTO
                    intra_spelerperspeeldag
                SET
                    gemiddelde = '%s',
                    speler_id = '%s',
                    speeldag_id = '%s'
                ON DUPLICATE KEY UPDATE
                    gemiddelde = '%s'
                ",
                $this->db->mysqli->real_escape_string($playerAverage),
                $this->db->mysqli->real_escape_string($playerId),
                $this->db->mysqli->real_escape_string($roundId),
                $this->db->mysqli->real_escape_string($playerAverage));

            return $this->db->mysqli->query($query);
        }
        public function getByPlayerAndRound($playerId, $roundId)
        {
            $query = sprintf("SELECT * from intra_spelerperspeeldag where speler_id = '%s' AND speeldag_id = '%s';",
                $this->db->mysqli->real_escape_string($playerId),
                $this->db->mysqli->real_escape_string($roundId)
            );

            $$result = $this->db->mysqli->query($query);
            //return assoc tabel
            return $result->fetch_assoc();
        }
    }
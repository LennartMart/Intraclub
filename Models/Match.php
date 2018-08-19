<?php
    class Match {
        public $id;
        public $roundId;
        public $team1_player1Id;
        public $team1_player2Id;
        public $team2_player1Id;
        public $team2_player2Id;
        public $set1_1;
        public $set1_2;
        public $set2_1;
        public $set2_2;
        public $set3_1;
        public $set3_2;

        public function fill($data)
        {
            $this->id = $data['id'];
            $this->roundId = $data['speeldag_id'];
            $this->team1_player1Id = $data['team1_speler1'];
            $this->team1_player2Id = $data['team1_speler2'];
            $this->team2_player1Id = $data['team2_speler1'];
            $this->team2_player2Id = $data['team2_speler2'];
            $this->set1_1 = $data['set1_1'];
            $this->set1_2 = $data['set1_2'];
            $this->set2_1 = $data['set2_1'];
            $this->set2_2 = $data['set2_2'];
            $this->set3_1 = $data['set3_1'];
            $this->set3_2 = $data['set3_2'];
        }
    }
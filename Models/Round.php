<?php
    class Round {
        public $id;
        public $seasonId;
        public $roundNumber;
        public $averageLosing;
        public $date;
        public $isCalculated;

        public function fill($data)
        {
            $this->id = $data['id'];
            $this->averageLosing = $data['gemiddeld_verliezend'];
            $this->roundNumber = $data['speeldagnummer'];
            $this->date = $data['datum'];
            $this->seasonId = $data["seizoen_id"];
            $this->isCalculated = $data["is_berekend"];
        }
    }
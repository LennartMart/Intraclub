<?php
    class Player
    {
        public $id;
        public $name;
        public $lastName;
        public $gender;
        public $youth;
        public $ranking;
        public $isMember;
        public $isVeteran;

        public function fill($data){
            $this->id = $data["id"];
            $this->name = $data["voornaam"];
            $this->lastName = $data["naam"];
            $this->gender = $data["geslacht"];
            $this->youth = $data["jeugd"];
            $this->ranking = $data["klassement"];
            $this->isMember = $data["is_lid"];
            $this->isVeteran = $data["is_veteraan"];
        }
    }
<?php
    class ConnectionSettings
    {
        private $hostname = '';
        private $username = '';
        private $password = '';
        private $db = '';
        public $mysqli;
        public function connect()
        {
                // establish connection
            
            $this->mysqli = new mysqli($this->hostname, $this->username, $this->password, $this->db);
            if ($this->mysqli->connect_error) {
                throw new Exception('Connect Error (' . $this->mysqli->connect_errno . ') '. $this->mysqli->connect_error);
            }
            $this->mysqli->set_charset("utf8");
        }

        public function close()
        {
            $this->mysqli->close();
        }
    }
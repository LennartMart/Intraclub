<?php
    class ConnectionSettings
    {
        private $hostname = '';
        private $username = '';
        private $password = '';
        private $db = '';
        protected $connLink;

        function connect()
        {

            // establish connection
            if (!$this->connLink = mysqli_connect($this->hostname, $this->username, $this->password)) {
                throw new Exception('Error connecting to MySQL: ' . mysqli_error());
            }

            // select database
            if (!mysqli_select_db($this->connLink, $this->db)) {
                throw new Exception('Error selecting database: ' . mysqli_error());
            }
        }
    }
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
            if (!$this->connLink = mysql_connect($this->hostname, $this->username, $this->password)) {
                throw new Exception('Error connecting to MySQL: ' . mysql_error());
            }

            // select database
            if (!mysql_select_db($this->db, $this->connLink)) {
                throw new Exception('Error selecting database: ' . mysql_error());
            }
        }
    }
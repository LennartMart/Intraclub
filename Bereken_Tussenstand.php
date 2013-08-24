<?php
    /**
     * User: Lennart
     * Date: 24-08-2013
     * Time: 22:03
     */
    include("Models/Seizoen.php");

    $seizoen = new Seizoen();
    $seizoen->bereken_huidig_seizoen();
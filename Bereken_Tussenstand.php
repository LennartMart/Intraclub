<?php
    /**
     * User: Lennart
     * Date: 24-08-2013
     * Time: 22:03
     */
    include("Models/Seizoen.php");

    ('_JEXEC') or die;
    $user =& JFactory::getUser();
    $authorisedGroups = $user->getAuthorisedGroups();
    if(in_array("Super Administrator",$authorisedGroups)){
        die("Onvoldoende rechten !");
    }
    $seizoen = new Seizoen();
    $seizoen->bereken_huidig_seizoen();
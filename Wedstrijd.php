<?php
/**
 * User: Lennart
 * Date: 1/08/12
 * Time: 22:55
 * To change this template use File | Settings | File Templates.
 */

include('connect.php');
class Wedstrijd
{
    function __construct(){
        $this->db = new ConnectionSettings();
        $this->db->connect();
    }
    function voeg_toe($data){

    }

    function get($wedstrijd_id){

    }
    function update($wedstrijd){

    }

}

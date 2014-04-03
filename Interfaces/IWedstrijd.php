<?php
    /**
     * User: Lennart
     * Date: 19-1-13
     * Time: 17:02
     */
    interface IWedstrijd
    {
        function get($wedstrijd_id);
        function voeg_toe($data);
        function vulop($resultaat);
        function update($wedstrijd);
        function bepaal_winnaar();
        function delete($wedstrijd_id);
    }

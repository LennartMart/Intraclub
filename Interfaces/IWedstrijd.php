<?php
    /**
     * User: Lennart
     * Date: 19-1-13
     * Time: 17:02
     */
    interface IWedstrijd
    {
        function voeg_toe($data);
        function vulop($resultaat);
        function update($wedstrijd);
        function bepaal_winnaar();

    }

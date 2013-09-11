<?php
    /**
     * User: Lennart
     * Date: 19-1-13
     * Time: 17:01
     */
    interface ISpelers
    {
        function get_spelers($is_lid);
        function get_spelers_associative_array($is_lid);
        function get_gemiddelde_allespelers($seizoen_id);
        function get_klassementen();
    }

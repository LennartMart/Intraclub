<?php
    /**
     * User: Lennart
     * Date: 19-1-13
     * Time: 16:57
     */
    interface ISpeler
    {
        function create($data);
        function get($speler_id);
        function getRankingHistory($seizoen_id);
        function get_seizoen_stats($seizoen_id);
        function get_speeldagstats($speeldag_id);
        function update_basisinfo($data);
        function update_seizoenstats($data);
        function update_speeldagstats($speeldag_id, $tussenstand_speeldag);
        function get_wedstrijden($seizoen_id);
    }

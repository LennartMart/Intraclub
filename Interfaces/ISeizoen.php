<?php
/**
 * User: Lennart
 * Date: 19-1-13
 */
interface ISeizoen
{
    function create($seizoen);
    function bereken_huidig_seizoen();
    function get_speeldagen($seizoen_id);
}

<?php
/**
 * User: Lennart
 * Date: 19-1-13
 * Time: 16:53
*/
interface ISpeeldag
{
    function voeg_toe($data);
    function get_wedstrijden();
    function vulop($data);
    function update();

}

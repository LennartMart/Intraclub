<?php
/**
 * User: Lennart
 * Date: 19-1-13
 */
interface ISeizoen
{
    public function create($seizoen);
    public function get($seizoen_id);
    public function bereken_huidig_seizoen();
    public function get_speeldagen();
    public function get_seizoenen();
    public function get_huidig_seizoen();
}

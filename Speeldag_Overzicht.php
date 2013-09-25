<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
<?php
/**
 * User: Lennart
 * Date: 11-9-13
 * Time: 23:44
 */
    include("Models/Speeldag.php");
    include("Models/Spelers.php");

    $speeldag_id = $_GET["speeldag"];
    $speeldag = new Speeldag();
    $speeldag->get($speeldag_id);
    if($speeldag->id != $speeldag_id)
    {
        exit("Speeldag bestaat niet!");

    }
    $wedstrijden = $speeldag->get_wedstrijden();

    $spelers = new Spelers();
    $spelerslijst = $spelers->get_spelers_associative_array(false);
    $datum = formatDate($speeldag->datum);
    echo "<h1>Speeldag $speeldag->speeldagnummer</h1>";
    $afgerondVerliezend = round($speeldag->gemiddeld_verliezend,2);
    echo "<table><tbody><tr><th align='left'>Tijdstip:</th><td>$datum</td></tr><tr><th>Gemiddelde voor afwezigen:</th><td>$afgerondVerliezend</td></tr></tbody></table>";
    echo "<table class='table table-striped'>";
    echo "<thead><tr><th style='text-align:right'>Team 1 </th><th style='text-align:left'>Team 2</th><th>Uitslag</th></tr></thead>";

    foreach($wedstrijden as $wedstrijd)
    {
        $team1_speler1 = $spelerslijst[$wedstrijd->team1_speler1];
        $team1_speler2 = $spelerslijst[$wedstrijd->team1_speler2];
        $team2_speler1 = $spelerslijst[$wedstrijd->team2_speler1];
        $team2_speler2 = $spelerslijst[$wedstrijd->team2_speler2];
        echo "<tr>";
        echo "<td style='text-align:right'>";
        echo "$team1_speler1->voornaam $team1_speler1->naam<br/>";
        echo "$team1_speler2->voornaam $team1_speler2->naam</td>";

        echo "<td style='text-align:left'>";
        echo "$team2_speler1->voornaam $team2_speler1->naam<br/>";
        echo "$team2_speler2->voornaam $team2_speler2->naam</td>";

        echo "<td><span class='score'><span>$wedstrijd->set1_1-$wedstrijd->set1_2</span> <span>$wedstrijd->set2_1-$wedstrijd->set2_2</span>";
        if(($wedstrijd->set3_1 != '' && $wedstrijd->set3_2 != '') && ($wedstrijd->set3_1 != 0 && $wedstrijd->set3_2 != 0)){
            echo " <span>$wedstrijd->set3_1-$wedstrijd->set3_2</span>";
        }
        echo "</span></td>";

        echo "</tr>";
    }
    echo "</table>";

    //Nu lijst speeldagen ...
    $seizoen = new Seizoen();
    $seizoen->get($speeldag->seizoen_id);
    $speeldagen = $seizoen->get_speeldagen();
    echo "<div style='width:250px;margin-left: auto;margin-right: auto'>";
    echo "<select name='speeldag'>";
    foreach($speeldagen as $speeldag)
    {
        /* @var $speeldag Speeldag */
        echo "<option value='".$speeldag->id."'";
        if($speeldag->id == $speeldag_id) { echo "SELECTED"; }
        echo ">" . $speeldag->speeldagnummer . ": ".$speeldag->datum."</option>";
    }
    echo "</select></div>";

    function formatDate($datum)
    {
        return implode('/', array_reverse(explode('-', $datum)));
    }


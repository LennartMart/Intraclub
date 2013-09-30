<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">

<style type="text/css">
    .hover {
        background-color: #ccc !important;
        cursor: pointer;
    }
</style>
<?php
    ('_JEXEC') or die;
/**
 * User: Lennart
 * Date: 30-9-13
 * Time: 20:10
 */

    require_once("Models/Speler.php");
    require_once("Models/Spelers.php");
    require_once("Models/Seizoen.php");
    require_once("Globals.php");

    if(!isset($_GET["speler_id"]))
    {
        die("Geen speler");
    }

    $speler_id = $_GET["speler_id"];
    $speler = new Speler();
    $speler->get($speler_id);
    if($speler->id == '')
    {
        die("Speler niet gevonden");
    }
    $wedstrijden = $speler->get_wedstrijden();

    $seizoen = new Seizoen();
    $speeldagen = $seizoen->get_speeldagen();

    $spelers = new Spelers();
    $spelerslijst = $spelers->get_spelers_associative_array(false);

    echo "<h3>". $spelerslijst[$speler_id]->voornaam . " ". $spelerslijst[$speler_id]->naam  . "</h3>";
    echo "<table class='table table-striped'>";
    echo "<thead><tr><th>Speeldag</th><th style='text-align:right'>Team 1 </th><th style='text-align:left'>Team 2</th><th>Uitslag</th></tr></thead>";
    foreach($wedstrijden as $wedstrijd)
    {
        $team1_speler1 = $spelerslijst[$wedstrijd->team1_speler1];
        $team1_speler2 = $spelerslijst[$wedstrijd->team1_speler2];
        $team2_speler1 = $spelerslijst[$wedstrijd->team2_speler1];
        $team2_speler2 = $spelerslijst[$wedstrijd->team2_speler2];
        echo "<tr>";
        echo "<td>";
        echo "<a href='". $speeldag_url . "?speeldag=$wedstrijd->speeldag_id'>";
        echo  $speeldagen[$wedstrijd->speeldag_id]->speeldagnummer;
        echo "</a></td>";
        echo "<td style='text-align:right'>";
        echo "<a href='". $speler_url . "?speler_id=$team1_speler1->id"."'>$team1_speler1->voornaam $team1_speler1->naam</a><br/>";
        echo "<a href='". $speler_url . "?speler_id=$team1_speler2->id"."'>$team1_speler2->voornaam $team1_speler2->naam</a></td>";

        echo "<td style='text-align:left'>";
        echo "<a href='". $speler_url . "?speler_id=$team2_speler1->id"."'>$team2_speler1->voornaam $team2_speler1->naam</a><br/>";
        echo "<a href='". $speler_url . "?speler_id=$team2_speler2->id"."'>$team2_speler2->voornaam $team2_speler2->naam</a></td>";

        echo "<td><span class='score'><span>$wedstrijd->set1_1-$wedstrijd->set1_2</span> <span>$wedstrijd->set2_1-$wedstrijd->set2_2</span>";
        if(($wedstrijd->set3_1 != '' && $wedstrijd->set3_2 != '') && ($wedstrijd->set3_1 != 0 && $wedstrijd->set3_2 != 0)){
            echo " <span>$wedstrijd->set3_1-$wedstrijd->set3_2</span>";
        }
        echo "</span></td>";

        echo "</tr>";
    }
    echo "</table>";

<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">

<style type="text/css">
    .hover {
        background-color: #ccc !important;
        cursor: pointer;
    }
</style>
<script type="text/javascript">
    jQuery(document).ready(function() {

        jQuery( "tr.selectable" ).hover(
            function() {
                jQuery(this).children('td').each(function() {
                    jQuery(this).addClass("hover");
                });
            }, function() {
                jQuery(this).children('td').each(function() {
                    jQuery(this).removeClass("hover");
                });
            }
        );
        jQuery('tr.selectable').click(function() {
            window.location = jQuery(this).attr('href');
            return false;
        });

    });
</script>
<?php
    ('_JEXEC') or die;
/**
 * User: Lennart
 * Date: 11-9-13
 * Time: 23:44
 */
    require_once("Models/Speeldag.php");
    require_once("Models/Spelers.php");
    require_once("Globals.php");

    $speeldag_id = $_GET["speeldag"];
    $speeldag = new Speeldag();
    $speeldag->get($speeldag_id);
    if($speeldag->id != $speeldag_id)
    {
        exit("Speeldag bestaat niet!");

    }
    $wedstrijden = $speeldag->get_wedstrijden();
?>
<div id="tabs">
    <ul>
        <li><a href="#fragment-1"><span>Speeldag</span></a></li>
        <li><a href="#fragment-2"><span>Speeldagen</span></a></li>
    </ul>
    <div id="fragment-1">
<?php
    $spelers = new Spelers();
    $spelerslijst = $spelers->get_spelers_associative_array(false);
    $datum = formatDate($speeldag->datum);
    echo "<h3>Speeldag $speeldag->speeldagnummer</h3>";
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
?>
    </div>
    <div id="fragment-2">

        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Datum</th>
                <th>Gemiddelde voor afwezigen</th>
            </tr>
            </thead>
            <?php
                $seizoen = new Seizoen();
                $seizoen->get_huidig_seizoen();
                $speeldagen = $seizoen->get_speeldagen();
                foreach($speeldagen as $speeldag)
                {
                    $datum = formatDate($speeldag->datum);
                    $afgerondVerliezend = round($speeldag->gemiddeld_verliezend,2);
                    $speeldagnummer= $speeldag->speeldagnummer;
                    echo "<tr class='selectable' href='" . $speeldag_url ."?speeldag=$speeldagnummer'><td>$speeldagnummer</td><td>$datum</td><td>$afgerondVerliezend</td></tr>";
                }

            ?>
        </table>
    </div>
</div>
<script>
    jQuery( "#tabs" ).tabs();
</script>

    <?php
    function formatDate($datum)
    {
        return implode('/', array_reverse(explode('-', $datum)));
    }


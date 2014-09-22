<?php
    ('_JEXEC') or die;
    require_once("Globals.php");
    ?>
<style type="text/css">
    .hover {
        background-color: #ccc !important;
        cursor: pointer;
    }
    ul.tabs{
        margin: 0px;
        padding: 0px;
        list-style: none;
    }

    ul.tabs li{
        background: none;
        color: #222;
        display: inline-block;
        padding: 10px 15px;
        cursor: pointer;
    }

    ul.tabs li.current{
        background: #ededed;
        color: #222;
    }

    .tab-content{
        display: none;
        background: white;
        padding: 15px;
    }

    .tab-content.current{
        display: inherit;

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

        jQuery('ul.tabs li').click(function(){
            var tab_id = jQuery(this).attr('data-tab');

            jQuery('ul.tabs li').removeClass('current');
            jQuery('.tab-content').removeClass('current');

            jQuery(this).addClass('current');
            jQuery("#"+tab_id).addClass('current');
        });

    });
</script>
<div id="container">
    <ul class="tabs">
        <li class="tab-link current" data-tab="tab-1">Algemene Ranking</li>
        <li class="tab-link" data-tab="tab-2">Jeugd Ranking</li>
        <li class="tab-link" data-tab="tab-3">Vrouwen Ranking</li>
        <li class="tab-link" data-tab="tab-4">Speeldagen</li>
    </ul>
    <div id="tab-1" class="tab-content current">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Vorige (verschil)</th>
                <th>Naam</th>
                <th>Punten</th>
                </tr>
            </thead>

<?php

    //Lees $GET waarden uit voor seizoen en eventueel speeldag
    //Geef die door aan de algemene ranking functie - ook voor andere rankings!!
    // Bundelen in één functie - voorlopig ?
    require_once("Models/Ranking.php");

    $ranking = new Ranking();
    $rankings = $ranking->getRanking();
    $vorige_ranking_exists = array_key_exists("vorigeRanking", $rankings);

    for($i = 0; $i < count($rankings["ranking"]); $i++)
    {
        echo "<tr class='selectable' href ='$speler_url" . "speler_id=" . $rankings["ranking"][$i]["speler_id"] ."'>";
        $naam = $rankings["ranking"][$i]["voornaam"]." ".$rankings["ranking"][$i]["naam"];
        $gemiddelde = $rankings["ranking"][$i]['gemiddelde'];
        $positie = $i +1;
        $verschil = $vorige_ranking_exists ? formatNum($rankings["vorigeRanking"][$rankings["ranking"][$i]['speler_id']] - $positie) : 0;
        echo "<td>$positie</td>";
        if($verschil > 0)
        {
            echo "<td style='color:green'>";
        }
        else if($verschil < 0)
        {
            echo "<td style='color:red'>";
        }
        else
        {
            echo "<td>";
        }
        echo "$verschil </td>";
        echo "<td>$naam</td>";
        $afgerondGemiddelde = round($gemiddelde,2);
        echo "<td>$afgerondGemiddelde</td>";
        echo "</tr>";
    }
    echo "</table>";
?>
    </div>
    <div id="tab-2" class="tab-content">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Naam</th>
                    <th>Punten</th>
                </tr>
                </thead>
                <?php
                    $positie = 1;
                    for($i = 0; $i < count($rankings["ranking"]); $i++)
                    {
                        if($rankings["ranking"][$i]["jeugd"] == 1)
                        {
                            echo "<tr class='selectable' href ='$speler_url". "speler_id=" . $rankings["ranking"][$i]["speler_id"] ."'>";
                            $naam = $rankings["ranking"][$i]["voornaam"]." ".$rankings["ranking"][$i]["naam"];
                            $gemiddelde = $rankings["ranking"][$i]['gemiddelde'];
                            $afgerondGemiddelde = round($gemiddelde,2);
                            echo "<td>$positie</td>";
                            echo "<td>$naam</td>";
                            echo "<td>$afgerondGemiddelde</td>";
                            echo "</tr>";
                            $positie++;
                        }
                    }
                    echo "</table>";
                ?>
             </table>
    </div>
    <div id="tab-3" class="tab-content">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Naam</th>
                <th>Punten</th>
            </tr>
            </thead>
            <?php
                $positie = 1;
                for($i = 0; $i < count($rankings["ranking"]); $i++)
                {
                    if($rankings["ranking"][$i]["geslacht"] == "Vrouw")
                    {
                        echo "<tr class='selectable' href ='$speler_url". "speler_id=" . $rankings["ranking"][$i]["speler_id"] ."'>";
                        $naam = $rankings["ranking"][$i]["voornaam"]." ".$rankings["ranking"][$i]["naam"];
                        $gemiddelde = $rankings["ranking"][$i]['gemiddelde'];
                        $afgerondGemiddelde = round($gemiddelde,2);
                        echo "<td>$positie</td>";
                        echo "<td>$naam</td>";
                        echo "<td>$afgerondGemiddelde</td>";
                        echo "</tr>";

                        $positie ++;
                    }
                }
                echo "</table>";
            ?>
        </table>
    </div>
    <div id="tab-4" class="tab-content">
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
                    echo "<tr class='selectable' href='". $speeldag_url. "speeldag=$speeldagnummer'><td>$speeldagnummer</td><td>$datum</td><td>$afgerondVerliezend</td></tr>";
                }

            ?>
        </table>
    </div>
</div>

<?php
    function formatNum($num){
        return sprintf("%+d",$num);
    }
    function formatDate($datum)
    {
        return implode('/', array_reverse(explode('-', $datum)));
    }

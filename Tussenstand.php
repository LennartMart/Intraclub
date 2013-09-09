<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">

<div id="tabs">
    <ul>
        <li><a href="#fragment-1"><span>Algemene Ranking</span></a></li>
        <li><a href="#fragment-2"><span>Jeugd Ranking</span></a></li>
        <li><a href="#fragment-3"><span>Vrouwen Ranking</span></a></li>
    </ul>
    <div id="fragment-1">
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
        echo "<tr>";
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
        echo "<td>$gemiddelde</td>";
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
                            echo "<tr>";
                            $naam = $rankings["ranking"][$i]["voornaam"]." ".$rankings["ranking"][$i]["naam"];
                            $gemiddelde = $rankings["ranking"][$i]['gemiddelde'];
                            echo "<td>$positie</td>";
                            echo "<td>$naam</td>";
                            echo "<td>$gemiddelde</td>";
                            echo "</tr>";
                            $positie++;
                        }
                    }
                    echo "</table>";
                ?>
             </table>
    </div>
    <div id="fragment-3">
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
                        echo "<tr>";
                        $naam = $rankings["ranking"][$i]["voornaam"]." ".$rankings["ranking"][$i]["naam"];
                        $gemiddelde = $rankings["ranking"][$i]['gemiddelde'];

                        echo "<td>$positie</td>";
                        echo "<td>$naam</td>";
                        echo "<td>$gemiddelde</td>";
                        echo "</tr>";

                        $positie ++;
                    }
                }
                echo "</table>";
            ?>
        </table>
    </div>
</div>

<script>
    jQuery( "#tabs" ).tabs();
</script>

<?php
    function formatNum($num){
        return sprintf("%+d",$num);
    }

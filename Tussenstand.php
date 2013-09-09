<?php

    //Lees $GET waarden uit voor seizoen en eventueel speeldag
    //Geef die door aan de algemene ranking functie - ook voor andere rankings!!
    // Bundelen in één functie - voorlopig ?
    require_once("Models/Ranking.php");

    $ranking = new Ranking();
    $rankings = $ranking->getRanking();
    $vorige_ranking_exists = array_key_exists("vorigeRanking", $rankings);
    echo "<table>";
    for($i = 0; $i < count($rankings["ranking"]); $i++)
    {
        echo "<tr>";
        $naam = $rankings["ranking"][$i]["voornaam"]." ".$rankings["ranking"][$i]["naam"];
        $gemiddelde = $rankings["ranking"][$i]['gemiddelde'];
        $positie = $i +1;
        $vorige_positie = $vorige_ranking_exists ? $positie - $rankings["vorigeRanking"][$rankings["ranking"][$i]['speler_id']] : 0;
        echo "<td>$positie</td>";
        echo "<td>$vorige_positie</td>";
        echo "<td>$naam</td>";
        echo "<td>$gemiddelde</td>";
        echo "</tr>";
    }
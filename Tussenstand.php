<?php

    //Lees $GET waarden uit voor seizoen en eventueel speeldag
    //Geef die door aan de algemene ranking functie - ook voor andere rankings!!
    // Bundelen in één functie - voorlopig ?
    require_once("Models/Ranking.php");

    $ranking = new Ranking();
    $spelerslijst = $ranking->getAlgemeneRanking();
    echo "<table>";
    foreach($spelerslijst as $speler)
    {
        echo "<tr>";
        $naam = $speler["voornaam"]." ".$speler["naam"];
        $gemiddelde = $speler['gemiddelde'];
        echo "<td>$naam</td>";
        echo "<td>$gemiddelde</td>";
        echo "</tr>";
    }
<?php

    //Lees $GET waarden uit voor seizoen en eventueel speeldag
    //Geef die door aan de algemene ranking functie - ook voor andere rankings!!
    // Bundelen in één functie - voorlopig ?
    require_once("Models/Ranking.php");

    $ranking = new Ranking();
    $spelerslijst = $ranking->getRanking();
    echo "<table>";
    for($i = 0; $i < count($spelerslijst); $i++)
    {
        echo "<tr>";
        $naam = $spelerslijst[$i]["voornaam"]." ".$spelerslijst[$i]["naam"];
        $gemiddelde = $spelerslijst[$i]['gemiddelde'];
        $positie = $i +1;
        $vorige_positie = array_key_exists('vorige_positie',$spelerslijst[$i]) ?$spelerslijst[$i]['vorige_positie'] : 0;
        echo "<td>$positie</td>";
        echo "<td>$vorige_positie</td>";
        echo "<td>$naam</td>";
        echo "<td>$gemiddelde</td>";
        echo "</tr>";
    }
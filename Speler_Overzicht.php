<script src="components/com_jumi/files/intraclub/js/highcharts.js"></script>
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
    require_once("Models/Ranking.php");
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

    //GET SPELER RANKING
    $historische_punten = $speler->getRankingHistory($seizoen->id);
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
        echo "<a href='". $speeldag_url . "speeldag=$wedstrijd->speeldag_id'>";
        echo  $speeldagen[$wedstrijd->speeldag_id]->speeldagnummer;
        echo "</a></td>";
        echo "<td style='text-align:right'>";
        echo "<a href='". $speler_url . "speler_id=$team1_speler1->id"."'>$team1_speler1->voornaam $team1_speler1->naam</a><br/>";
        echo "<a href='". $speler_url . "speler_id=$team1_speler2->id"."'>$team1_speler2->voornaam $team1_speler2->naam</a></td>";

        echo "<td style='text-align:left'>";
        echo "<a href='". $speler_url . "speler_id=$team2_speler1->id"."'>$team2_speler1->voornaam $team2_speler1->naam</a><br/>";
        echo "<a href='". $speler_url . "speler_id=$team2_speler2->id"."'>$team2_speler2->voornaam $team2_speler2->naam</a></td>";

        echo "<td><span class='score'><span>$wedstrijd->set1_1-$wedstrijd->set1_2</span> <span>$wedstrijd->set2_1-$wedstrijd->set2_2</span>";
        if(($wedstrijd->set3_1 != '' && $wedstrijd->set3_2 != '') && ($wedstrijd->set3_1 != 0 && $wedstrijd->set3_2 != 0)){
            echo " <span>$wedstrijd->set3_1-$wedstrijd->set3_2</span>";
        }
        echo "</span></td>";

        echo "</tr>";
    }
    echo "</table>";

    //Nu de spelerstatistieken ophalen...
    $speler_statistieken = $speler->get_seizoen_stats($seizoen->id);
    ?>
<h4>Statistieken</h4>
<div id="puntenContainer" style="width: 100%; height: 150px; margin: 0 auto"></div>
<div id="setsContainer" style="width: 100%; height: 150px; margin: 0 auto"></div>
<div id="historyPunten" style="width: 100%; height: 150px; margin: 0 auto"></div>

<script type="text/javascript">
    jQuery(function () {
        var puntenChart;
        var setsChart;
        var historyPunten;

        <!--Punten overzicht-->
        jQuery(document).ready(function() {
            puntenChart = new Highcharts.Chart({
                chart: {
                    renderTo: 'puntenContainer',
                    type: 'bar'
                    },
                colors: [
                    '#ccc', '#b44f42'
                    ],

                title: {
                    text: 'Gespeelde punten '
                    },
                xAxis: {
                    categories: ['<?php echo $speler->voornaam ?>']
                    },
                yAxis: {
                    min: 0,
                    minTickInterval: 10,
                    title: {
                        text: ''
                    },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                         }
                        }
                  },

                tooltip: {
                    formatter: function() {
                        return '<b>'+ this.x +'</b><br/>'+
                        this.series.name +': '+ this.y +'<br/>'+
                        'Totaal: '+ this.point.stackTotal;
                    }

                },
                plotOptions: {
                    series: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: true,
                            color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                            }
                        }
                    },
                    series: [{
                        name: 'Verloren punten',
                        data: [<?php echo $speler_statistieken["gespeelde_punten"] - $speler_statistieken["gewonnen_punten"]?>]
                    },
                    {
                        name: 'Gewonnen punten',
                        data: [<?php echo $speler_statistieken["gewonnen_punten"]?>]
                    }]

            });

            <!--Sets overzicht-->
            setsChart = new Highcharts.Chart({
                chart: {
                    renderTo: 'setsContainer',
                    type: 'bar'
                },
                colors: [
                    '#ccc', '#b44f42'
                ],

                title: {
                    text: 'Gespeelde sets '
                },
                xAxis: {
                    categories: ['<?php echo $speler->voornaam ?>']
                },
                yAxis: {
                    min: 0,
                    minTickInterval: 1,
                    title: {
                        text: ''
                    },
                    stackLabels: {
                        enabled: true,
                        style: {
                            fontWeight: 'bold',
                            color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                        }
                    }
                },
                tooltip: {
                    formatter: function() {
                        return '<b>'+ this.x +'</b><br/>'+
                            this.series.name +': '+ this.y +'<br/>'+
                            'Totaal: '+ this.point.stackTotal;
                    }

                },
                plotOptions: {
                    series: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: true,
                            color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                        }
                    }
                },
                series: [{
                    name: 'Verloren sets',
                    data: [<?php echo $speler_statistieken["gespeelde_sets"] - $speler_statistieken["gewonnen_sets"]?>]
                },
                    {
                        name: 'Gewonnen sets',
                        data: [<?php echo $speler_statistieken["gewonnen_sets"]?>]
                    }]

            });
//            historyPunten = new Highcharts.Chart({
//                chart: {
//                    renderTo: 'historyPunten'
//                },
//                title: {
//                    text: 'Geschiedenis punten',
//                    x: -20 //center
//                },
//                xAxis: {
//                    categories: [
<!--                        --><?php
//                            for($categorie_speeldag = 0; $categorie_speeldag <count($historische_punten); $categorie_speeldag ++)
//                            {
//                                echo "$categorie_speeldag, ";
//                            }
//                        ?>
//                    ]
//                },
//                yAxis: {
//                    title: {
//                        text: 'Punten'
//                    },
//                    min: 0,
//                    startOnTick: false,
//                    max: 21
//                },
//                legend: {
//                    layout: 'vertical',
//                    align: 'right',
//                    verticalAlign: 'middle',
//                    borderWidth: 0
//                },
//                series: [{
//                    name: 'Punten',
//                    data: [
<!--                        --><?php
//                            foreach($historische_punten as $historisch_punt)
//                            {
//                                echo $historisch_punt . ',';
//                            }
//                         ?>
//                    ]
//                }]
//            });
        });
    });
</script>

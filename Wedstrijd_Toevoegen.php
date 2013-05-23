<?php
/**
 * User: Lennart
 * Date: 23-5-13
 * Time: 23:00
 */
    require_once("Models/Wedstrijd.php");
    require_once("Models/Speeldag.php");
    require_once("Models/Seizoen.php");
    require_once("Models/Spelers.php");
?>
<h1>Wedstrijd toevoegen</h1>

    
<?php
    function speeldagen()
    {
        $seizoen = new Seizoen();
        $speeldagen = $seizoen->get_speeldagen();
        $laatste_speeldag = new Speeldag();
        $laatste_speeldag->get_laatste_speeldag();

        echo "<select name='speeldag'>";
        foreach($speeldagen as $speeldag)
        {
            /* @var $speeldag Speeldag */
            echo "<option value='".$speeldag->id."'";
            if($speeldag->id == $laatste_speeldag->id) { echo "SELECTED"; }
            echo ">" . $speeldag->speeldagnummer . ": ".$speeldag->datum;
        }
        echo "</select>";
    }

    function spelerslijst($speler_html)
    {
        $spelers = new Spelers();
        $spelers->get_spelers(true);

        echo "<select name='$speler_html'>";
        foreach($spelers as $speler) {
            /* @var $speler Speler */
            echo "<option value='".$speler->id."'>".$speler->voornaam." ".$speler->achternaam;
        }
        echo "</select>";

    }

?>
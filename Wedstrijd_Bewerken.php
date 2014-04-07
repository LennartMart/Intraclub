<?php
/**
 * Created by PhpStorm.
 * User: Lennart
 * Date: 2/04/14
 * Time: 23:33
 */
('_JEXEC') or die;
$user =& JFactory::getUser();
$authorisedViewLevels = $user->getAuthorisedViewLevels();
if(!in_array(5,$authorisedViewLevels)){
    die("Onvoldoende rechten !");
}

require_once("Models/Wedstrijd.php");
require_once("Models/Speeldag.php");
require_once("Models/Seizoen.php");
require_once("Models/Spelers.php");

$speeldag = new Speeldag();
$speeldag->get_laatste_speeldag();

$spelers = new Spelers();
$spelerslijst = $spelers->get_spelers_associative_array(true);

?>
<h2>Wedstrijd bewerken</h2>
<div class='alert alert-warning'>Enkel wedstrijden van de huidige speeldag kunnen aangepast worden!</div>
<!--- Form om wedstrijd te kiezen -->
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method='post'>

<?php

    if(isset($_POST["BewerkWedstrijd"]) && isset($_POST["wedstrijdId"]))
    {
        $wedstrijd = new Wedstrijd();
        $result = $wedstrijd->update(array(
            'id' => $_POST["wedstrijdId"],
            'team1_speler1' => $_POST["team1_speler1"],
            'team1_speler2' => $_POST["team1_speler2"],
            'team2_speler1' => $_POST["team2_speler1"],
            'team2_speler2' => $_POST["team2_speler2"],
            'set1_1' => $_POST["set1_team1"],
            'set1_2' => $_POST["set1_team2"],
            'set2_1' => $_POST["set2_team1"],
            'set2_2' => $_POST["set2_team2"],
            'set3_1' => $_POST["set3_team1"],
            'set3_2' => $_POST["set3_team2"]
        ));
        if($result)
        {
            echo "<div class='alert alert-success'>Wedstrijd succesvol bijgewerkt. De stand moet nog (her)berekend worden.</div>";
        }
    }
    else if(isset($_POST["VerwijderWedstrijd"]) && isset($_POST["wedstrijdId"]))
    {
        $wedstrijd = new Wedstrijd();
        $result = $wedstrijd->delete($_POST["wedstrijdId"]);
        if($result)
        {
            echo "<div class='alert alert-success'>Wedstrijd werd verwijderd.</div>";
            $_POST["wedstrijd"] = '';
        }

    }

wedstrijden_keuzelijst($speeldag, $spelerslijst, $_POST["wedstrijd"]);
echo "<input class='btn' type='submit' value='Kies wedstrijd' name='KiesWedstrijd'>";
?>

<?php
    if(isset($_POST["wedstrijd"]))
    {
        $wedstrijd = new Wedstrijd();

        $bestaande_wedstrijd  = $wedstrijd->get($_POST["wedstrijd"]);
        if(!$bestaande_wedstrijd)
        {
            echo "<div class='alert alert-danger'>Gekozen wedstrijd niet gevonden!</div>";
            exit();
        }

        $team1_speler1 = $spelerslijst[$wedstrijd->team1_speler1];
        $team1_speler2 = $spelerslijst[$wedstrijd->team1_speler2];
        $team2_speler1 = $spelerslijst[$wedstrijd->team2_speler1];
        $team2_speler2 = $spelerslijst[$wedstrijd->team2_speler2];

?>
        <input type="hidden" name="wedstrijdId" value='<?php echo $_POST["wedstrijd"] ?>'>
        <div class="center" style="background-color: #eee">
            <table class=''>
                <tr>
                    <th>Team 1</th>
                    <th></th>
                    <th>Team 2</th>
                    <th COLSPAN='4'>Set 1</th>
                    <th COLSPAN='4'>Set 2</th>
                    <th COLSPAN='3'>Set 3</th>
                </tr>
                <tr>
                    <td align="center">
                        <?php spelerslijst('team1_speler1', $team1_speler1->id); ?> <br>
                        <?php spelerslijst('team1_speler2', $team1_speler2->id); ?>
                    </td>
                    <td>Vs</td>
                    <td align="center">
                        <?php spelerslijst("team2_speler1", $team2_speler1->id); ?> <br>
                        <?php spelerslijst("team2_speler2", $team2_speler2->id); ?>
                    </td>
                    <td><input style='width:30px' type='input' size='1' name="set1_team1" value='<?php echo $wedstrijd->set1_1?>'></td><td>-</td><td><input style='width:30px' type='input' size='1' name="set1_team2"  value ='<?php echo $wedstrijd->set1_2?>'></td><td></td>
                    <td><input style='width:30px' type='input' size='1' name="set2_team1" value='<?php echo $wedstrijd->set2_1?>'></td><td>-</td><td><input style='width:30px' type='input' size='1' name="set2_team2"  value ='<?php echo $wedstrijd->set2_2?>'></td><td></td>
                    <?php if($wedstrijd->set3_1 != 0 || $wedstrijd->set3_2 !=0)
                    {
                      ?>
                        <td><input style='width:30px' type='input' size='1' name="set3_team1" value ='<?php echo $wedstrijd->set3_1 ?>'></td><td>-</td><td><input style='width:30px' type='input' size='1' name="set3_team2"  value ='<?php echo $wedstrijd->set3_2?>'></td>
                    <?php
                    }
                    else
                    {
                    ?>
                        <td><input style='width:30px' type='input' size='1' name="set3_team1"></td><td>-</td><td><input style='width:30px' type='input' size='1' name="set3_team2"></td>
                    <?php
                    }
                    ?>
                </tr>
            </table>
            </div>
    <?php
    }
        if(isset($_POST["wedstrijd"]))
        {

            echo "<input class='btn' type='submit' value='Bewerk wedstrijd' name='BewerkWedstrijd'>";
            echo "&nbsp;<input class='btn btn-danger' type='submit' value='Verwijder wedstrijd' name='VerwijderWedstrijd' onclick='return confirm(\"Deze wedstrijd wordt verwijderd. Bent u het zeker?\")'>";
        }
    ?>

      </form>


<?php

function spelerslijst($speler_html,$speler_id)
{
    $spelers = new Spelers();
    $opgehaalde_spelers = $spelers->get_spelers(true);

    echo "<select name='$speler_html'>";
    foreach($opgehaalde_spelers as $speler) {
        /* @var $speler Speler */
        echo "<option value='".$speler->id."'";
        if($speler->id == $speler_id) echo " selected";
        echo ">".$speler->voornaam." ".$speler->naam."</option>";
    }
    echo "</select>";

}


function wedstrijden_keuzelijst($speeldag, $spelerslijst, $wedstrijd_id)
{
    $wedstrijden = $speeldag->get_wedstrijden();

    echo "<select  name='wedstrijd' style='width: 100%;'>";
    foreach($wedstrijden as $wedstrijd)
    {
        $team1_speler1 = $spelerslijst[$wedstrijd->team1_speler1];
        $team1_speler2 = $spelerslijst[$wedstrijd->team1_speler2];
        $team2_speler1 = $spelerslijst[$wedstrijd->team2_speler1];
        $team2_speler2 = $spelerslijst[$wedstrijd->team2_speler2];

        echo "<option value='".$wedstrijd->wedstrijd_id."'";
        if($wedstrijd_id == $wedstrijd->wedstrijd_id)
        {
            echo " selected";
        }
        echo ">" . $team1_speler1->voornaam . " & " . $team1_speler2->voornaam . " - ". $team2_speler1->voornaam . " & " . $team2_speler2->voornaam . " : "
            . $wedstrijd->set1_1 . "-" . $wedstrijd->set1_2 . " ". $wedstrijd->set2_1 . "-" . $wedstrijd->set2_2;

        if(($wedstrijd->set3_1 != '' && $wedstrijd->set3_2 != '') && ($wedstrijd->set3_1 != 0 && $wedstrijd->set3_2 != 0)){
           echo " ". $wedstrijd->set3_1 . "-" . $wedstrijd->set3_2;

       }
       echo "</option>";
    }
    echo "</select>";


}

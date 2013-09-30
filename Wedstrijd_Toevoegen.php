<?php
    ('_JEXEC') or die;

/**
 * User: Lennart
 * Date: 23-5-13
 * Time: 23:00
 */
    require_once("Models/Wedstrijd.php");
    require_once("Models/Speeldag.php");
    require_once("Models/Seizoen.php");
    require_once("Models/Spelers.php");

    if(isset($_POST["VoegWedstrijdToe"])){
        $speeldag_id = $_POST["speeldag"];
        $team1_speler1 = $_POST["team1_speler1"];
        $team1_speler2 = $_POST["team1_speler2"];
        $team2_speler1 = $_POST["team2_speler1"];
        $team2_speler2 = $_POST["team2_speler2"];
        $set1_team1 = $_POST["set1_team1"];
        $set1_team2 = $_POST["set1_team2"];
        $set2_team1 = $_POST["set2_team1"];
        $set2_team2 = $_POST["set2_team2"];
        $set3_team1 = $_POST["set3_team1"];
        $set3_team2 = $_POST["set3_team2"];

        $errors = Array();

        if($set1_team1 == "" || $set1_team2 == '')
        {
            $errors[] = "Onvolledige score voor set 1!";
        }
        if($set2_team1 == "" || $set2_team2 == '')
        {
            $errors[] = "Onvolledige score voor set 2!";
        }
        if(($set1_team1 >= 21 && $set1_team2 != $set1_team1 -2) && ($set1_team2 >= 21 && $set1_team1 != $set1_team2 -2))
        {
            $errors[] = "Geen duidelijke winnaar voor set 1!";
        }
        if(($set2_team1 >= 21 && $set2_team2 != $set2_team1 -2) && ($set2_team2 >= 21 && $set2_team1 != $set2_team2 -2))
        {
            $errors[] = "Geen duidelijke winnaar voor set 2!";
        }
        if(empty($errors))
        {
            $wedstrijd = new Wedstrijd();
            $result = $wedstrijd->voeg_toe(array(
                'speeldag_id' => $speeldag_id,
                'team1_speler1' => $team1_speler1,
                'team1_speler2' => $team1_speler2,
                'team2_speler1' => $team2_speler1,
                'team2_speler2' => $team2_speler2,
                'set1_1' => $set1_team1,
                'set1_2' => $set1_team2,
                'set2_1' => $set2_team1,
                'set2_2' => $set2_team2,
                'set3_1' => $set3_team1,
                'set3_2' => $set3_team2
                ));

            if(!$result) {
                echo "<div class='alert alert-error'>Wedstrijd werd niet toegevoegd!</div>";

                unset($_POST["VoegWedstrijdToe"]);
            }
            else {
                echo "<div class='alert alert-success'>Wedstrijd succesvol toegevoegd!</div>";
            }
        }
        else
        {
            echo "<div class='alert alert-error'>";
            foreach ($errors as $error)
            {
                echo "<p>" . $error . "</p>";
            }

            echo "</div>";
        }
    }
?>
<h3>Wedstrijd toevoegen</h3>
<div class="hero-unit center">
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method='post'>
        <table class=''>
            <tr>
                <td align="center" colspan="3">
                    Speeldag <br>
                    <?php speeldagen(); ?>
                </td>
            </tr>
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
                    <?php spelerslijst('team1_speler1'); ?> <br>
                    <?php spelerslijst('team1_speler2'); ?>
                </td>
                <td>Vs</td>
                <td align="center">
                    <?php spelerslijst("team2_speler1"); ?> <br>
                    <?php spelerslijst("team2_speler2"); ?>
                </td>
                <td><input style='width:75px' type='input' size='1' name="set1_team1"></td><td>-</td><td><input style='width:75px' type='input' size='1' name="set1_team2" ></td><td></td>
                <td><input style='width:75px' type='input' size='1' name="set2_team1"></td><td>-</td><td><input style='width:75px' type='input' size='1' name="set2_team2" ></td><td></td>
                <td><input style='width:75px' type='input' size='1' name="set3_team1"></td><td>-</td><td><input style='width:75px' type='input' size='1' name="set3_team2" ></td>
            </tr>
        </table>
    </table>

    <input class="btn" type="submit" value="Voeg toe" name="VoegWedstrijdToe">
    </form>
</div>

<?php
    $speeldag = new Speeldag();
    $speeldag->get_laatste_speeldag();
    $speeldagnummer = $speeldag->speeldagnummer;
    function speeldagen()
    {
        $seizoen = new Seizoen();
        $speeldagen = $seizoen->get_speeldagen();
        $laatste_speeldag = new Speeldag();
        $laatste_speeldag->get_laatste_speeldag();

        echo "<select class='input input-medium' name='speeldag'>";
        foreach($speeldagen as $speeldag)
        {
            /* @var $speeldag Speeldag */
            echo "<option value='".$speeldag->id."'";
            if($speeldag->id == $laatste_speeldag->id) { echo "SELECTED"; }
            echo ">" . $speeldag->speeldagnummer . ": ".$speeldag->datum."</option>";
        }
        echo "</select>";
    }

    function spelerslijst($speler_html)
    {
        $spelers = new Spelers();
        $opgehaalde_spelers = $spelers->get_spelers(true);

        echo "<select name='$speler_html'>";
        foreach($opgehaalde_spelers as $speler) {
            /* @var $speler Speler */
            echo "<option value='".$speler->id."'>".$speler->voornaam." ".$speler->naam."</option>";
        }
        echo "</select>";

    }

?>


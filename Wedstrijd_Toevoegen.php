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
        if($set3_team1 == "") $set3_team1=0;
        if($set3_team2 == "") $set3_team2=0;

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
            echo "<h3>Wedstrijd werd niet toegevoegd!</h3>";

            unset($_POST["VoegWedstrijdToe"]);
        }
        else {
            echo "<h3>Wedstrijd succesvol toegevoegd!</h3>";
        }
    }
?>
<h1>Wedstrijd toevoegen</h1>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method='post'>
    <table>
        <tr>
            <td align="center" colspan="3">
                Speeldag <br>
                <?php speeldagen(); ?>
            </td>
        </tr>
        <tr>
            <td align="center">
                Team 1<br>
                <?php spelerslijst('team1_speler1'); ?> <br>
                <?php spelerslijst('team1_speler2'); ?>
            </td>
            <td align="center">
                Set 1 <br>
                <input type="text" size="1" maxlength="2" name="set1_team1"> - <input type="text" size="1" maxlength="2" name="set1_team2"> <br>
                Set 2 <br>
                <input type="text" size="1" maxlength="2" name="set2_team1"> - <input type="text" size="1" maxlength="2" name="set2_team2"> <br>
                Set 3 <br>
                <input type="text" size="1" maxlength="2" name="set3_team1"> - <input type="text" size="1" maxlength="2" name="set3_team2"> <br>
            </td>
            <td align="center">
                Team 2<br>
                <?php spelerslijst("team2_speler1"); ?> <br>
                <?php spelerslijst("team2_speler2"); ?>
            </td>
        </tr>
    </table>
</table>

<input type="submit" value="Voeg toe" name="VoegWedstrijdToe">
</form>

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

        echo "<select name='speeldag'>";
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


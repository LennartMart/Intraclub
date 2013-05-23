<h1>Speeldag toevoegen</h1>
<?php

    include("Models/Speeldag.php");

    $speeldag = new Speeldag();
    $speeldag->get_laatste_speeldag();
    $speeldagnummer = $speeldag->speeldagnummer +1;

    $show_form = true;

    if(isset($_POST["VoegSpeeldagToe"]))
    {

        $dag = $_POST["dag"];
        $maand = $_POST["maand"];
        $jaar = $_POST["jaar"];
        $datum = "$jaar-$maand-$dag";

        $result = $speeldag->voeg_toe(array("speeldagnummer" => $speeldagnummer, "datum" => $datum));
        if(!$result)
        {
            echo "<h3> Ernstige error: ".mysql_error() + " </h3>";
        }
        else
        {
            echo "<h3>Nieuwe speeldag correct toegevoegd!</h3>";
            $show_form = false;
        }
        unset($_POST["VoegSpeeldagToe"]);
    }

    if($show_form)
    {
        date_default_timezone_set('UTC');
        $maand = date('m');
        $dag = date('d');
        $jaar = date('Y');

        ?>


        <form name="formulier" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

            <fieldset>
                <p><label for="speeldag" class='field'>Speeldag: </label><input type="text" size="1" maxlength="2" value="<?php echo $speeldagnummer; ?>" disabled="true" name="speeldag"/> </p>
                <p><label class='field'>Datum: </label> <input type="text" size="2" maxlength="2" name="dag" value="<?php echo $dag; ?>">/<input type="text" size="2" maxlength="2" name="maand" value="<?php echo $maand; ?>">/<input type="text" size="4" maxlength="4" name="jaar" value="<?php echo $jaar; ?>"></p>
            </fieldset>
            <input type="submit" value="Toevoegen" name="VoegSpeeldagToe" onclick="return confirm('Speeldag <?php echo $speeldagnummer ?> wordt toegevoegd. Bent u zeker?')">
        </form>

    <?php

    }

?>
<h3>Speeldag toevoegen</h3>

<?php
    ('_JEXEC') or die;
    $user =& JFactory::getUser();
    $authorisedGroups = $user->getAuthorisedGroups();
    if(in_array("Super Administrator",$authorisedGroups)){
        die("Onvoldoende rechten !");
    }
    include("Models/Speeldag.php");
    include("Models/Seizoen.php");


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
            echo"<div class='alert alert-error'>";
            echo "Speeldag bestaat al! </div>";
        }
        else
        {
            echo "<div class='alert alert-success'>Nieuwe speeldag correct toegevoegd!</div>";
            $show_form = false;
        }
        unset($_POST);

        $speeldag->get_laatste_speeldag();
        $speeldagnummer = $speeldag->speeldagnummer +1;
    }
    if($show_form)
    {
        date_default_timezone_set('UTC');
        $maand = date('m');
        $dag = date('d');
        $jaar = date('Y');

        ?>

        <div class="hero-unit center">
            <form name="formulier" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

                <fieldset>
                    <p><label for="speeldag" class='field'>Speeldag: </label><input type="text" class="input input-small" size="1" maxlength="2" value="<?php echo $speeldagnummer; ?>" disabled="true" name="speeldag"/> </p>
                    <p><label class='field'>Datum: </label> <input class="input input-small" type="text" size="2" maxlength="2" name="dag" value="<?php echo $dag; ?>">/<input class="input input-small" type="text" size="2" maxlength="2" name="maand" value="<?php echo $maand; ?>">/<input class="input input-small" type="text" size="4" maxlength="4" name="jaar" value="<?php echo $jaar; ?>"></p>
                </fieldset>
                <input class = 'btn btn-success' type="submit" value="Toevoegen" name="VoegSpeeldagToe" onclick="return confirm('Speeldag <?php echo $speeldagnummer ?> wordt toegevoegd. Bent u zeker?')">
            </form>
        </div>

    <?php

    }

?>
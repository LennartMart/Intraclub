<?php
    ('_JEXEC') or die;
    $user =& JFactory::getUser();
    $authorisedViewLevels = $user->getAuthorisedViewLevels();
    if(!in_array(5,$authorisedViewLevels)){
        die("Onvoldoende rechten !");
    }
    require_once("Models/Speler.php");
    require_once("Models/Spelers.php");

    $spelers = new Spelers();
    if(isset($_POST["VoegSpelerToe"])) {
        $errors = array();
        $gegevens = array(
            "voornaam" => $_POST["voornaam"],
            "naam" =>  $_POST["naam"],
            "geslacht" => isset($_POST["geslacht"])? $_POST["geslacht"]: '',
            "jeugd" => (isset($_POST["jeugd"])) ? 1 : 0,
            "klassement" => $_POST["klassement"],
            "basispunten" =>  $_POST["basispunten"]);
        if(!strlen($gegevens["voornaam"])) {
            $errors[] = "Een voornaam is verplicht";
        }
        if(!strlen($gegevens["naam"])) {
            $errors[] ="Een achternaam is verplicht";
        }
        if(!strlen($gegevens["geslacht"])) {
            $errors[] = "Kies een geslacht";
        }
        if(!is_numeric($gegevens["basispunten"])) {
            $errors[] = "'Basispunten' moet een getal zijn!";
        }
        //TO DO : Check of speler met dezelfde voor -en achternaam reeds bestaat!

        if(empty($errors)) {
            $nieuwe_speler = new Speler();
            $resultaat = $nieuwe_speler->create($gegevens);
            if(!$resultaat) {
                echo"<div class='alert alert-error'>Speler bestaat al! </div>";
            }
            else {
                echo "<div class='alert alert-success'>".$gegevens["voornaam"]." ".$gegevens["naam"]." succesvol toegevoegd</div>";
            }
        }

        else
        {
            echo"<div class='alert alert-error'>";
            foreach ($errors as $error)
            {
                echo "<p>$error</p>";
            }
            echo "</div>";

        }
    }
?>
    <h2>Voeg een nieuwe speler toe</h2>
<div class="center" style="background-color: #eee">


<form name="VoegSpelerToeForm" action="<?php echo $_SERVER['REQUEST_URI']?>" method="post">
    <table class="table">
        <tr>
            <th align="left">Voornaam:</th>
            <td align="left"><input type="text" maxlength="25" name="voornaam"></td>
        </tr>
        <tr>
            <th align="left">Naam:</th>
            <td align="left"><input type="text" maxlength="25" name="naam"></td>
        </tr>
        <tr>
            <th align="left">Geslacht:</th>
            <td align="left">
                <input type="radio" name="geslacht" value="Man" checked="true">Man
            </td>
        </tr>
                <tr>
                    <th></th>
                    <td align="left"><input type="radio" name="geslacht" value="Vrouw">Vrouw</td>
                </tr>


        <tr>
            <th align="left">Jeugd:</th>
            <td align="left">
                <input type="checkbox" name="jeugd">
            </td>
        </tr>
        <tr>
            <th align="left">Klassement:</th>
            <td align="left">
                <?php klassementen(); ?>
            </td>
        </tr>
        <tr>
            <th align="left">Basispunten:</th>
            <td align="left">
                <input type="number" step="any" min="0" class='input-medium' maxlength="25" name="basispunten" style="height:30px" value="<?php echo $spelers->get_gemiddelde_allespelers(); ?>">
            </td>
        </tr>
    </table>
    <input type="submit" class='btn' value="Toevoegen" name="VoegSpelerToe">
</form>

</div>
<?php

    function klassementen()
    {
        $spelers = new Spelers();
        $klassementen = $spelers->get_klassementen();

        echo "<select class='input-medium' name='klassement'>";
        foreach($klassementen as $klassement)
        {
            echo "<option value='".$klassement."'";
            echo ">" . $klassement . "</option>";
        }
        echo "</select>";
    }
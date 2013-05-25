<?php
    require_once("Models/Speler.php");
    require_once("Models/Spelers.php");

    $spelers = new Spelers();
    if(isset($_POST["VoegSpelerToe"])) {
        $error = array();
        $gegevens = array(
            "voornaam" => $_POST["voornaam"],
            "naam" =>  $_POST["naam"],
            "geslacht" => $_POST["geslacht"],
            "jeugd" => (isset($_POST["jeugd"])) ? 1 : 0,
            "klassement" => $_POST["klassement"],
            "basispunten" =>  $_POST["basispunten"]);
        if(!strlen($gegevens["voornaam"])) {
            $error[] = "Een voornaam is verplicht";
        }
        if(!strlen($gegevens["naam"])) {
            $error[] ="Een achternaam is verplicht";
        }
        if(!strlen($gegevens["geslacht"])) {
            $error[] = "Kies een geslacht";
        }
        if(!is_numeric($gegevens["basispunten"])) {
            $error[] = "'Basispunten' moet een getal zijn!";
        }
        //TO DO : Check of speler met dezelfde voor -en achternaam reeds bestaat!

        if(empty($error)) {
            $nieuwe_speler = new Speler();
            $resultaat = $nieuwe_speler->create($gegevens);
            if(!$resultaat) {
                echo"<p>Probleem met toevoegen aan database. Contacteer beheerder en geef volgende foutboodschap</p>".mysql_error();
            }
            else {
                echo "<h3>".$gegevens["voornaam"]." ".$gegevens["naam"]." succesvol toegevoegd!</h3>";
            }
        }
    }
?>

<h1> Speler toevoegen </h1>

<form name="VoegSpelerToeForm" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
    <table>
        <tr>
            <td>Voornaam:</td>
            <td><input type="text" maxlength="25" name="voornaam"></td>
        </tr>
        <tr>
            <td>Naam:</td>
            <td><input type="text" maxlength="25" name="naam"></td>
        </tr>
        <tr>
            <td>Geslacht</td>
            <td>
                <input type="radio" name="geslacht" value="Man">Man <br>
                <input type="radio" name="geslacht" value="Vrouw">Vrouw
            </td>
        </tr>
        <tr>
            <td>Jeugd</td>
            <td>
                <input type="checkbox" name="jeugd">
            </td>
        </tr>
        <tr>
            <td>
                Klassement
            </td>
            <td>
                <?php klassementen(); ?>
            </td>
        </tr>
        <tr>
            <td>
                Basispunten
            </td>
            <td>
                <input type="number" maxlength="25" name="basispunten" value="<?php echo $spelers->get_gemiddelde_allespelers(); ?>">
            </td>
        </tr>
    </table>
    <input type="submit" value="Toevoegen" name="VoegSpelerToe">
</form>


<?php

    function klassementen()
    {
        $spelers = new Spelers();
        $klassementen = $spelers->get_klassementen();

        echo "<select name='klassement'>";
        foreach($klassementen as $klassement)
        {
            echo "<option value='".$klassement."'";
            echo ">" . $klassement . "</option>";
        }
        echo "</select>";
    }
<h1>Voeg een nieuw seizoen toe</h1>
<?php
    include("Models/Seizoen.php");
    $show_form = true;
    if(isset($_POST["VoegSeizoenToe"]))
    {
        $seizoen1 = strip_tags($_POST['seizoen1']);
        $seizoen2 = strip_tags($_POST['seizoen2']);
        $errors = array();

        if(!strlen($seizoen1) || !strlen($seizoen2)) {
            $errors[] =  "Vul een seizoen in";
        }
        else if(strlen($seizoen1) != 4 || strlen($seizoen2) != 4) {
            $errors[] =  "Vul een seizoen in van het fomaat xxxx";
        }
        else if($seizoen2 != $seizoen1+1 || !ctype_digit($seizoen1)) {
            $errors[] =  "Vul een geldig seizoen in";
        }
        if(empty($errors))
        {
            $seizoen = new Seizoen();
            $newSeizoen = $seizoen1 ." - " . $seizoen2;
            $uitkomst = $seizoen->create($newSeizoen);
            if($uitkomst)
            {
                echo "<h1>Seizoen " + $newSeizoen + " is succesvol toegevoegd!</h1>";
                echo "<p>Alle gegevens van vorig seizoen werden weggeschreven, iedereen heeft nu als basispunt = eindpunt vorig seizoen!</p>";
                unset($_POST["VoegSeizoenToe"]);
                $show_form = false;
            }
            else
            {
                echo "<p> Er trad een uitzonderlijke error op: " + mysql_error() + " </p>";
            }
        }
        else
        {
            echo "<h3>Er zijn enkele fouten opgetreden</h3>";
            echo "<li>";
            foreach ($errors as $error)
            {
                echo "<ul>" + $error + "</ul>";
            }

            echo "</li><br/>";
        }

    }

    if($show_form)
    {
?>


<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <legend>Voer gegevens in voor het volgende seizoen</legend>
    <p><label class='field'> Seizoen: </label><input type="text" id="seizoen1" name="seizoen1" maxlength="4" size="4"> - <input type="text" id="seizoen2" name="seizoen2" maxlength="4" size="4"></p>
    <input class="btn" type="submit" value="Submit" name="VoegSeizoenToe">
</form>

<?php
    }
?>




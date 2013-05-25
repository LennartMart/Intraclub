<h1>Voeg een nieuw seizoen toe</h1>
<?php
    include("Models/Seizoen.php");
    $show_form = true;
    $huidig_seizoen = new Seizoen();
    $huidig_seizoen->get_huidig_seizoen();
    if(isset($_POST["VoegSeizoenToe"]))
    {
        $seizoen1 = strip_tags($_POST['seizoen1']);
        $seizoen2 = strip_tags($_POST['seizoen2']);
        $newSeizoen = $seizoen1 ." - " . $seizoen2;
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
        else if($newSeizoen == $huidig_seizoen->seizoen)
        {
            $errors[] =  "Dit seizoen werd reeds toegevoegd";
        }
        if(empty($errors))
        {
            $seizoen = new Seizoen();

            $uitkomst = $seizoen->create($newSeizoen);
            if($uitkomst)
            {
                echo "<h1>Seizoen " + $newSeizoen + " is succesvol toegevoegd!</h1>";
                echo "<p>Alle gegevens van vorig seizoen werden weggeschreven, iedereen heeft nu als basispunt = eindpunt vorig seizoen!</p>";
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
            echo "<ul>";
            foreach ($errors as $error)
            {
                echo "<li>" . $error . "</li>";
            }

            echo "</ul><br/>";
        }
    }

    if($show_form)
    {

?>


<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <legend>Voer gegevens in voor het volgende seizoen</legend>
    <p><label class='field'> Huidig seizoen: </label><?php echo $huidig_seizoen->seizoen ?></p>
    <p><label class='field'> Seizoen: </label><input type="text" id="seizoen1" name="seizoen1" maxlength="4" size="4"> - <input type="text" id="seizoen2" name="seizoen2" maxlength="4" size="4"></p>
    <input class="btn" type="submit" value="Start nieuw seizoen!" name="VoegSeizoenToe">
</form>

<?php
    }
?>




<!--<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">-->
<div class="hero-unit center">
<?php
    ('_JEXEC') or die;
    $user = JFactory::getUser();
    $authorisedGroups = $user->getAuthorisedGroups();
    if(!in_array(8,$authorisedGroups)){
        die("Onvoldoende rechten !");
    }
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
                echo "<div class='alert alert-success'>";
                echo "<h3>Seizoen $newSeizoen is succesvol toegevoegd!</h3>";
                echo "<p>Alle gegevens van vorig seizoen werden weggeschreven, iedereen heeft nu als basispunt = eindpunt vorig seizoen!</p>";
                echo "</div>";
                $show_form = false;
            }
            else
            {
                echo "<p> Er trad een uitzonderlijke error op: " + mysql_error() + " </p>";
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

    if($show_form)
    {

?>


<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <legend>Voer gegevens in voor het volgende seizoen</legend>
    <p><label class='field'> Huidig seizoen: </label><?php echo $huidig_seizoen->seizoen ?></p>
    <p><label class='field'> Seizoen: </label><input class="input-small" type="text" id="seizoen1" name="seizoen1" maxlength="4" size="4"> - <input class="input-small"type="text" id="seizoen2" name="seizoen2" maxlength="4" size="4"></p>
    <input class="btn btn-danger" type="submit" value="Start nieuw seizoen!" name="VoegSeizoenToe" onclick="return confirm('Bent u zeker dat u het seizoen ' + document.getElementById('seizoen1').value + '-' + document.getElementById('seizoen2').value + ' wilt toevoegen?')">
</form>
</div>

<?php
    }
?>




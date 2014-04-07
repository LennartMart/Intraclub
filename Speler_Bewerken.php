<?php
/**
 * Created by PhpStorm.
 * User: Lennart
 * Date: 7/04/14
 * Time: 21:45
 */
('_JEXEC') or die;
$user =& JFactory::getUser();
$authorisedViewLevels = $user->getAuthorisedViewLevels();
if(!in_array(5,$authorisedViewLevels)){
    die("Onvoldoende rechten !");
}
require_once("Models/Speler.php");
require_once("Models/Spelers.php");

$spelerGekozen = $_POST["kiesSpeler"];


echo "<h2>Speler bewerken</h2>";
if(!empty($_POST['BewerkSpeler']))
{
    $speler = new Speler();
    $gegevens = array(
        "voornaam" => $_POST["voornaam"],
        "naam" =>  $_POST["naam"],
        "geslacht" => isset($_POST["geslacht"])? $_POST["geslacht"]: '',
        "jeugd" => (isset($_POST["jeugd"])) ? 1 : 0,
        "is_lid" => (isset($_POST["is_lid"])) ? 1 : 0,
        "klassement" => $_POST["klassement"],
        "id" =>  $_POST["spelerId"]);

    if($speler->update_basisinfo($gegevens))
    {
        echo "<div class='alert alert-success'>Speler ".$_POST["voornaam"]. " ". $_POST["naam"]." werd succesvol bijgewerkt</div>";
    }
}
if($spelerGekozen == "")
{
    echo "<form action='". $_SERVER['REQUEST_URI'] ."' method='post'>";
    spelerslijst();
    echo "<input class='btn' type='submit' value='Selecteer' name='kiesSpeler'>";
    echo "</form>";
}
else
{
    $speler_id = $_POST["gekozenSpeler"];
    $speler = new Speler();
    $speler->get($speler_id);
?>
<form action="<?php echo $_SERVER['REQUEST_URI']?>" method="post">
    <input type="hidden" name="spelerId" value=<?php echo $speler->id ?>>
    <table class="table">
        <tr>
            <th align="left">Voornaam:</th>
            <td align="left"><input type="text" maxlength="25" name="voornaam" value='<?php echo $speler->voornaam ?>'></td>
        </tr>
        <tr>
            <th align="left">Naam:</th>
            <td align="left"><input type="text" maxlength="25" name="naam" value='<?php echo $speler->naam ?>'></td>
        </tr>
        <tr>
            <th align="left">Geslacht:</th>
            <td align="left">
                <input type="radio" name="geslacht" value="Man" <?php echo ($speler->geslacht=='Man')? 'CHECKED':''?>>Man
            </td>
        </tr>
        <tr>
            <th></th>
            <td align="left"><input type="radio" name="geslacht" value="Vrouw" <?php echo ($speler->geslacht=='Vrouw')?'CHECKED':''?>>Vrouw</td>
        </tr>


        <tr>
            <th align="left">Jeugd:</th>
            <td align="left">
                <input type="checkbox" name="jeugd" <?php echo ($speler->jeugd)?'CHECKED':''?>>
            </td>
        </tr>
        <tr>
            <th align="left">Klassement:</th>
            <td align="left">
                <?php klassementen($speler->klassement); ?>
            </td>
        </tr>
        <tr>
            <th align="left">Lid BC Landegem:</th>
            <td align="left">
                <input type="checkbox" name="is_lid" <?php echo ($speler->is_lid)?'CHECKED':''?>>
            </td>
        </tr>
    </table>
    <input type="submit" class='btn' value="Bewerk" name="BewerkSpeler">
</form>

<?php
}
function spelerslijst()
{
    $spelers = new Spelers();
    $opgehaalde_spelers = $spelers->get_spelers(false);

    echo "<select name='gekozenSpeler'>";
    foreach($opgehaalde_spelers as $speler) {
        /* @var $speler Speler */
        echo "<option value='".$speler->id."'>".$speler->voornaam." ".$speler->naam."</option>";
    }
    echo "</select><br/>";
}
function klassementen($klassement_speler)
{
    $spelers = new Spelers();
    $klassementen = $spelers->get_klassementen();

    echo "<select class='input-medium' name='klassement'>";
    foreach($klassementen as $klassement)
    {
        echo "<option value='".$klassement."'";
        if($klassement_speler == $klassement) echo " selected";
        echo ">" . $klassement . "</option>";
    }
    echo "</select>";
}
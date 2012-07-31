<?php
/**
 * User: Lennart
 * Date: 31/07/12
 * Time: 19:06
 */
defined('_JEXEC') OR defined('_VALID_MOS') OR die( "Direct Access Is Not Allowed" );
include("config.php");

if(isset($_REQUEST["addSeizoen"])) {
    $begin_seizoen = strip_tags($_POST['begin_seizoen']);
    $einde_seizoen = strip_tags($_POST['einde_seizoen']);

    //Seizoenen correct ingevuld?
    if(!strlen($begin_seizoen) || !strlen($einde_seizoen)) {
        $error = "Vul een seizoen in";
    }
    else if(strlen($begin_seizoen) != 4 || strlen($einde_seizoen) != 4) {
        $error = "Vul een seizoen in van het fomaat xxxx";
    }
    else if($einde_seizoen != $begin_seizoen+1 || !ctype_digit($begin_seizoen)) {
        $error = "Vul een geldig seizoen in!";
    }
    if(!$error) {

        $seizoen = $begin_seizoen." - ".$einde_seizoen;

        //Bestaat seizoen al of niet?
        $query = "SELECT count(id) AS aantal FROM intra_seizoen WHERE seizoen='$seizoen'";
        $resultaat = mysql_query($query);
        $aantal = @mysql_result($resultaat, 0, aantal);

        if(!$aantal) {


            //Pak de eindpunten en zet deze als basispunten!
            //Eerst: ID vorige seizoen ophalen
            $resultaat = mysql_query("SELECT seizoen_id FROM intra_seizoen ORDER BY seizoen_id DESC LIMIT 1;");
            $vorige_seizoen = @mysql_result($resultaat, 0, seizoen_id);

            //Tweede: ID laatste speeldag seizoen ophalen
            $resultaat = mysql_query("SELECT speeldag_id FROM intra_speeldagen WHERE seizoen_id = '$vorige_seizoen' ORDER BY speeldag_id DESC LIMIT 1;");
            $laatste_speeldag = @mysql_result($resultaat, 0, speeldag_id);

            //Seizoen bestaat niet -> invullen in database
            $query = "INSERT INTO intra_seizoen SET seizoen='$seizoen'";
            mysql_query($query);
            //Get the last generated ID
            $huidig_seizoen = mysql_insert_id();

            //Haal de eindstand op van elke speler van vorig seizoen
            //En zet het in de tabel spelerperseizoen
            $resultaat = mysql_query("SELECT speler_id, gemiddelde FROM intra_spelerperspeeldag WHERE speeldag_id='$laatste_speeldag';");
            while($rij = mysql_fetch_array($resultaat)) {
                $insert_query = "
                    INSERT INTO
                        intra_spelerperseizoen
                    SET
                        speler_id = '{$rij['speler_id']}',
                        seizoen_id = '$huidig_seizoen',
                        basispunten = '{$rij['gemiddelde']}',
                        gespeelde_sets = 0,
                        gewonnen_sets = 0,
                        gespeelde_punten = 0,
                        gewonnen_punten = 0,
                        aanwezig = 0,
                        verbergen = 0
                        ";

                mysql_query($insert_query) or $error = 1;
            }
            if($error){
                $error = "Mislukt: ".mysql_error();
            }
            else{
                $oke = "Nieuw seizoen aangemaakt!";
            }
        }
        else {
            $error = "Seizoen $seizoen bestaat al<br>";
        }
    }
}
?>


<h1>Seizoen opslaan</h1>
<?php include("error.php"); ?>

<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <table>
        <tr>
            <td>
                Seizoen:
            </td>
            <td>
                <input type="text" id="begin_seizoen" name="begin_seizoen" maxlength="4" size="4"> - <input type="text" id="einde_seizoen" name="einde_seizoen" maxlength="4" size="4">
            </td>
        </tr>
    </table>
    <input type="submit" value="Voeg seizoen toe" name="addSeizoen" onclick="return confirm('Bent u zeker dat u het seizoen ' + document.getElementById('begin_seizoen').value + '-' + document.getElementById('einde_seizoen').value + ' wilt toevoegen?')">
</form>

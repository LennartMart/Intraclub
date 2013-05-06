<?php
    include("Models/Speler.php");
    if(isset($_POST["submit"])) {
        $gegevens = array();
        $gegevens["voornaam"] = $_POST["voornaam"];
        $gegevens["achternaam"]  = $_POST["achternaam"];
        $gegevens["geslacht"]  = $_POST["geslacht"];
        $gegevens["jeugd"]  = (isset($_POST["jeugd"])) ? 1 : 0;
        $gegevens["klassement"]  = $_POST["klassement"];
        if(!strlen($gegevens["voornaam"])) {
            $error .= "Een voornaam is verplicht<br>";
            $err .= 1;
        }
        if(!strlen($gegevens["achternaam"])) {
            $error .= "Een achternaam is verplicht<br>";
            $err .= 2;
        }
        if(!strlen($gegevens["geslacht"])) {
            $error .= "Kies een geslacht<br>";
            $err .= 3;
        }

        if(!$error) {
            $nieuwe_speler = new Speler();
            $resultaat = $nieuwe_speler->create($gegevens);
            if(!$resultaat) {
                $error .= "kon gegevens niet toevoegen aan de database, probeer het later opnieuw<br>".mysql_error();
            }
            else {
                $oke = "Speler" +  $gegevens["voornaam"] +  $gegevens["achternaam"] +" is toegevoegd...";
            }
        }
    }
?>

<h1> Speler toevoegen </h1>

<?php include("error.php") ?>

<form name="formulier" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
    <table>
        <tr>
            <td style="<?=substr_count($err, "1")?"color: red":""?>">Voornaam:</td>
            <td><input type="text" maxlength="25" name="voornaam" value="<?=$_POST["voornaam"]?>"></td>
        </tr>
        <tr>
            <td style="<?=substr_count($err, "2")?"color: red":""?>">Achternaam:</td>
            <td><input type="text" maxlength="25" name="achternaam" value="<?=$_POST["achternaam"]?>"></td>
        </tr>
        <tr>
            <td style="<?=substr_count($err, "3")?"color: red":""?>">Geslacht</td>
            <td>
                <input type="radio" name="geslacht" value="man" <?=($_POST["geslacht"]=="man")?"checked":""?>>man <br>
                <input type="radio" name="geslacht" value="vrouw" <?=($_POST["geslacht"]=="vrouw")?"checked":""?>>vrouw
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
                <select name='klassement'>
                    <?php
                    $query = "SELECT * FROM comp_klassement ORDER BY id ASC";
                    $result = mysql_query($query);
                    while($row = mysql_fetch_array($result)):
                        ?>
                        <option value="<?=$row["id"]?>" <?=($_POST["klassement"]==$row["id"])?"SELECTED":""?>><?=$row["naam"]?></option>
                        <?php endwhile; ?>
                </select>
            </td>
        </tr>
    </table>
    <input type="submit" value="Toevoegen" name="submit">
</form>

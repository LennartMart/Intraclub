<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title></title>
</head>
<body>
<?php
    include("Models/Seizoen.php");

    if(isset($_REQUEST["verder"])) {
        $seizoen1 = strip_tags($_POST['seizoen1']);
        $seizoen2 = strip_tags($_POST['seizoen2']);
        $seizoen = new Seizoen();
        $uitkomst = $seizoen->create($seizoen1 ." - " . $seizoen2);
        echo $uitkomst;
    }

?>


<h1>Voeg een seizoen toe</h1>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <table>
        <tr>
            <td>
                Seizoen:
            </td>
            <td>
                <input type="text" id="seizoen1" name="seizoen1" maxlength="4" size="4"> - <input type="text" id="seizoen2" name="seizoen2" maxlength="4" size="4">
            </td>
        </tr>
    </table>
    <input type="submit" value="Voeg seizoen toe" name="verder" onclick="return confirm('Bent u zeker dat u het seizoen ' + document.getElementById('seizoen1').value + '-' + document.getElementById('seizoen2').value + ' wilt toevoegen?')">

</body>
</html>
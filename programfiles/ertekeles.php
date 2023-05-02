<?php
require_once 'dbconn/conn.php';
require_once "menu.php";

$db = new Database();
$conn = $db -> connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $munkakorny = $_POST['munkakorny'];
    $tavolsag = $_POST['tavolsag'];
    $berezes = $_POST['berezes'];
    $felhid = $_GET["felhid"];
    $id=$_GET["id"];
    $check_query = "SELECT * FROM jelentkezik WHERE felhid = '$felhid'";
    $check_result = oci_parse($conn, $check_query);
    oci_execute($check_result);
    if (oci_fetch($check_result)) {
        $update_query = "UPDATE jelentkezik SET munkakorny='$munkakorny', tavolsag='$tavolsag', berezes='$berezes', allasertekido=sysdate WHERE felhid = '$felhid' and allasid='$id'";
        $update_result = oci_parse($conn, $update_query);
        oci_execute($update_result);
        header("Location: profil.php");
    }
}
oci_close($conn);
?>

<!doctype html>
<html>
<head>

</head>
<body>
<h1 style="text-align: center">Értékelés</h1>
<form method="POST" style="text-align: center; border: black 2px solid; margin: 10px 20% 10px 20%">
    <br><label for="munkakor">Munkakörnyezet:</label>
    <input type="number" name="munkakorny" min="1" max="5" required><br><br>

    <label for="tavolsag">Távolság:</label>
    <input type="number" name="tavolsag" min="1" max="5" required><br><br>

    <label for="berezes">Bérezés:</label>
    <input type="number" name="berezes" min="1" max="5" required><br><br>

    <input type="submit" name="mentes" value="Küldés"><br><br>
</form>
</body>
</html>




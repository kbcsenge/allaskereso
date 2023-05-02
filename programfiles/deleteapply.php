<?php
$conn = "";
require_once "dbconn/conn.php";
$db = new Database();
$conn = $db -> connect();
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET["id"];
    $felhid=$_GET["felhid"];
    $sql1 = "DELETE FROM jelentkezik WHERE jelentkezik.allasid=$id and jelentkezik.felhid=$felhid";
    $run1 = oci_parse($conn, $sql1);
    oci_execute($run1);
    echo $sql1;
    header("Location: profil.php");
}
?>

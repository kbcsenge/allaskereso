<?php
$conn = "";
require_once "dbconn/conn.php";
$db = new Database();
$conn = $db -> connect();
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET["id"];
    $sql1 = "INSERT INTO jelentkezik ()";
    $run1 = oci_parse($conn, $sql1);
    oci_execute($run1);
    $sql2 = "DELETE FROM allas WHERE allas.allasid=$id";
    $run2 = oci_parse($conn, $sql2);
    oci_execute($run2);
    header("Location: sajat.php");
}
?>

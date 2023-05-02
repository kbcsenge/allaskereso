<?php
$conn = "";
require_once "dbconn/conn.php";
$db = new Database();
$conn = $db -> connect();
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET["id"];
    $allasid=$_GET["allasid"];
    $sql1 = "DELETE FROM jelentkezik WHERE jelentkezik.allasid=$allasid and jelentkezik.felhid=$id";
    $run1 = oci_parse($conn, $sql1);
    oci_execute($run1);
    header("Location: jelentkezett.php?id={$_GET["allasid"]}");
}
?>
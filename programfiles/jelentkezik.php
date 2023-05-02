<?php
require_once "dbconn/conn.php";
$db = new Database();
$conn = $db -> connect();
$id=$_GET["id"];
$felhid=$_GET["felhid"];
$query = "SELECT COUNT(*) AS cnt FROM jelentkezik WHERE felhid =:felhid and allasid=:id";
$statement = oci_parse($conn, $query);
oci_bind_by_name($statement, ":felhid", $felhid);
oci_bind_by_name($statement, ":id", $id);
oci_execute($statement);
$result = oci_fetch_assoc($statement);
if ($result['CNT'] > 0) {
    header("Location: hiba.php?&hiba=Már jelentkezett erre az állásra");
} else {
    $query = "INSERT INTO jelentkezik (jelentkezesido,felhid, allasid, munkakorny, tavolsag, berezes, allasertekido) VALUES (sysdate, :felhid,:id,NULL,NULL,NULL,NULL)";
    $statement = oci_parse($conn, $query);
    oci_bind_by_name($statement, ":felhid", $felhid);
    oci_bind_by_name($statement, ":id", $id);
    oci_execute($statement);
    header("Location: profil.php");
}
oci_close($conn);
?>


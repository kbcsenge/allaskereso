<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Állás meghirdetése</title>
    <link rel="stylesheet" href="css/mindenes.css">
</head>
<body>
<?php
require_once 'dbconn/conn.php';
require_once 'menu.php';
$db = new Database();
$conn = $db -> connect();
$allasid = "SELECT max(allasid) FROM allas";
$run = oci_parse($conn, $allasid);
oci_execute($run);
$row = oci_fetch_array($run);
$allasid = $row[0];
$allasid = intval($allasid) + 1;
if (isset($_POST['Meghirdet'])) {
    $name = $_POST["allasnev"];
    $munkaido = $_POST["munkaido"];
    $munkarend = $_POST["munkarend"];
    $tipus = $_POST["tipus"];
    $fizu = $_POST["fizu"];
    $hely = $_POST["hely"];
    $tapasztalat = $_POST["tapasztalat"];
    $leir = $_POST["leir"];
    $hirdetid=$_SESSION['user']['FELHID'];
    $sql = "INSERT INTO allas (allasid,allasnev, leiras, munkaido, fizetes,hely,elvart_munkatapasztalat_ev,meghirdet_id,tipusid,meghirdet_ido,munkarendid)
            VALUES (:allasid,:name, :leir, :munkaido, :fizu, :hely, :tapasztalat, :hirdetid,:tipus,sysdate,:munkarend)";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":allasid", $allasid);
    oci_bind_by_name($stmt, ":munkaido", $munkaido);
    oci_bind_by_name($stmt, ":tipus", $tipus);
    oci_bind_by_name($stmt, ":hely", $hely);
    oci_bind_by_name($stmt, ":tapasztalat", $tapasztalat);
    oci_bind_by_name($stmt, ":leir", $leir);
    oci_bind_by_name($stmt, ":fizu", $fizu);
    oci_bind_by_name($stmt, ":munkarend", $munkarend);
    oci_bind_by_name($stmt, ":hirdetid", $hirdetid);
    oci_execute($stmt);
    header("Location: sajat.php");

}
?>
<form method="post" action="hirdet.php" style='border: #1d60aa 3px solid; margin:50px 20% 50px 20%; padding: 10px 10px 10px 10px'>

    <div class="container">

        <h1>Új állás</h1>
        <hr>
        <label> <b>Állás megnevezése</b></label><br>
        <input style="width: 100%" type="text" name="allasnev" placeholder= "Állás megnevezése" required /><br>
        <label><b>Munkaido</b></label><br>
        <input style="width: 100%" type="text" placeholder="Munkaidő" name="munkaido" required><br>
        <label><b>Munkarend</b></label><br>
        <select id="munkarend" name="munkarend" required>
            <option value="">Válasszon munkarendet</option>
            <option value="tm">Teljes munkaidő</option>
            <option value="rm">Részmunkaidő</option>
            <option value="am">Alkalmi munka</option>
            <option value="vm">Vállalkozói</option>
        </select><br/>
        <label><b>Típus</b></label><br>
        <select id="tipus" name="tipus" required>
            <option value="">Válasszon típust</option>
            <option value="it">Informatika</option>
            <option value="et">Egészségügy</option>
            <option value="mt">Műszaki</option>
            <option value="vt">Vendéglátás</option>
            <option value="jt">Jogi</option>
            <option value="st">Sport</option>
            <option value="ot">Oktatás</option>
            <option value="gt">Gazdasági</option>
            <option value="ft">Fizikai</option>
            <option value="kt">Közigazgatás</option>
            <option value="lt">Logisztika</option>
            <option value="tt">Takarítás</option>
        </select><br/>
        <label><b>Fizetés</b></label><br>
        <input style="width: 100%" type="number" placeholder="Fizetés" name="fizu" min="0" required><br>
        <label><b>Hely</b></label><br>
        <input style="width: 100%" type="text" placeholder="Hely" name="hely" required><br>
        <label><b>Elvárt munkatapasztalat</b></label><br>
        <input style="width: 100%" type="number" placeholder="Elvárt munkatapasztalat" name="tapasztalat" min="0" required><br>
        <label><b>Leírás</b></label><br>
        <input style="width: 100%" type="text" placeholder="Leírás" name="leir" required><br>
    </div>
    <div style="text-align: center; margin-top: 10px">
        <input type="submit" value="Meghirdet" name="Meghirdet">
    </div>
</form>
</body>
</html>

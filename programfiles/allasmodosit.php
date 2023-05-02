<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Állás módosítása</title>
    <link rel="stylesheet" href="css/mindenes.css">
</head>
<body>
<?php
require_once 'dbconn/conn.php';
require_once 'menu.php';
$db = new Database();
$conn = $db -> connect();
if (isset($_POST['Modosit'])) {
    $name = $_POST["allasnev"];
    $munkaido = $_POST["munkaido"];
    $munkarend = $_POST["munkarend"];
    $tipus = $_POST["tipus"];
    $fizu = $_POST["fizu"];
    $hely = $_POST["hely"];
    $tapasztalat = $_POST["tapasztalat"];
    $leir = $_POST["leir"];
    $allasid=$_POST["id"];
    $sql = "UPDATE allas SET allasnev=:name, leiras=:leir, munkaido=:munkaido, fizetes=:fizu,hely=:hely,elvart_munkatapasztalat_ev=:tapasztalat,tipusid=:tipus,munkarendid=:munkarend where allasid=$allasid";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":munkaido", $munkaido);
    oci_bind_by_name($stmt, ":tipus", $tipus);
    oci_bind_by_name($stmt, ":hely", $hely);
    oci_bind_by_name($stmt, ":tapasztalat", $tapasztalat);
    oci_bind_by_name($stmt, ":leir", $leir);
    oci_bind_by_name($stmt, ":fizu", $fizu);
    oci_bind_by_name($stmt, ":munkarend", $munkarend);
    oci_execute($stmt);
    header("Location: sajat.php?id={$allasid}");

}
?>
<form method="post" action="allasmodosit.php" style='border: #1d60aa 3px solid; margin:50px 20% 50px 20%; padding: 10px 10px 10px 10px'>
    <?php
    $id=$_GET["id"];
    $adatok="select allasnev, leiras, munkaido, fizetes, hely, ELVART_MUNKATAPASZTALAT_EV, tipusid, munkarendid from allas where allasid=$id";
    $runadat = oci_parse($conn, $adatok);
    oci_execute($runadat);
    $res = oci_fetch_array($runadat);
    ?>
    <div class="container">

        <h1>Hirdetés módosítása</h1>
        <hr>
        <label> <b>Állás megnevezése</b></label><br>
        <input style="width: 100%" type="text" name="allasnev" placeholder= "Állás megnevezése" value="<?php  echo $res['ALLASNEV']?>" required /><br>
        <label><b>Munkaido</b></label><br>
        <input style="width: 100%" type="text" placeholder="Munkaidő" name="munkaido" value="<?php  echo $res['MUNKAIDO']?>" required><br>
        <label><b>Munkarend</b></label><br>
        <select id="munkarend" name="munkarend" required>
            <option value="">Válasszon munkarendet</option>
            <option value="tm" <?php echo ($res['MUNKARENDID']=='tm')?'selected':''?>>Teljes munkaidő</option>
            <option value="rm" <?php echo ($res['MUNKARENDID']=='rm')?'selected':''?>>Részmunkaidő</option>
            <option value="am" <?php echo ($res['MUNKARENDID']=='am')?'selected':''?>>Alkalmi munka</option>
            <option value="vm" <?php echo ($res['MUNKARENDID']=='vm')?'selected':''?>>Vállalkozói</option>
        </select><br/>
        <label><b>Típus</b></label><br>
        <select id="tipus" name="tipus" required>
            <option value="">Válasszon típust</option>
            <option value="it" <?php echo ($res['TIPUSID']=='it')?'selected':''?>>Informatika</option>
            <option value="et" <?php echo ($res['TIPUSID']=='et')?'selected':''?>>Egészségügy</option>
            <option value="mt" <?php echo ($res['TIPUSID']=='mt')?'selected':''?>>Műszaki</option>
            <option value="vt" <?php echo ($res['TIPUSID']=='vt')?'selected':''?>>Vendéglátás</option>
            <option value="jt" <?php echo ($res['TIPUSID']=='jt')?'selected':''?>>Jogi</option>
            <option value="st" <?php echo ($res['TIPUSID']=='st')?'selected':''?>>Sport</option>
            <option value="ot" <?php echo ($res['TIPUSID']=='ot')?'selected':''?>>Oktatás</option>
            <option value="gt" <?php echo ($res['TIPUSID']=='gt')?'selected':''?>>Gazdasági</option>
            <option value="ft" <?php echo ($res['TIPUSID']=='ft')?'selected':''?>>Fizikai</option>
            <option value="kt" <?php echo ($res['TIPUSID']=='kt')?'selected':''?>>Közigazgatás</option>
            <option value="lt" <?php echo ($res['TIPUSID']=='lt')?'selected':''?>>Logisztika</option>
            <option value="tt" <?php echo ($res['TIPUSID']=='tt')?'selected':''?>>Takarítás</option>
        </select><br/>
        <label><b>Fizetés</b></label><br>
        <input style="width: 100%" type="number" placeholder="Fizetés" name="fizu" min="0" value="<?php  echo $res['FIZETES']?>" required><br>
        <label><b>Hely</b></label><br>
        <input style="width: 100%" type="text" placeholder="Hely" name="hely" value="<?php  echo $res['HELY']?>" required><br>
        <label><b>Elvárt munkatapasztalat</b></label><br>
        <input style="width: 100%" type="number" placeholder="Elvárt munkatapasztalat" name="tapasztalat" min="0" value="<?php  echo $res['ELVART_MUNKATAPASZTALAT_EV']?>" required><br>
        <label><b>Leírás</b></label><br>
        <input style="width: 100%" type="text" placeholder="Leírás" name="leir" value="<?php  echo $res['LEIRAS']?>" required><br>
        <input type="hidden" name="id" value="<?php  echo $id; ?>">
    </div>
    <div style="text-align: center; margin-top: 10px">
        <input type="submit" value="Módosít" name="Modosit">
    </div>
</form>
</body>
</html>


<?php
$conn ="";
require_once "dbconn/conn.php";
require_once "menu.php";
require_once "common/fgv.php";
?>
<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Főoldal</title>
    <link rel="stylesheet" href="css/mindenes.css">
    <style>
        .layout-container {
            display: flex;
        }
        main {
            flex: 85;
        }
        aside {
            flex: 15;
            background: #6c7cb7;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="layout-container">
    <main>
        <form method="get" action="index.php" style="border: #000000 3px solid; background: #6c7cb7; text-align: center">
            <label for="kereses">Keresés:</label>
            <input type="text" name="cegek" id="cegek" placeholder="Cég neve">
            <input type="text" name="varosok" id="varosok" placeholder="Város">
            <select id="tipus" name="tipus">
                <option value="">Válasszon típust</option>
                <option value="informatika">Informatika</option>
                <option value="egészségügy">Egészségügy</option>
                <option value="műszaki">Műszaki</option>
                <option value="vendéglátás">Vendéglátás</option>
                <option value="jogi">Jogi</option>
                <option value="sport">Sport</option>
                <option value="oktatás">Oktatás</option>
                <option value="gazdaság">Gazdasági</option>
                <option value="fizikai">Fizikai</option>
                <option value="közigazgatás">Közigazgatás</option>
                <option value="logisztika">Logisztika</option>
                <option value="takarítás">Takarítás</option>
            </select>
            <select id="munkarend" name="munkarend">
                <option value="">Válasszon munkarendet</option>
                <option value="teljes munkaidő">Teljes munkaidő</option>
                <option value="részmunkaidő">Részmunkaidő</option>
                <option value="alkalmi munka">Alkalmi munka</option>
                <option value="vállalkozói">Vállalkozói</option>
            </select>
            <select name="ev">
                <?php
                $currentYear = date('Y');
                for ($i = $currentYear; $i >= 1900; $i--) {
                    echo '<option value="' . $i . '">' . $i . '</option>';
                }
                ?>
            </select>
            <select id="honap" name="honap">
                <option value="">Válasszon hónapot</option>
                <option value="01">Január</option>
                <option value="02">Február</option>
                <option value="03">Március</option>
                <option value="04">Árpilis</option>
                <option value="05">Május</option>
                <option value="06">Június</option>
                <option value="07">Július</option>
                <option value="08">Augusztus</option>
                <option value="09">Szeptember</option>
                <option value="10">Október</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
            <input type="submit" name="szures" value="Szűrés">
        </form>
        <?php
        $db = new Database();
        $conn = $db -> connect();
        if(isset($_GET['szures'])) {
            $whereClause = "";
            $cegek = $_GET["cegek"];
            $varosok = $_GET["varosok"];
            $tipus = $_GET["tipus"];
            $munkarend = $_GET["munkarend"];
            $ev = $_GET["ev"];
            $honap = $_GET["honap"];
            $elso=true;
            if ($cegek != ""){
                if($elso){
                    $whereClause = " AND (";
                    $elso=false;
                }
                $whereClause = $whereClause." munkaltato.munkaltatonev LIKE '%$cegek%'";
            }
            if ($varosok != ""){
                if($elso){
                    $whereClause = $whereClause." AND (";
                    $elso=false;
                }else{
                    $whereClause=$whereClause." AND";
                }
                $whereClause = $whereClause." allas.hely LIKE '%$varosok%'";
            }
            if ($tipus != ""){
                if($elso){
                    $whereClause = $whereClause." AND (";
                    $elso=false;
                }else{
                    $whereClause=$whereClause." AND";
                }
                $whereClause = $whereClause." tipus.tipusnev ='$tipus'";
            }
            if ($munkarend != ""){
                if($elso){
                    $whereClause =$whereClause. " AND (";
                    $elso=false;
                }else{
                    $whereClause=$whereClause." AND";
                }
                $whereClause =$whereClause. " munkarend.munkarendnev = '$munkarend'";
            }
            if ($ev != ""){
                if($elso){
                    $whereClause = $whereClause." AND (";
                    $elso=false;
                }else{
                    $whereClause=$whereClause." AND";
                }
                $whereClause = $whereClause."  EXTRACT(YEAR FROM allas.meghirdet_ido)=$ev";
            }
            if ($honap != ""){
                if($elso){
                    $whereClause =$whereClause. " AND (";
                    $elso=false;
                }else{
                    $whereClause=$whereClause." AND";
                }
                $whereClause = $whereClause." EXTRACT(MONTH FROM allas.meghirdet_ido)=$honap";
            }
            if($whereClause!=""){
                $whereClause=$whereClause.")";
            }
            $sql = "SELECT distinct allas.allasid as id ,
                    allas.allasnev as nev,
                    munkaltato.munkaltatonev as cegek,
                    tipus.tipusnev as tipus, 
                    munkarend.munkarendnev as munkarend,
                    allas.hely ,
                    allas.munkaido as munkaido,
                    allas.fizetes as fizetes,
                    allas.meghirdet_ido as ido ,
                    EXTRACT(YEAR FROM allas.meghirdet_ido) as ev, 
                    EXTRACT(MONTH FROM allas.meghirdet_ido) as honap 
                    FROM allas, tipus, munkarend, munkaltato 
                    WHERE allas.meghirdet_id=munkaltato.felhid 
                    and  allas.tipusid=tipus.tipusid
                    and allas.munkarendid=munkarend.munkarendid".$whereClause;
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            if(isset($_GET['szures'])) {
                while ($row = oci_fetch_array($stid)) {
                    $uj_datum = datum_ertelmes_alkara_hozasa($row[8]);
                    echo "<div style='border: #1d60aa 3px solid; margin:50px 20% 50px 20%'>
                            <section>
                                <form action='index.php'>
                                    <fieldset>
                                        <h1 style='text-align: center'>{$row[1]}</h1>
                                        <label>Meghírdető: {$row[2]}</label><br><br>
                                        <label>Típus: {$row[3]}</label><br><br>
                                        <label>Munkarend: {$row[4]}</label><br><br>
                                        <label>Hely {$row[5]}</label><br><br>
                                        <label>Munkaidő: {$row[6]}</label><br><br>
                                        <label>Fizetés: {$row[7]} Ft</label><br><br>
                                        <label>Meghirdetés ideje: $uj_datum</label><br>
                                    </fieldset><br>
                                    <div style='text-align: center'>
                                   <button><a href='megtekint.php?id={$row[0]}'>Megtekintés</a></button>
                                    </div><br>
                                </form>
                            </section> 
                            </div>";
                }
            }
        }else{
            $db = new Database();
            $conn = $db -> connect();
            $sql = "select allas.allasid as id,
                    allas.allasnev as nev,
                    munkaltato.munkaltatonev as hirdeto,
                    tipus.tipusnev as tipus,
                    munkarend.munkarendnev as munkarend,
                    allas.hely ,
                    allas.munkaido as munkaido,
                    allas.fizetes as fizetes,
                    allas.meghirdet_ido as ido
                    from allas,tipus, munkarend, munkaltato
                    where allas.meghirdet_id=munkaltato.felhid
                    and  allas.tipusid=tipus.tipusid
                    and allas.munkarendid=munkarend.munkarendid
                    order by allas.meghirdet_ido desc";
            $stid = oci_parse($conn,$sql);
            oci_execute($stid);
            while ($row = oci_fetch_array($stid)){
                $uj_datum = datum_ertelmes_alkara_hozasa($row[8]);
                echo "<div style='border: #1d60aa 3px solid; margin:50px 20% 50px 20%'>
                <section>
                <form action='index.php'>
                        <fieldset>
                            <h1 style='text-align: center'>{$row[1]}</h1>
                            <label>Meghírdető: {$row[2]}</label><br><br>
                            <label>Típus: {$row[3]}</label><br><br>
                            <label>Munkarend: {$row[4]}</label><br><br>
                            <label>Hely: {$row[5]}</label><br><br>
                            <label>Munkaidő: {$row[6]}</label><br><br>
                            <label>Fizetés: {$row[7]} Ft</label><br><br>
                            <label>Meghirdetés ideje: $uj_datum</label><br>
                        </fieldset><br>
                        <div style='text-align: center'>
                       <button><a href='megtekint.php?id={$row[0]}'>Megtekintés</a></button>
                        </div><br>
                    </form>
                </section> 
                </div>";
            }
        }
        ?>
    </main>
    <aside>
        <h1 style="color: white">Milyten típusú állást keres?</h1>
        <?php
        $db = new Database();
        $conn = $db -> connect();
        $sql = "select tipusnev, tipusid from tipus";
        $stid = oci_parse($conn,$sql);
        oci_execute($stid);
        while ($row = oci_fetch_array($stid)){
            echo "<a class='dropdown-item' href='allas.php?tipusnev=$row[0]'>{$row[0]}</a><br><br>";
        }
        ?>
    </aside>
</body>
</html>


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
    <title>Állás</title>
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
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "GET"){
            $tipus = $_GET["tipusnev"];
            $sql = "SELECT allas.allasnev, munkaltato.munkaltatonev ,
                    allas.hely ,tipus.tipusnev, munkarend.munkarendnev,
                    allas.munkaido, allas.fizetes, allas.elvart_munkatapasztalat_ev, 
                    allas.meghirdet_ido, allas.leiras, allas.allasid
                    FROM allas, tipus, munkarend, munkaltato 
                    WHERE allas.meghirdet_id=munkaltato.felhid 
                      AND allas.tipusid=tipus.tipusid 
                      and allas.munkarendid=munkarend.munkarendid 
                      and tipus.tipusnev='$tipus'";
            $stid = oci_parse($conn,$sql);
            oci_execute($stid);
        }
        ?>
        <?php if (!isset($_SESSION['user'])){?>
            <?php
            while ($row = oci_fetch_array($stid)){
                $uj_datum = datum_ertelmes_alkara_hozasa($row[8]);
                echo "<div style='border: #1d60aa 3px solid; margin:50px 20% 50px 20%'>
            <section>
                <form action='allas.php'>
                    <fieldset>
                        <h1 style='text-align: center'>{$row[0]}</h1>
                        <label>Meghirdető: {$row[1]}</label><br><br>
                        <label>Hely: {$row[2]}</label><br><br>
                        <label>Tipus: {$row[3]}</label><br><br>
                        <label>Munkarend: {$row[4]}</label><br><br>
                        <label>Munkaidő: {$row[5]}</label><br><br>
                        <label>Fizetés: {$row[6]} Ft</label><br><br>
                        <label>Elvárt munkatapasztalat: {$row[7]} év</label><br><br>
                        <label>Meghirdetés ideje: $uj_datum</label><br><br>
                        <label>Leírás: {$row[9]}</label><br>
                    </fieldset>
             </form>
        </section>
    </div>";}?>
        <?php } else{ ?>
            <?php
            while ($row = oci_fetch_array($stid)){
                $uj_datum = datum_ertelmes_alkara_hozasa($row[8]);
                echo "<div style='border: #1d60aa 3px solid; margin:50px 20% 50px 20%'>
            <section>
                <form action='allas.php'>
                    <fieldset>
                        <h1 style='text-align: center'>{$row[0]}</h1>
                         <label>Meghirdető: {$row[1]}</label><br><br>
                        <label>Hely: {$row[2]}</label><br><br>
                        <label>Tipus: {$row[3]}</label><br><br>
                        <label>Munkarend: {$row[4]}</label><br><br>
                        <label>Munkaidő: {$row[5]}</label><br><br>
                        <label>Fizetés: {$row[6]} Ft</label><br><br>
                        <label>Elvárt munkatapasztalat: {$row[7]} év</label><br><br>
                        <label>Meghirdetés ideje: $uj_datum</label><br><br>
                        <label>Leírás: {$row[9]}</label><br>
                    </fieldset>";
                    if($row[3]=="informatika"){
                        echo "<br><div style='text-align: center'>
                                <button><a href='teszt.php?id={$row[10]}&felhid={$_SESSION['user']['FELHID']}'>Teszt</a></button>
                        </div><br>
            </div>";
                    }else{echo
                     "<br><div style='text-align: center'>
                                <button><a href='jelentkezik.php?id={$row[10]}&felhid={$_SESSION['user']['FELHID']}'>Jelentkezek</a></button>
                        </div><br>
                        </form>
        </section>
    </div>";}}?>
        <?php } ?>
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
</div>
</body>
</html>

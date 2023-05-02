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
        <?php
        $conn ="";
        require_once "dbconn/conn.php";
        require_once "menu.php";
        require_once "common/fgv.php";
        $db = new Database();
        $conn = $db -> connect();
        $id=$_SESSION['user']['FELHID'];
        $eredmeny="";
        $query="
            BEGIN
            :eredmeny:=allasokszama($id);
            END;";
        $result = oci_parse($conn, $query);
        oci_bind_by_name($result, ':eredmeny', $eredmeny, 32);
        oci_execute($result);
        $sql = "select allas.allasid as id,
                    allas.allasnev as nev,
                    munkaltato.munkaltatonev as hirdeto,
                    tipus.tipusnev as tipus,
                    munkarend.munkarendnev as munkarend,
                    allas.hely ,
                    allas.munkaido as munkaido,
                    allas.fizetes as fizetes,
                    allas.meghirdet_ido as ido,
                    allas.leiras
                    from allas,tipus, munkarend, munkaltato
                    where allas.meghirdet_id=munkaltato.felhid
                    and  allas.tipusid=tipus.tipusid
                    and allas.munkarendid=munkarend.munkarendid
                    and allas.meghirdet_id='". $_SESSION['user']['FELHID'] ."'
                    order by allas.meghirdet_ido desc";
        $run = oci_parse($conn,$sql);
        $r = oci_execute($run);
        echo '<h1 style="text-align: center">Jelenleg '.$eredmeny.' darab álláshirdetése elérhető</h1>';
        while ($row = oci_fetch_array($run)){
            $avg="select avg(berezes) , avg(tavolsag), avg(munkakorny) from jelentkezik where allasid=$row[0]";
            $runavg= oci_parse($conn,$avg);
            oci_execute($runavg);
            $row1 = oci_fetch_array($runavg);
            $uj_datum_meghirdet = datum_ertelmes_alkara_hozasa($row[8]);
            echo "<div style='border: #1d60aa 3px solid; margin:50px 20% 50px 20%'>
                <section>
                <form action='sajat.php'>
                        <fieldset>
                            <h1 style='text-align: center'>{$row[1]}</h1>
                            <label>Meghírdető: {$row[2]}</label><br><br>
                            <label>Típus: {$row[3]}</label><br><br>
                            <label>Munkarend: {$row[4]}</label><br><br>
                            <label>Hely: {$row[5]}</label><br><br>
                            <label>Munkaidő: {$row[6]}</label><br><br>
                            <label>Fizetés: {$row[7]} Ft</label><br><br>
                            <label>Meghirdetés ideje: $uj_datum_meghirdet</label><br><br>
                            <label>Leírás ideje: {$row[9]}</label><br><br>
                            <label>Átlagos értékelés:</label><br><br>
                            <label style='margin-left: 30px'>Munkakörnyezet: {$row1[0]}</label><br><br>
                            <label style='margin-left: 30px'>Távolság: {$row1[1]}</label><br><br>
                            <label style='margin-left: 30px'>Bérezés: {$row1[2]}</label><br><br>                         
                             <br><div style='text-align: center'>
                                <button><a href='jelentkezett.php?id={$row[0]}'>Megtekintés</a></button>  
                        </div>
                        <br><div style='text-align: center'>
                                <button><a href='allasmodosit.php?id={$row[0]}'>Módosítás</a></button>
                        </div>
                        <br><div style='text-align: center'>
                                <button><a href='allastorol.php?id={$row[0]}'>Törlés</a></button>
                        </div><br>
                        </fieldset><br>
                    </form>
                </section> 
                </div>";
        }
        ?>
    </main>
</body>
</html>

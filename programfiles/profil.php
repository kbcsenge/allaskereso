<?php
require_once "menu.php";
require_once "common/fgv.php";

if (!isset($_SESSION['user'])){
    header("Location: index.php");
}

define("DEFAULT_PROFILKEP", "img/profilkepek/dobby.jpeg");
$profilkep = DEFAULT_PROFILKEP;
$utvonal = trim($_SESSION['user']['FELHNEV']);

?>

<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Profil</title>
    <link rel="stylesheet" href="css/mindenes.css">
    <style>
        img{
            display: block;
            margin: auto;
            width: 200px;
        }
        table {
            border: 1px solid #6c7cb7;
            border-collapse: collapse;
            margin-left: auto;
            margin-right: auto;
        }
        td, th{
            border: 1px solid #6c7cb7;
            padding: 8px;
        }
        th{
            text-align: left;
        }
        input[type='submit']{
            display: block;
            margin: 15px auto;
            width: auto;
        }
        td img {
            display: block;
            max-width: 50px;
            margin-left: 0;
        }
    </style>
</head>
<body>
<img src="img/profilkep.jpg" alt="profilkép">
<table>
    <?php
    $db = new Database();
    $conn = $db -> connect();

    $felhasznalo = "SELECT ceg_e FROM felhasznalo WHERE felhnev ='". $_SESSION['user']['FELHNEV'] ."'";
    $stid = oci_parse($conn,$felhasznalo);
    oci_execute($stid);
    $row = oci_fetch_array($stid);
    $ceg = false;
    $id=$_SESSION['user']['FELHID'];
    $eredmeny="";
    $query="
            BEGIN
            :eredmeny:=jelentkezesekszama($id);
            END;";
    $result = oci_parse($conn, $query);
    oci_bind_by_name($result, ':eredmeny', $eredmeny, 32);
    oci_execute($result);
    // ha cég
    if ($row[0] == 1){
        $ceg = true;
        $adatok = "munkaltatonev, dolgozokszama";
        $tabla = "munkaltato";
    }
    // ha nem cég
    else {
        $adatok = "nev, nem, szulido";
        $tabla = "allaskereso";
    }
    $adatok = "SELECT felhnev, email, telsz, tartozkhely, ". $adatok ." FROM felhasznalo, ". $tabla .
        " WHERE felhnev = '". $_SESSION['user']['FELHNEV'] ."' AND felhasznalo.felhid = ". $tabla .".felhid";

    $stid = oci_parse($conn, $adatok);
    oci_execute($stid);
    $row = oci_fetch_array($stid);

    $_SESSION["user"]['FELHNEV'] = $row[0];

    for ($i=0; $i < (count($row)/2); $i++){
        if ($row[$i] === null){
            $row[$i] = "-";
        }
    }
    if ($_SESSION['user']['CEG_E'] != '1') {
        $uj_datum = datum_ertelmes_alkara_hozasa($row['SZULIDO']);
    }
    echo
    "<tr>
            <th colspan='2' style='color: #003cff; text-align: center'>Személyes adatok</th>
        </tr>
        <tr>
            <th>Felhasználónév</th>
            <td>$row[0]</td>
        </tr>
        <tr>
            <th>E-mail cím</th>
            <td>$row[1]</td>
        </tr>
        <tr>
            <th>Telefon</th>
            <td>$row[2]</td>
        </tr>
        <tr>
            <th>Hely</th>
            <td>$row[3]</td>
        </tr>
        <tr>";
    if ($ceg) {
        echo "<tr>
            <th>Cég neve</th>
            <td>$row[4]</td>
        </tr>
        <tr>
            <th>Dolgozók száma</th>
            <td>$row[5]</td>
        </tr>";
    } else {
        echo "<tr>
            <th>Teljes név</th>
            <td>$row[4]</td>
        </tr>
        <tr>
            <th>Nem</th>
            <td>$row[5]</td>
        </tr>
        <tr>
            <th>Születési idő</th>
            <td>$uj_datum</td>
        </tr>
        <tr>
            <th>Szakma</th>
            <td>";
        $szakma = "SELECT szakma.szakmanev
                    FROM szakma
                    inner join szakmaja on szakma.szakmaid=szakmaja.szakmaid
                    WHERE szakmaja.felhid = '". $_SESSION['user']['FELHID'] ."'";
        $runsql = oci_parse($conn, $szakma);
        oci_execute($runsql);
        while ($row = oci_fetch_array($runsql)){
            echo $row[0]."<br>";
        }
        echo "</td>
        </tr>
        <tr>
            <th>Önéletrajzok</th>
            <td>";

        $sql = "SELECT cvid, nyelv FROM eletrajz WHERE felhid = '". $_SESSION['user']['FELHID'] ."'";
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);
        while ($row = oci_fetch_array($stid)){
            $nyelv = $row[1];
            if ($nyelv === "német"){ $nyelv = "nemet"; }
            if ($nyelv === "kínai"){ $nyelv = "kinai"; }
            $files = glob('./cvk/'.$_SESSION['user']['FELHID']."_".$nyelv.'.*');
            $utvonal = $files[0] ?? NULL;

            echo " <a href='$utvonal' target='_blank'>
                         <img src='img/cv_icon.png' alt='cv'>
                       </a>$row[1]";
        }
        echo "</td>
        </tr>";
    }
    ?>
</table>

<form method='post' action='profilt_modosit.php'>
    <input type='submit' name='profilt_modosit' value='Módosítás'>
</form>
</table>

<?php
    if(!$ceg) {
        echo
'<table class="table" style="margin-left: auto ; margin-right: auto ; width: 500px; border: 1px black; border-collapse: collapse">
    <thead class="thead-light">
    <tr>
        <th scope="col" style="width: 500px">Állás neve</th>
        <th scope="col" style="width: 500px">Jelentkezés</th>
        <th scope="col" style="width: 500px">Értékelések</th>
        <th scope="col" style="width: 500px">Értékelés</th>
        <th scope="col" style="width: 500px">Lejelentkezés</th>
    </tr>
    </thead>
    <tbody>';
        $applied = "select allas.allasnev, jelentkezik.jelentkezesido, jelentkezik.allasid
                     from jelentkezik
                      inner join allas on jelentkezik.allasid=allas.allasid
                       where jelentkezik.felhid=" . $_SESSION['user']['FELHID'];
        $run = oci_parse($conn, $applied);
        oci_execute($run);
        echo '<h2 style="text-align: center">Ön eddig '.$eredmeny.' állásra jelentkezett</h2>';
        while ($row1 = oci_fetch_array($run)) {
            $uj_datum_jelentkezik = datum_ertelmes_alkara_hozasa($row1[1]);
            $ertekel = "select munkakorny, berezes, tavolsag
                     from jelentkezik
                      inner join allas on jelentkezik.allasid=allas.allasid
                       where allas.allasid=$row1[2] and jelentkezik.felhid=" . $_SESSION['user']['FELHID'] ;
            $run2 = oci_parse($conn, $ertekel);
            oci_execute($run2);
            $row2 = oci_fetch_array($run2);
            echo
            "<tr>
                            <td>{$row1[0]}</td>
                            <td>$uj_datum_jelentkezik </td>    
                            <td>Munkakörnyezet:{$row2[0]} <br>
                            Bérezés:{$row2[1]} <br>
                            Távolság:{$row2[2]}
                            </td>    
                            <td><button><a href='ertekeles.php?id={$row1[2]}&felhid={$_SESSION['user']['FELHID']}'>Értékelés</a></button></td>    
                            <td><button><a href='deleteapply.php?id={$row1[2]}&felhid={$_SESSION['user']['FELHID']}'>Lejelentkezés</a></button></td>                          
                        </tr>";
        }
    }
   echo
    '</tbody>
    </table>' ?>
</body>
</html>

<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Jelentkezettek</title>
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
<?php
require_once "menu.php";
require_once "common/fgv.php";
$db = new Database();
$conn = $db -> connect();
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET["id"];
    $applied = "select allaskereso.nev , allaskereso.szulido, allaskereso.nem,jelentkezik.jelentkezesido, felhasznalo.telsz, felhasznalo.email, jelentkezik.felhid
from allaskereso, felhasznalo, jelentkezik
where allaskereso.felhid=jelentkezik.felhid
  and felhasznalo.felhid=jelentkezik.felhid 
  and jelentkezik.allasid='$id'";
    $run = oci_parse($conn, $applied);
    oci_execute($run);
    $sql = "SELECT cvid, nyelv , jelentkezik.felhid
            FROM eletrajz
            inner join jelentkezik on jelentkezik.allasid=$id
            WHERE eletrajz.felhid = jelentkezik.felhid";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
}
echo "<h1 style='text-align: center'>Jelentkezettek</h1>";
while ($row = oci_fetch_array($run)){
    $uj_datum_szulido = datum_ertelmes_alkara_hozasa($row[1]);
    $uj_datum_jelentkezik = datum_ertelmes_alkara_hozasa($row[3]);
echo
" <br><table><tr>
    <th colspan='2' style='color: #003cff; text-align: center'>Jelentkező adatai</th>
</tr>
<tr>
    <th>Név</th>
    <td>$row[0]</td>
</tr>
<tr>
    <th>Szüledési dátum</th>
    <td>$uj_datum_szulido</td>
</tr>
<tr>
    <th>Nem</th>
    <td>$row[2]</td>
</tr>
<tr>
    <th>Jelentkezés ideje</th>
    <td>$uj_datum_jelentkezik</td>
</tr>
<tr>
    <th>Telefonszám</th>
    <td>$row[4]</td>
</tr>
<tr>
    <th>E-mail</th>
    <td>$row[5]</td>
</tr>
<tr>
    <th>Szakmák</th>
    <td>";
    $szakma = "SELECT szakma.szakmanev
                    FROM szakma
                    inner join szakmaja on szakma.szakmaid=szakmaja.szakmaid
                    WHERE szakmaja.felhid = $row[6]";
    $runsql = oci_parse($conn, $szakma);
    oci_execute($runsql);
    while ($row1 = oci_fetch_array($runsql)){
        echo $row1[0]."<br>";
    }
    echo "</td>
</tr>
<tr>
    <th>Önéletrajzok</th>
    <td>";
    $sql = "SELECT cvid, nyelv , felhid
            FROM eletrajz
            WHERE felhid = $row[6] ";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
        while ($row1 = oci_fetch_array($stid)){
        $nyelv = $row1[1];
        if ($nyelv === "német"){ $nyelv = "nemet"; }
        if ($nyelv === "kínai"){ $nyelv = "kinai"; }
        $files = glob('./cvk/'.$row1[2]."_".$nyelv.'.*');
        $utvonal = $files[0] ?? NULL;

        echo " <a href='$utvonal' target='_blank'>
            <img src='img/cv_icon.png' alt='cv'>
        </a>$row1[1]";
        }
        echo "</td></tr></table><br>";
    echo "<div style='text-align: center'>
                                <button><a href='jelentkezotorol.php?id={$row[6]}&allasid={$_GET["id"]}'>Elutasítás</a></button>
                        </div><br>";
}
?>
</body>
</html>
<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Szatisztika</title>
    <style>
        a{
            color: white;
        }
        tr{
            text-align: justify;
        }
    </style>
</head>
<body>
<?php
require_once "dbconn/conn.php";
require_once "menu.php";
$db = new Database();
$conn = $db -> connect();
$sqltipus = "select tipus.tipusnev, count(allas.allasid) from allas, tipus where allas.tipusid=tipus.tipusid group by tipus.tipusnev";
$run1 = oci_parse($conn,$sqltipus);
oci_execute($run1);
$sqlrend = "select munkarend.munkarendnev, count(allas.allasid) from allas, munkarend where allas.munkarendid=munkarend.munkarendid group by munkarend.munkarendnev";
$run2 = oci_parse($conn,$sqlrend);
oci_execute($run2);
$sqlber = "select tipus.tipusnev, round(avg(allas.fizetes)) from allas, tipus where allas.tipusid=tipus.tipusid group by tipus.tipusnev";
$run3 = oci_parse($conn,$sqlber);
oci_execute($run3);
$sqlapplied = "select tipus.tipusnev, count(jelentkezik.allasid) from jelentkezik, tipus, allas where allas.tipusid=tipus.tipusid and allas.allasid=jelentkezik.allasid group by tipus.tipusnev";
$run4 = oci_parse($conn,$sqlapplied);
oci_execute($run4);
?>
<br>

<table class="table" style="margin-left: auto ; margin-right: auto ; width: 500px; border: 1px black solid; border-collapse: collapse">
    <thead class="thead-light"style="border: 1px black solid; border-collapse: collapse">
    <tr>
        <th scope="col" style="width: 500px;">Állás tipúsa</th>
        <th scope="col" style="width: 500px">Mehirdetett állások száma</th>
    </tr>
    </thead>
    <tbody>
    <?php
    echo '<h2 style="text-align: center">Meghirdetett állások száma típusonként</h2>';
    while ($row1 = oci_fetch_array($run1)){
        echo
        "<tr>
                            <td>{$row1[0]}</td>
                            <td>{$row1[1]}</td>
                        </tr>";
    }
    ?>
</table>

<table class="table" style="margin-left: auto ; margin-right: auto ; width: 500px; border: 1px black solid; border-collapse: collapse">
    <thead class="thead-light"style="border: 1px black solid; border-collapse: collapse">
    <tr>
        <th scope="col" style="width: 500px;">Munkarend</th>
        <th scope="col" style="width: 500px">Mehirdetett állások száma</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
    <?php
    echo '<h2 style="text-align: center">Meghirdetett állások száma munkarendenként</h2>';
    while ($row2 = oci_fetch_array($run2)){
    echo
    "<tr>
        <td>{$row2[0]}</td>
        <td>{$row2[1]}</td>
    </tr>";
    }
    ?>
</table>

<table class="table" style="margin-left: auto ; margin-right: auto ; width: 500px; border: 1px black solid; border-collapse: collapse">
    <thead class="thead-light"style="border: 1px black solid; border-collapse: collapse">
    <tr>
        <th scope="col" style="width: 500px;">Állás tipúsa</th>
        <th scope="col" style="width: 500px">Átlagos Fizetés</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
    <?php
    echo '<h2 style="text-align: center">Átlagos fizetés állástípusonként</h2>';
    while ($row3 = oci_fetch_array($run3)){
        echo
        "<tr>
        <td>{$row3[0]}</td>
        <td>{$row3[1]} Ft</td>
    </tr>";
    }
    ?>
</table>

<table class="table" style="margin-left: auto ; margin-right: auto ; width: 500px; border: 1px black solid; border-collapse: collapse">
    <thead class="thead-light"style="border: 1px black solid; border-collapse: collapse">
    <tr>
        <th scope="col" style="width: 500px;">Állás tipúsa</th>
        <th scope="col" style="width: 500px">Jelentkezettek száma</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
    <?php
    echo '<h2 style="text-align: center">Jelentkezések száma állástípusonként</h2>';
    while ($row4 = oci_fetch_array($run4)){
        echo
        "<tr>
        <td>{$row4[0]}</td>
        <td>{$row4[1]}</td>
    </tr>";
    }
    ?>
</table>

</body>
</html>

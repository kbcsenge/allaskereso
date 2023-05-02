<?php
$conn ="";
require_once "dbconn/conn.php";
require_once "menu.php";
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
    <main style="margin-top: 20%">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if(isset($_GET['hiba'])){
                echo "<p style='text-align: center; color: red; font-size: 50px'>{$_GET['hiba']}</p>";
            }
        }
        ?>
        <div style='text-align: center'>
            <button><a href='index.php'>Böngészés tovább</a></button>
        </div><br>
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

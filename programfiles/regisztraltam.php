<?php
$conn ="";
require_once "dbconn/conn.php";
require_once "menu.php";
?>
<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Hurrá</title>
    <link rel="stylesheet" href="css/mindenes.css">
</head>
<body>
<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $message=$_GET['uzenet'];
    echo '<h1 style="text-align: center">'.$message.'</h1>';
    echo '<br>';
    echo '<h1 style="text-align: center">'.'Lépjen be!'.'</h1>';
}
?>
<div style='text-align: center'>
    <button><a href='login.php'>Bejelentkezés</a></button>
</div><br>
</body>
</html>


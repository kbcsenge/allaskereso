<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Regisztráció</title>
    <link rel="stylesheet" href="css/mindenes.css">
    <style>
        form {
            border: #1d60aa 3px solid;
            margin:50px 600px 50px 600px;
            padding: 10px 10px 10px 10px
        }
        input[required]{
            width: 100%
        }
    </style>
</head>
<body>
<?php
require_once 'dbconn/conn.php';
require_once 'menu.php';
require_once "common/fgv.php";
$db = new Database();
$conn = $db -> connect();
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['error'])) {
        echo "<p style='text-align: center; color: red; font-size: 20px'>{$_GET['error']}</p>";
    }
}

if (isset($_POST['continue_btn']) && !isset($_POST['register_btn'])) {

    // felhasználónév ellenőrzés
    $name = $_POST["felhnev"];
    $sql4 = "SELECT * FROM felhasznalo WHERE felhnev = '$name'";
    $stmt = oci_parse($conn, $sql4);
    oci_execute($stmt);
    $row = oci_fetch_array($stmt);
    if ($row !== false){
        header("Location: register.php?error=Username is unavailable");
        exit();
    }

    // email ellnőrzése
    if (!email_vaild( trim($_POST['email']) )){
        header("Location: register.php?error=E-mail is not valid");
        exit();
    }

    // felhid létrehozása
    $felhid = "SELECT max(felhid) FROM felhasznalo";
    $stmt = oci_parse($conn, $felhid);
    oci_execute($stmt);
    $row = oci_fetch_array($stmt);
    $felhid = $row[0];
    $felhid = intval($felhid) + 1;
    $_SESSION["felhid"] = $felhid;

    $email = $_POST["email"];
    $jelszo = $_POST["password"];
    $telsz= $_POST["telsz"];
    $hely= $_POST["hely"];
    $ceg = intval($_POST["cege"]);
    $_SESSION["ceg"] = $ceg;
    $jelszohash = password_hash($jelszo, PASSWORD_DEFAULT);
    $sql = "INSERT INTO FELHASZNALO (felhnev, email, jelszo, ceg_e, felhid, telsz, tartozkhely) VALUES (:name, :email, :jelszohash, :ceg_e, :felhid, :telsz, :hely)";
    $stmt = oci_parse($conn, $sql);

    oci_bind_by_name($stmt, ":felhid", $felhid);
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":email", $email);
    oci_bind_by_name($stmt, ":jelszohash", $jelszohash);
    oci_bind_by_name($stmt, ":ceg_e", $ceg);
    oci_bind_by_name($stmt, ":telsz", $telsz);
    oci_bind_by_name($stmt, ":hely", $hely);
    if (oci_execute($stmt)){
        $succRegAsUser = true;
    }
}


if (isset($_POST['register_btn'])){

    if ($_SESSION["ceg"]){
        $ceg_nev = $_POST["ceg_nev"];
        $dolgozok = intval($_POST["dolgozoszam"]);

        $sql2 = "INSERT INTO MUNKALTATO (munkaltatonev, dolgozokszama, felhid) VALUES (:ceg_nev, :dolgozok, :felhid)";
        $stmt = oci_parse($conn, $sql2);

        oci_bind_by_name($stmt, ":felhid", $_SESSION["felhid"]);
        oci_bind_by_name($stmt, ":ceg_nev", $ceg_nev);
        oci_bind_by_name($stmt, ":dolgozok", $dolgozok);

    } else {
        $full_name = $_POST["nev"];
        $nem = $_POST["neme"];
        $szulido = $_POST["szulido"];
        $szulido = "to_date('". $szulido ."', 'YYYY-MM-DD')";

        $sql3 = "INSERT INTO ALLASKERESO (felhid, nev, nem, szulido)
                    VALUES ('". $_SESSION["felhid"] ."', '". $full_name ."', '". $nem ."', ". $szulido .")";
        $stmt = oci_parse($conn, $sql3);
    }

    if (oci_execute($stmt)){
        $tobelepesek="insert into BELEPESEK(felhid, belepett_mar) values(:felhid, 'Még nem')";
        $runto=oci_parse($conn, $tobelepesek);
        oci_bind_by_name($runto, ":felhid", $_SESSION["felhid"]);
        oci_execute($runto);
        $sql4 = "select message from messages inner join felhasznalo on felhasznalo.felhnev=messages.felhnev where felhasznalo.felhid=".$_SESSION["felhid"];
        $stmt_trig = oci_parse($conn, $sql4);
        oci_execute($stmt_trig);
        $row = oci_fetch_array($stmt_trig);
        header('Location: regisztraltam.php?uzenet='.$row[0]);
        exit();
    }
}

if (!isset($_POST['continue_btn'])){ echo "
<form method='post' autocomplete='off'>
    <div class='container'>
        <h1>Regisztráció</h1>
        <hr>
        <label>
            <b>Felhasználónév</b>
            <input type='text' placeholder='Felhasználónév' name='felhnev' required /><br>
        </label><br>
        
        <label>
            <b>E-mail</b>
            <input type='text' placeholder='Email cím' name='email' required><br>
        </label><br>
        
        <label>
            <b>Jelszó</b>
            <input type='password' placeholder='Jelszó' name='password' required><br>
        </label><br>
         <label>
            <b>Telefonszám</b>
            <input type='number' placeholder='Telefonszám' name='telsz' required><br>
        </label><br>
        <label>
            <b>Tartózkodási hely</b>
            <input type='text' placeholder='Tartózkodási hely' name='hely' required><br>
        </label><br>
        <div>
            <label>
                <b>Cég-e? : </b>
                <input type='radio' value='1' name='cege'> Igen
                <input type='radio' value='0' name='cege' checked> Nem
            </label><br>
        </div>
        <br>
        <div style='text-align: center'>
            <button style='color: white' type='submit' name='continue_btn'>Tovább</button>
        </div>
    </div>
</form>";}

if (isset($succRegAsUser)){
    echo "<form method='post' autocomplete='off'>
            <div class='container'>";
    if($_SESSION["ceg"]){
        echo "<label>
                <b>Cég neve</b>
                <input type='text' placeholder='Cég neve' name='ceg_nev' required><br>
             </label><br>
             
             <label>
                <b>Dolgozók száma a cégnél</b>
                <input type='number' placeholder='30' name='dolgozoszam' min='1'><br>
             </label><br>";
    } else {
        echo "<label>
                <b>Teljes név</b>
                <input type='text' placeholder='Teljes név' name='nev' required><br>
             </label><br>
             
             <div>
                <label>
                    <b>Neme : </b>
                    <input type='radio' value='Nő' name='neme' checked> Nő
                    <input type='radio' value='Férfi' name='neme'> Férfi
                </label><br><br>
             </div>
             
             <label>
                <b>Születési idő</b>
                <input type='date' name='szulido' value='2000-04-01' required><br>
             </label><br>";
    }
    echo "   <br>
             <div style='text-align: center'>
                <button style='color: white' type='submit' name='register_btn'>Regisztrálás</button>
             </div>
             
          </div>
        </form>
    ";
}
?>
</body>
</html>



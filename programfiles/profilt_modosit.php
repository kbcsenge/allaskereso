<?php
require_once "menu.php";
require_once "common/fgv.php";

if (!isset($_SESSION['user'])){
    header("Location: index.php");
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['error'])) {
        echo "<p style='text-align: center; color: red; font-size: 20px'>{$_GET['error']}</p>";
    }
}

function delete($tabla){
    $db = new Database();
    $conn = $db -> connect();

    $sql = "DELETE FROM $tabla WHERE felhid = '". $_SESSION['user']['FELHID'] ."'";
    $stid = oci_parse($conn, $sql);
    if (!oci_execute($stid)){
        return 1;
    }
    return 0;
}

if (isset($_POST['vissza'])){
    header("Location: profil.php");
    exit();
}

//TODO: ellenőrizni önéletrajz miatt
if (isset($_POST['profilt_torol'])){
    $n = 0;

    if ($_SESSION['user']['CEG_E']=='1'){
        $db = new Database();
        $conn = $db -> connect();
        $allasdelete="delete from allas where meghirdet_id='". $_SESSION['user']['FELHID'] ."'";
        $del = oci_parse($conn, $allasdelete);
        oci_execute($del);
        $n += delete("munkaltato");
    } else {
        $n += delete("szakmaja");
        $n += delete("jelentkezik");
        $n += delete("eletrajz");
        $files = glob('./cvk/'.$_SESSION['user']['FELHID'].'*');
        if (count($files) > 0){
            unlink($files[0]);
        }
        $n += delete("allaskereso");

    }

    $n += delete("felhasznalo");

    if ($n > 0){
        header("Location: profilt_modosit.php?error=Account delete failed");
        exit();
    }
    header("Location: logout.php");
    exit();
}

if (isset($_POST['profilt_ment'])){
    $db = new Database();
    $conn = $db -> connect();
    // felhasználónév ellenőrzés
    $sql4 = "SELECT * FROM felhasznalo WHERE felhnev ='".$_POST['username']."'  AND felhid != '". $_SESSION['user']['FELHID'] ."'";
    $stmt = oci_parse($conn, $sql4);
    oci_execute($stmt);
    $row = oci_fetch_array($stmt);
    if ($row !== false){
        header("Location: profilt_modosit.php?error=Username is unavailable");
        exit();
    } else {
        $_SESSION["user"]['FELHNEV'] = $_POST['username'];
    }

    // telefonszám ellenőrzése
    if (!only_numbers( trim($_POST['telefon']) )){
        header("Location: profilt_modosit.php?error=Phone number must be a number");
        exit();
    }

    // email ellenőrzése
    if (!email_vaild( trim($_POST['email']) )){
        header("Location: profilt_modosit.php?error=E-mail is not valid");
        exit();
    }

    $sql = "UPDATE felhasznalo
            SET tartozkhely='".$_POST['hely']."', felhnev = '". $_POST['username'] ."', email = '". $_POST['email'] ."', telsz = '". $_POST['telefon'] ."'";
        $folyt = " ";

    $folyt = $folyt . "WHERE felhid = '". $_SESSION['user']['FELHID'] ."'";
    $sql = $sql . $folyt;

    $stid = oci_parse($conn, $sql);
    if (!oci_execute($stid)){
        echo "Nem sikerült az update: felhasznalo";
    }

    if ($_SESSION['user']['CEG_E']=='1'){
        $sql = "UPDATE munkaltato
                SET munkaltatonev = '". $_POST['ceg_nev'] ."', dolgozokszama = '". $_POST['dolgozok'] ."'
                WHERE felhid = '". $_SESSION['user']['FELHID'] ."'";
        $stid = oci_parse($conn, $sql);
        if (!oci_execute($stid)){
            echo "Nem sikerült az update: munkaltato";
        }
    } else {
        $szulido = $_POST["szulido"];
        $szulido = "to_date('". $szulido ."', 'YYYY-MM-DD')";

        $sql = "UPDATE allaskereso
                SET nev = '". $_POST['full_name'] ."', nem = '". $_POST['nem'] ."', szulido = $szulido
                WHERE felhid = '". $_SESSION['user']['FELHID'] ."'";
        $stid = oci_parse($conn, $sql);
        if (!oci_execute($stid)){
            echo "Nem sikerült az update: allaskereso";
        }
    }


    if (isset($_FILES["cv"]) && is_uploaded_file($_FILES["cv"]["tmp_name"]) && isset($_POST["nyelv"])) {

        $nyelv = $_POST["nyelv"];
        if ($nyelv === "német"){ $nyelv = "nemet"; }
        if ($nyelv === "kínai"){ $nyelv = "kinai"; }
        $files = glob('./cvk/'.$_SESSION['user']['FELHID']."_".$nyelv.'.*');

        // egy nyelven csak egy cv lehet
        if (count($files) > 0){
            unlink($files[0]);
            $cvdelete="delete from eletrajz where nyelv='$nyelv' and felhid='". $_SESSION['user']['FELHID'] ."'";
            $del = oci_parse($conn, $cvdelete);
            oci_execute($del);

        }

        if (!fajlFeltoltese($_SESSION['user']['FELHID'], $_POST["nyelv"])){
            header("Location: profilt_modosit.php?error=File upload failed.");
            exit();
        } else {
            // cvid létrehozása
            $cvid = "SELECT max(cvid) FROM eletrajz";
            $stmt = oci_parse($conn, $cvid);
            oci_execute($stmt);
            $row = oci_fetch_array($stmt);
            $cvid = intval($row[0]) + 1;

            $sql = "INSERT INTO ELETRAJZ(cvid, nyelv, felhid) VALUES(:cvid, :nyelv, :felhid)";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ":cvid", $cvid);
            oci_bind_by_name($stmt, ":nyelv", $_POST["nyelv"]);
            oci_bind_by_name($stmt, ":felhid", $_SESSION['user']['FELHID']);
            oci_execute($stmt);
        }
    }

    // nyelv alapján cv törlés
    if (isset($_POST["nyelv_del"])){
        $nyelv = $_POST["nyelv_del"];

        for ($i = 0; $i < count($nyelv); $i++){
            $sql = "DELETE FROM eletrajz WHERE felhid = '". $_SESSION['user']['FELHID'] . "' AND nyelv = '" . $nyelv[$i] ."'";
            $stid = oci_parse($conn, $sql);
            if (oci_execute($stid)){
                if ($nyelv[$i] === "német"){ $nyelv[$i] = "nemet"; }
                if ($nyelv[$i] === "kínai"){ $nyelv[$i] = "kinai"; }
                $files = glob('./cvk/'.$_SESSION['user']['FELHID']."_".$nyelv[$i].'.*');
                unlink($files[0]);
            }
        }
    }
    if(isset($_POST['szakma'])){
        $szakma=$_POST['szakma'];
        $felhid=$_SESSION['user']['FELHID'];
        $vane="SELECT count(szakmaid) from szakmaja where szakmaid='$szakma' and felhid='$felhid'";
        $run=oci_parse($conn, $vane);
        oci_execute($run);
        $row = oci_fetch_array($run);
        if($row[0]==0){
            $sql="insert into SZAKMAJA(szakmaid, felhid) values('$szakma','$felhid')";
            $runsql= $run=oci_parse($conn, $sql);
            oci_execute($runsql);
        }else{
            header("Location: profilt_modosit.php?error=Már rendelkezel ilyen szakmával.");
            exit();
        }
    }
    if(isset($_POST["szakma_del"])){
        $szakma = $_POST["szakma_del"];
        $felhid=$_SESSION['user']['FELHID'];
        for ($i = 0; $i < count($szakma); $i++) {
            $sqldel = "DELETE FROM szakmaja WHERE felhid = '$felhid' AND szakmaid = '$szakma[$i]'";
            $rundel = oci_parse($conn, $sqldel);
            oci_execute($rundel);
        }
    }
}

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
        table input{
            border: none;
            padding: 5px;
            font-size: medium;
        }
        table input:focus{
            outline:none;
        }
        td img {
            display: block;
            max-width: 50px;
            margin-left: 0;
        }
        .center {
            text-align: center;
        }
    </style>

</head>
<body>
<img src="img/profilkep.jpg" alt="profilkép">
<form method="post" autocomplete='off' enctype="multipart/form-data">
    <table>
        <?php
        $db = new Database();
        $conn = $db -> connect();

        // ha cég
        if ($_SESSION['user']['CEG_E']=='1'){
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

        if ($_SESSION['user']['CEG_E'] != '1') {
            $uj_datum = datum_ertelmes_alkara_hozasa($row['SZULIDO']);
        }
        echo
        "<tr>
                <th colspan='2' style='color: #003cff; text-align: center'>Személyes adatok</th>
            </tr>
            <tr>
                <th>Felhasználónév</th>
                <td><input  type='text' name='username' value='$row[0]' required></td>
            </tr>
            <tr>
                <th>E-mail cím</th>
                <td><input  type='text' name='email' value='$row[1]' required></td>
            </tr>
            <tr>
                <th>Telefon</th>
                <td><input  type='tel' name='telefon' value='$row[2]'></td>
            </tr>
             <tr>
                <th>Hely</th>
                <td><input  type='text' name='hely' value='$row[3]'></td>
            </tr>
            ";
        if ($_SESSION['user']['CEG_E']=='1'){
            echo "<tr>
                    <th>Cég neve</th>
                    <td><input  type='text' name='ceg_nev' value='$row[4]' required></td>
                </tr>
                <tr>
                    <th>Dolgozók száma</th>
                    <td><input  type='number' name='dolgozok' value='$row[5]'  min='1'></td>
                </tr>";
        } else {
            echo "<tr>
                    <th>Teljes név</th>
                    <td><input  type='text' name='full_name' value='$row[4]' required></td>
                </tr>
                <tr>
                    <th>Nem</th>";
            echo $row[5] === "Nő" ?
                "<td>
                    <input  type='radio' name='nem' value='Nő' checked> Nő
                    <input  type='radio' name='nem' value='Férfi'> Férfi
                 </td>"
                :
                "<td>
                    <input  type='radio' name='nem' value='Nő'> Nő
                    <input  type='radio' name='nem' value='Férfi' checked> Férfi
                </td>";
            echo "
                </tr>
                <tr>
                    <th>Születési idő</th>
                    <td><input  type='date' name='szulido' value='$uj_datum' required></td>";
            echo "</select>
                </tr>
                <tr>
                    <th rowspan='3'>Szakmák</th>
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
            echo " </td>
                </tr>
                <tr>
                    <td>
                        <p class='center'>Új hozzáadása</p>                                            
                        <label>Szakma</label>
                        <select name='szakma' id='szakma' required>
                            <option value='-' selected disabled>-</option>
                            <option value='isz' >Informatikus</option>
                            <option value='szsz'>Szakács</option>
                            <option value='ksz'>Közgazdász</option>
                            <option value='jsz'>Jogász</option>
                            <option value='esz'>Személyi edző</option>
                            <option value='msz'>Mérnök</option>
                            <option value='rsz'>Rendőr</option>
                            <option value='tsz'>Tanár</option>
                            <option value='osz'>Szakorvos</option>
                            <option value='gysz'>Gyógyszerész</option>
                            <option value='vsz'>Vegyész</option>
                            <option value='psz'>Pincér</option>                          
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class='center'>Szakma törlése</p>";

            $sql = "SELECT szakma.szakmanev , szakma.szakmaid
                    FROM szakma
                    inner join szakmaja on szakma.szakmaid=szakmaja.szakmaid
                    WHERE szakmaja.felhid = '". $_SESSION['user']['FELHID'] ."'";
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            while ($row = oci_fetch_array($stid)){
                echo "  <label>$row[0]<input type='checkbox' name='szakma_del[]' value='$row[1]'></label>";
            }
            echo "</td>
                </tr>";


        echo "</select>
                </tr>
                <tr>
                    <th rowspan='3'>Önéletrajzok</th>
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
            echo " </td>
                </tr>
                <tr>
                    <td>
                        <p class='center'>Új hozzáadása </p>
                        <input type='file' name='cv'><br>
                        
                        <label for='lang'>Nyelve: </label>
                        <select name='nyelv' id='lang' required>
                            <option value='-' selected disabled>-</option>
                            <option value='magyar' >magyar</option>
                            <option value='angol'>angol</option>
                            <option value='német'>német</option>
                            <option value='francia'>francia</option>
                            <option value='spanyol'>spanyol</option>
                            <option value='olasz'>olasz</option>
                            <option value='kínai'>kínai</option>
                            <option value='finn'>finn</option>
                            <option value='arab'>arab</option>
                            <option value='hindi'>hindi</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class='center'>Fájl törlése </p>";

            $sql = "SELECT nyelv from eletrajz WHERE felhid = '". $_SESSION['user']['FELHID'] ."'";
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            while ($row = oci_fetch_array($stid)){
                echo "  <label>$row[0]<input type='checkbox' name='nyelv_del[]' value='$row[0]'></label>";
            }
            echo "</td>
                </tr>";
            }
        ?>
        </table>

        <input type='submit' name='profilt_ment' value='Mentés'>
        <input type='submit' name='vissza' value='Vissza' id="back">
        <input type='submit' name='profilt_torol' value='Profil törlése'>

</form>
<script>
    //input mezők formázása
    let sorok = document.getElementsByTagName("td");
    for (let i = 0; i < sorok.length; i++){
        let adat = sorok[i].getElementsByTagName("input")[0];
        if (adat.getAttribute("type") !== "radio"){
            adat.onfocus = () => { sorok[i].style.border = "2px solid #003cff" };
            adat.onblur = () => { sorok[i].style.border = "1px solid #6c7cb7" };
        }
    }

    //vissza gombhoz
    let elem = document.getElementById('back');
    elem.onclick = () => {
        let inputok = document.getElementsByTagName("input");
        for (let i = 0; i < inputok.length; i++){
            inputok[i].removeAttribute("required");
        }
    };
</script>

</body>
</html>

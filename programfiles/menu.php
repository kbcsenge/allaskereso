<?php
require_once "dbconn/conn.php";
session_start();

// -> felhid elérhető bárhonnan
if(isset($_SESSION['user'])){
    $db = new Database();
    $conn = $db -> connect();

    $id = "SELECT felhid FROM felhasznalo WHERE felhnev ='". $_SESSION['user']['FELHNEV'] ."'";
    $stid = oci_parse($conn, $id);
    oci_execute($stid);
    $row = oci_fetch_array($stid);
    $azon = $row[0];
    $result='';
    $query="
            BEGIN
            print_user_name($azon, :result);
            END;";
    $run = oci_parse($conn, $query);
    oci_bind_by_name($run, ':result', $result, 250);
    oci_execute($run);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        .navbar {
            overflow: hidden;
            background-color: #333;
            width: 100%;
        }

        .navbar a {
            float: left;
            font-size: 16px;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .dropdown {
            float: left;
            overflow: hidden;
        }

        .dropdown .dropbtn {
            font-size: 16px;
            border: none;
            outline: none;
            color: white;
            padding: 14px 16px;
            background-color: inherit;
            font-family: inherit;
            margin: 0;
        }

        .navbar a:hover, .dropdown:hover .dropbtn {
            background-color: #003cff;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            float: none;
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        .dropdown-content a:hover {
            background-color: #ddd;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
</head>
<body>
<div class="navbar">
    <?php if(isset($_SESSION['user'])){?>
        <?php
        $db = new Database();
        $conn = $db -> connect();
        $felhasznalo_nev = $_SESSION['user']['FELHNEV'];
        $sql = "select * from felhasznalo where felhnev=:felhasznalo_nev";
        $stid = oci_parse($conn,$sql);
        oci_bind_by_name($stid, ':felhasznalo_nev', $felhasznalo_nev);
        oci_execute($stid);
        $row = oci_fetch_array($stid);
        ?>
        <?php if($row[6]==1){ ?>
            <a href="sajat.php">Állások</a>
            <a href="hirdet.php">Állás meghirdetése</a>
            <div class="dropdown">
                <button class="dropbtn">Profil
                    <i class="fa fa-caret-down"></i>
                </button>
                <div class="dropdown-content">
                    <a href="profil.php">Profil megtekintése</a>
                    <a href="logout.php">Kijelentkezés</a>
                </div>
            </div>
            <div>
                <p style="float: right; margin-right: 10px; color: white"><?php echo $result; ?></p>
            </div>
        <?php }else{ ?>
            <a href="index.php">Főoldal</a>
            <div class="dropdown">
                <button class="dropbtn">Kategóriák
                    <i class="fa fa-caret-down"></i>
                </button>
                <div class="dropdown-content">
                    <?php
                    $db = new Database();
                    $conn = $db -> connect();

                    $sql = "select tipusnev, tipusid from tipus";
                    $stid = oci_parse($conn,$sql);
                    oci_execute($stid);


                    while ($row = oci_fetch_array($stid)){
                        echo "<a class='dropdown-item' href='allas.php?tipusnev=$row[0]'>{$row[0]}</a>";
                    }

                    ?>
                </div>
            </div>
            <a href="rolunk.php">Rólunk</a>
            <a href="statisztika.php">Statisztikák</a>
            <div class="dropdown">
                <button class="dropbtn">Profil
                    <i class="fa fa-caret-down"></i>
                </button>
                <div class="dropdown-content">
                    <a href="profil.php">Profil megtekintése</a>
                    <a href="logout.php">Kijelentkezés</a>
                </div>
            </div>
            <div>
                <p style="float: right; margin-right: 10px; color: white"><?php echo $result; ?></p>
            </div>
        <?php } ?>
    <?php } else{ ?>
        <a href="index.php">Főoldal</a>
        <div class="dropdown">
            <button class="dropbtn">Kategóriák
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-content">
                <?php
                $db = new Database();
                $conn = $db -> connect();

                $sql = "select tipusnev, tipusid from tipus";
                $stid = oci_parse($conn,$sql);
                oci_execute($stid);


                while ($row = oci_fetch_array($stid)){
                    echo "<a class='dropdown-item' href='allas.php?tipusnev=$row[0]'>{$row[0]}</a>";
                }
                ?>
            </div>
        </div>
        <a href="rolunk.php">Rólunk</a>
        <a href="statisztika.php">Statisztikák</a>
        <a href='login.php' >Bejelentkezés</a>
        <a  href='register.php'>Regisztráció</a>
    <?php } ?>
</div>
</body>
</html>


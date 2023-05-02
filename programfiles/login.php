<?php
require_once 'dbconn/conn.php';
require_once "menu.php";

$db = new Database();
$conn = $db -> connect();


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['error'])) {
        echo "<p style='text-align: center; color: red; font-size: 20px'>{$_GET['error']}</p>";
    }
}

if (isset($_POST['username']) && isset($_POST['password'])) {
    function validate($data)
    {

        $data = trim($data);

        $data = stripslashes($data);

        $data = htmlspecialchars($data);

        return $data;

    }

    $username = validate($_POST['username']);

    $password = $_POST['password'];

    if (empty($username)) {

        header("Location: login.php?error=Username is required");
        exit();

    } else if (empty($password)) {

        header("Location: login.php?error=Password is required");
        exit();

    }else{
        $query = "SELECT count(*) as mennyi FROM felhasznalo WHERE felhnev = :username";
        $count = oci_parse($conn, $query);
        oci_bind_by_name($count, ':username', $username);
        oci_execute($count);
        $row = oci_fetch_array($count);
        if($row['MENNYI']<1){
            header("Location: login.php?error=Wrong password or username");
            exit();
        }else{
            $query = "SELECT jelszo FROM felhasznalo WHERE felhnev = :username";
            $statement = oci_parse($conn, $query);
            oci_bind_by_name($statement, ':username', $username);
            oci_execute($statement);
            $row = oci_fetch_array($statement);
            if(!password_verify($password, $row['JELSZO'])){
                header("Location: login.php?error=Wrong password or username");
                exit();
            }else{
                $update="update felhasznalo set utolsobelepido=sysdate where felhnev='$username'";
                echo $update;
                $upd = oci_parse($conn, $update);
                oci_execute($upd);
                $sql = "select * from felhasznalo where felhnev='$username'";
                $st = oci_parse($conn, $sql);
                oci_execute($st);
                $res = oci_fetch_array($st);
                session_start();
                $_SESSION['user'] = $res;
                if($_SESSION['user']['CEG_E']=="1"){
                header('Location: sajat.php');
                } else{
                header('Location: index.php');
                }
            }

        }
    }
}
?>
<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Bejelentkezés</title>
    <link rel="stylesheet" href="css/mindenes.css">
</head>
<body>

<div style='border: #1d60aa 3px solid; margin:50px 20% 50px 20%; padding: 10px 10px 10px 10px'>
    <form method="post" action="login.php" autocomplete='off'>
        <h1>Bejelentkezés</h1>
        <label for="username">Felhasználónév:</label><br>
        <input style="width: 100%" type="text" id="username" name="username" required><br>
        <label for="password">Jelszó:</label><br>
        <input style="width: 100%" type="password" id="password" name="password" required><br><br>
        <div style="text-align: center">
            <input type="submit" value="Bejelentkezés"><br>
        </div>
    </form>
</div>
</body>
</html>


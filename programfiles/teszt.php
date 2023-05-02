<!DOCTYPE html>
<html>
<head>
    <title>Kitöltendő teszt</title>
</head>
<body>
<?php
require_once "menu.php";
$questions = array(
    array(
        'question' => 'Az adatok tömörítése növeli a fájl méretét?',
        'answer' => 'hamis'
    ),
    array(
        'question' => 'Az Ubuntu egy Linux alapú operációs rendszer?',
        'answer' => 'igaz'
    ),
    array(
        'question' => 'A VPN biztonságosabbá teszi az internetes kapcsolatot?',
        'answer' => 'igaz'
    ),
    array(
        'question' => 'A JavaScript egy statikus típusú programozási nyelv?',
        'answer' => 'hamis'
    ),
    array(
        'question' => 'Az SMTP egy protokoll az elektronikus levelek küldéséhez és fogadásához?',
        'answer' => 'igaz'
    ),
    array(
        'question' => 'A HTML egy programozási nyelv?',
        'answer' => 'hamis'
    )
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correct_answers = 0;
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'answer_') === 0) {
            $question_number = substr($key, 7);
            if ($value == $questions[$question_number]['answer']) {
                $correct_answers++;
            }
        }
    }
    $score = ($correct_answers / count($questions)) * 100;
    if ($score >= 80) {
        $db = new Database();
        $conn = $db -> connect();
        $id=$_GET["id"];
        $felhid=$_GET["felhid"];
        $query = "SELECT COUNT(*) AS cnt FROM jelentkezik WHERE felhid =:felhid and allasid=:id";
        $statement = oci_parse($conn, $query);
        oci_bind_by_name($statement, ":felhid", $felhid);
        oci_bind_by_name($statement, ":id", $id);
        oci_execute($statement);
        $result = oci_fetch_assoc($statement);
        if ($result['CNT'] > 0) {
            header("Location: hiba.php?&hiba=Már jelentkeztél erre az állásra!");
        } else {
            $query = "INSERT INTO jelentkezik (jelentkezesido,felhid, allasid, munkakorny, tavolsag, berezes, allasertekido)
                        VALUES (sysdate, :felhid,:id,NULL,NULL,NULL,NULL)";
            $statement = oci_parse($conn, $query);
            oci_bind_by_name($statement, ":felhid", $felhid);
            oci_bind_by_name($statement, ":id", $id);
            oci_execute($statement);
            header("Location: profil.php");
        }
        oci_close($conn);
        exit;
    } else {
        echo '<h1 style="text-align: center">Sajnáljuk, de nem értél el 80%-ot a teszten. Kérjük lépj vissza a Főoldalra és próbáld meg később</h1>';
    }
}
?>

<h1 style="text-align: center">Kérjük töltse ki a tesztet</h1>
<form method="post" action="" style="text-align: center; border: black 2px solid; margin: 10px 20% 10px 20%">
    <?php foreach ($questions as $key => $question) { ?>
        <p><?php echo $question['question']; ?></p>
        <input type="hidden" name="question_<?php echo $key; ?>" value="<?php echo $question['question']; ?>">
        <input type="radio" name="answer_<?php echo $key; ?>" value="igaz"> Igen
        <input type="radio" name="answer_<?php echo $key; ?>" value="hamis"> Nem
    <?php } ?>
    <br><input type="submit" name="tovabb" value="Tovább">
    <input type="button" value="Vissza" onclick="window.history.back()">
</form>
</body>
</html>
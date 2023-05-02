<?php

function fajlFeltoltese($id, $nyelv) {
    if ($_FILES["cv"]["error"] === 0) {
        $kiterjesztes = strtolower(pathinfo($_FILES["cv"]["name"], PATHINFO_EXTENSION));
        if ($nyelv === "német"){ $nyelv = "nemet"; }
        if ($nyelv === "kínai"){ $nyelv = "kinai"; }
        $utvonal = "cvk/".$id."_".$nyelv.".$kiterjesztes";
        return move_uploaded_file($_FILES["cv"]["tmp_name"], $utvonal);
    } else return false;
}

function only_numbers($input){
    if (!preg_match('/^[0-9]*$/', $input)){
        return false;
    }
    return true;
}

function email_vaild($email){
    if (!preg_match("/[0-9a-z.-]+@([0-9a-z-]+\.)+[a-z]{2,4}/", $email)) {
        return false;
    }
    return true;
}

function datum_ertelmes_alkara_hozasa($fujj){
    $tomb = explode("-", $fujj);

    $ev = intval($tomb[0]);
    if ($ev < 10){
        $ev = "200".$ev;
    } else if ($ev <= 23){
        $ev = "20".$ev;
    } else {
        $ev = "19".$ev;
    }

    switch (mb_strtolower(trim($tomb[1]))){
        case "jan.":
            $honap = "01";
            break;
        case "feb.":
            $honap = "02";
            break;
        case "márc.":
            $honap = "03";
            break;
        case "ápr.":
            $honap = "04";
            break;
        case "máj.":
            $honap = "05";
            break;
        case "jún.":
            $honap = "06";
            break;
        case "júl.":
            $honap = "07";
            break;
        case "aug.":
            $honap = "08";
            break;
        case "szept.":
            $honap = "09";
            break;
        case "okt.":
            $honap = "10";
            break;
        case "nov.":
            $honap = "11";
            break;
        case "dec.":
            $honap = "12";
            break;
        default:
            $honap = "00";
    }

    $nap = $tomb[2];
    return $ev."-".$honap."-".$nap;
}


<?php

class Database{

    public function connect(){
        $tns = "
        (DESCRIPTION =
            (ADDRESS_LIST =
              (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
            )
            (CONNECT_DATA =
              (SID = orania2)
            )
          )";

        $username = ''; //neptun kód
        $password = ''; // neptun jelszó

        $conn = oci_connect("$username", "$password", $tns,'AL32UTF8');
        if (!$conn) {
            $e = oci_error();
            trigger_error(htmlentities($e['Error'], ENT_QUOTES), E_USER_ERROR);
        }

        return $conn;
    }

}

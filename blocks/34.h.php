<?php

    $boxfound = 0;
    
    if(isset($_GET['box']) && is_numeric($_GET['box'])) {
        $boxfound = 1;
        $boxid = $_GET['box'];
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT ID, Barcode FROM boxes WHERE ID=$boxid"));
        if(!$row) {
            $boxfound = 0;
        } else {
            $barcode = $row['Barcode'];
        }
    }
    
    if($boxfound == 0) {
        if(isset($_GET['barcode'])) {
            $barcode = cleantext($_GET['barcode']);
            $boxfound = 1;

            $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT ID, Barcode FROM boxes WHERE Barcode='$barcode'"));
            if(!$row) {
                $boxfound = 0;
            } else {
                $boxid = $row['ID'];
            }
        }
    }
    
    if($boxfound == 0) {
        header('Location: index.php?pid=31'); exit;
    }

?>

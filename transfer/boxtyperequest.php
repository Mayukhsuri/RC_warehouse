<?php

    include('../includes/connection.php');
    include('../includes/g1functions.php');
    
    $output = -2;
    
    if(isset($_GET['id'])) {
        $boxcode = cleantext($_GET['id']);
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT ID FROM boxes WHERE Barcode='$boxcode' AND Status='P'"));
        if($row) {
            $boxid = $row['ID'];
            $row2 = sqlsrv_fetch_array(sqlsrv_query($connection,  "SELECT RecordTypeID FROM records WHERE BoxID=$boxid"));
            if($row2) {
                $recordtypeid = $row2['RecordTypeID'];
                $row3 = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT Description FROM recordtypes WHERE ID=$recordtypeid"));
                if($row3) {
                    $output = $row3['Description'];
                }
            }
        }
    }
    
    echo $output;

?>

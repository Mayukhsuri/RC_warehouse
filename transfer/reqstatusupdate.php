<?php

    include('../includes/connection.php');
    include('../includes/g1functions.php');
    
    // DON'T FORGET WE ARE USING $_POST VARIABLES AND *NOT* $_GET VARIABLES HERE! --------------------------------------------------------------------------- <<
    
    if(!isset($_POST['req']) || !is_numeric($_POST['req'])) {$errorcount++; }
    if(!isset($_POST['st']) || !is_numeric($_POST['st'])) {$errorcount++; }
    
    if($errorcount == 0) {
        $reqid = $_POST['req'];
        $statusid = $_POST['st'];
        //if($statusid!=3 && $statusid!=4 && $statusid!=7 && $statusid!=9) {$errorcount++; }
        if($statusid!=-1 && $statusid!=0 && $statusid!=1 && $statusid!=2 && $statusid!=3 && $statusid!=4 && $statusid!=7 && $statusid!=8 && $statusid!=9) {$errorcount++; }
    }
    
    if($errorcount == 0) {
        $row = sqlsrv_fetch_array(sqlsrv_query("SELECT Status FROM requests WHERE ID=$reqid"));
        if(!$row) {$errorcount++; } else {
            $currentstatus = $row['Status'];
            if($currentstatus < 1 || $currentstatus > 8) {$errorcount++; }
        }
    }
    
    if($errorcount == 0) {
        $currenttime = time();
        sqlsrv_query("UPDATE requests SET Status=$statusid, LastActivity=$currenttime WHERE ID=$reqid");
        http_response_code(200);
    } else {
        http_response_code(400);
    }

?>

<?php

    include('../includes/connection.php');
    include('../includes/g1functions.php');
    
    $errorcount == 0;
    if(!isset($_POST['device']) || !isset($_POST['data'])) {$errorcount++; }
    
    $device = cleantext($_POST['device']);
    $data = $_POST['data'];
    
    // data[x][0] = Barcode
    // data[x][1] = Location
    // data[x][2] = BatchID
    // data[x][3] = TimeStamp
    
    foreach($data as $key=>$value) {
        if(!is_numeric($value[2]) || !is_numeric($value[3])) {
            $errorcount++;
        }
    }
    
    $currenttime = time();
    $batchtext = '';
    $batchcount = 0;
    if($errorcount == 0) {
        foreach($data as $key=>$value) {
            $barcode = cleantext($value[0]);
            $location = cleantext($value[1]);
            $batchid = cleantext($value[2]);
            $timestamp = intval(cleantext($value[3]));
            if(!is_numeric($batchid) || !is_numeric($timestamp)) {$thiserror++; }
            if($thiserror == 0) {
                sqlsrv_query($connection, "INSERT INTO transactions (Barcode, Location, BatchID, TimeStamp, DeviceID, Active) VALUES ('$barcode','$location',$batchid,$timestamp,'$device',1)");
                if(isset($locationid[$location])) {
                    $templocationid = $locationid[$location];
                    $templocationtype = $locationtype[$location];
                } else {
                    $row2 = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT ID, LocationType FROM Locations WHERE Barcode='$location'"));
                    $templocationid = $row2['ID'];
                    $templocationtype = $row2['LocationType'];
                    $locationid[$location] = $row2['ID'];
                    $locationtype[$location] = $row2['LocationType'];
                }
                sqlsrv_query($connection, "UPDATE boxes SET Status='$templocationtype', LocationID=$templocationid, LastActivity=$currenttime WHERE Barcode='$barcode'");
                $batchcount++;
                if($batchcount > 1) {$batchtext .= '|||'; }
                $batchtext .= $barcode . ',' . $location . ',' . $timestamp;
            } else {
                $errorcount++;
                $batchcount++;
                if($batchcount == 1) {$batchtext = $batchid . '==='; }
                if($batchcount > 1) {$batchtext .= '|||'; }
                $batchtext .= 'TRANSMISSION ERROR';
            }
        }
        sqlsrv_query($connection, "INSERT INTO batches (TimeStamp, DeviceID, BatchText, Active) VALUES ($currenttime, '$device', '$batchtext', 1)");
        http_response_code(200);
    } else {
        http_response_code(400);
    }

?>

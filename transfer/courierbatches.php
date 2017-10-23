<?php

    include('../includes/connection.php');
    include('../includes/g1functions.php');
    
    $errorcount = 0;
    if(!isset($_GET['device'])) {$errorcount++; } //--------------------------------------------------------------------- THIS LINE DISABLED FOR TESTING PURPOSES ONLY!
    
    $device = -1;
    //$device = "EOFRCWD0000001"; //----------------------------------------------------------------------------------------- THIS LINE FOR TESTING PURPOSES ONLY!  DELETE!
    if(isset($_GET['device'])) {$device = cleantext($_GET['device']); }
    
    $output = "";
    $batchcount = 0;
    
    $starttime = time() - (86400 * 3);
    $result = sqlsrv_query($connection, "SELECT TimeStamp, BatchText FROM batches WHERE DeviceID='$device' AND TimeStamp>$starttime");
    if($result) {
        while($row = sqlsrv_fetch_array($result)) {
            $batchtime = $row['TimeStamp'];
            $boxarray = explode('|||',$row['BatchText']);
            $nextquery = "SELECT Barcode FROM boxes WHERE Status='C' AND LastActivity=$batchtime AND (";
            $tempbatchcount = 0;
            foreach($boxarray as $boxdetails) {
                $tempbox = explode(',',$boxdetails);
                $tempbatchcount++;
                if($tempbatchcount > 1) {$nextquery .= " OR"; }
                $nextquery .= " Barcode='" . $tempbox[0] . "'";
                $currentlocation = $tempbox[1];
            }
            $nextquery .= ")";
            if($tempbatchcount > 0) {
                $activeboxesthisbatch = 0;
                $boxesthisbatchtext = '';
                $result2 = sqlsrv_query($connection, $nextquery);
                while($row2 = sqlsrv_fetch_array($result2)) {
                    $activeboxesthisbatch++;
                    if($activeboxesthisbatch > 1) {$boxesthisbatchtext .= ','; }
                    $boxesthisbatchtext .= $row2['Barcode'];
                }
                if($activeboxesthisbatch > 0) {
                    $batchcount++;
                    if($batchcount > 1) {$output .= '|||'; }
                    $output .= $batchtime . '===' . $currentlocation . '===' . $activeboxesthisbatch . '===' . $boxesthisbatchtext;
                }
            }
        }
    } else {$errorcount++; }
    
    if($errorcount > 0) {$output = ''; }
    echo $output;
    
?>

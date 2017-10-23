<?php

    include('../includes/connection.php');
    include('../includes/g1functions.php');
    
    $errorcount == 0;
    if(!isset($_POST['device']) || !isset($_POST['data'])) {$errorcount++; }
    
    $device = cleantext($_POST['device']);
    $data = $_POST['data'];
    
    // data[x][0] = BoxCode
    // data[x][1] = PropertyID
    // data[x][2] = StartDate
    // data[x][3] = EndDate
    
    $currenttime = time();
    
    foreach($data as $key=>$value) {
        $barcode = cleantext($value[0]);
        $propertyid = intval(cleantext($value[1]));
        $startdate = intval(cleantext($value[2]));
        $enddate = intval(cleantext($value[3]));
        $row = sqlsrv_fetch_array(sqlsrv_query("SELECT ID, LocationID FROM boxes WHERE Barcode='$barcode'"));
        $boxid = $row['ID'];
        $locid = $row['LocationID'];
        $row2 = sqlsrv_fetch_array(sqlsrv_query("SELECT Barcode FROM locations WHERE ID=$locid"));
        $locbarcode = $row2['Barcode'];
        sqlsrv_query("UPDATE records SET PropertyID=$propertyid, StartDate=$startdate, EndDate=$enddate, Active=1 WHERE BoxID=$boxid");
        sqlsrv_query("UPDATE boxes SET Status='O', LastActivity='$currenttime' WHERE Barcode='$barcode'");
        if(sqlsrv_rows_affected() == 0) {
            $errorcount++;
        } else {
            sqlsrv_query("INSERT INTO transactions (Barcode, Location, BatchID, TimeStamp, DeviceID, Active) VALUES ('$barcode', '$locbarcode', -1, $currenttime, '$device', 1)");
        }
    }
    
    if($errorcount == 0) {
        http_response_code(200);
    } else {
        http_response_code(400);
    }

?>

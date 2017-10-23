<?php

    include('../includes/connection.php');
    include('../includes/g1functions.php');
    
    $activationcode = cleantext($_GET['dc']);
    
    $row = sqlsrv_fetch_array(sqlsrv_query("$connection, SELECT * FROM devices WHERE Token='$activationcode' AND ActivateDate=-1"));
    if($row) {
        $timestamp = time();
        sqlsrv_query($connection, "UPDATE devices SET ActivateDate=$timestamp WHERE Token='$activationcode' AND ActivateDate=-1");
        echo $row['DeviceID'] . '[::DDATA::]';
        echo $row['DeviceName'] . '[::DDATA::]';
        echo 'RC Warehouse Inventory System[::DDATA::]';
        echo $row['ApplicationType'];
    } else {
        echo '-1[::DDATA::]-1[::DDATA::]-1[::DDATA::]-1';
    }
    
?>

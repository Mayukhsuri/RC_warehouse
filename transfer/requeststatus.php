<?php

    include('../includes/connection.php');
    include('../includes/g1functions.php');
    
    if(isset($_GET['id']) && is_numeric($_GET['id'])) {
        $rid = $_GET['id'];
    } else {die('REQUEST NOT FOUND.'); }
    
    // Perform Primary Request Query
    $requestresult = sqlsrv_query($connection, "SELECT Status FROM requests WHERE ID=$rid");
    if(!$requestresult) {die('REQUEST NOT FOUND.'); }
    $row = sqlsrv_fetch_array($requestresult);
    echo $row['Status'];

?>

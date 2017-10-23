<?php
    
    function kick() {header('Location: index.php?pid=51'); exit; }
    
    if(!isset($_GET['did']) || !is_numeric($_GET['did'])) {kick(); }
    $deviceid = $_GET['did'];
    $row = sqlsrv_fetch_array(sqlsrv_query("SELECT * FROM devices WHERE ID=$deviceid"));
    if(!$row) {kick(); }
    $token = $row['Token'];
    
    
    
?>

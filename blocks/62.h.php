<?php
    
    function kick() {header("Location: index.php?pid=61"); }
    
    if(isset($_GET['rec']) && is_numeric($_GET['rec'])) {$whid = $_GET['rec']; } else {kick(); }
    $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT Name FROM warehouses WHERE ID=$whid"));
    if(!$row) {kick(); }
    $whname = $row['Name'];
    
    
?>

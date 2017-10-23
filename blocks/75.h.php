<?php
    
    if(isset($_GET['rec']) && is_numeric($_GET['rec'])) {
        if(isset($_GET['row']) && is_numeric($_GET['row'])) {
            $printrec = $_GET['rec'];
            $printrow = $_GET['row'];
            sqlsrv_query($connection, "UPDATE locations SET LabelPrinted=0 WHERE Warehouse=$printrec AND Row=$printrow");
            header('Location: index.php?pid=77'); exit;
        }
    }
    
?>

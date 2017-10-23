<?php

 write_log(__FILE__,__LINE__);
    
    if(isset($_GET['rl']) && is_numeric($_GET['rl'])) {
        if($_GET['rl'] == 0) {
            $sql = "UPDATE locations SET LabelPrinted=1 WHERE LocationType!='W'";
            write_log(__FILE__,__LINE__,$SQL);
            sqlsrv_query($connection, $sql);
        } else {
            if(isset($_GET['rr']) && is_numeric($_GET['rr'])) {
                $whid = $_GET['rl'];
                $rowid = $_GET['rr'];
                $sql = "UPDATE locations SET LabelPrinted=1 WHERE LocationType='W' AND Warehouse=$whid AND Row=$rowid";
                write_log(__FILE__,__LINE__,$sql);

                sqlsrv_query($connection, $sql);
            }
        }
    }
    
?>

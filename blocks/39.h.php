<?php

    if(isset($_GET['rid']) && is_numeric($_GET['rid'])) {
        $requestid = $_GET['rid'];
        $sql = "SELECT * FROM requests WHERE ID=$requestid";
        $row = sqlsrv_fetch_array(sqlsrv_query($connection,$sql));
        if(!$row) {
            header('Location: index.php?pid=35'); exit;
        }
    }
    
    if(isset($_GET['cancel']) && is_numeric($_GET['cancel']) && $_GET['cancel'] == 0) {
        $ALERT_MESSAGE = 'Are you sure you want to cancel this request? ';
        $ALERT_MESSAGE .= '<a href="index.php?pid=39&cancel=1&rid=' . $requestid . '">Yes</a>';
        $ALERT_MESSAGE .= '<a href="index.php?pid=39&rid=' . $requestid . '">No</a>';
    }
    
    if(isset($_GET['cancel']) && is_numeric($_GET['cancel']) && $_GET['cancel'] == 1) {
        $_SESSION['rcw_ALERT_CONFIRM'] = 'Records request #' . $requestid . ' successfully canceled! <a href="index.php?pid=35">OK</a>';
        $currenttime = time();
        sqlsrv_query("UPDATE requests SET Status=-1, LastActivity=$currenttime WHERE ID=$requestid");
        header('Location: index.php?pid=35'); exit;
    }

?>

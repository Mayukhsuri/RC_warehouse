<?php
    
    if(isset($_GET['pl']) && is_numeric($_GET['pl'])) {
        $labelid = $_GET['pl'];
        $ALERT_CONFIRM = 'Label generated!  Click here to print label: <a href="printfiles/BoxLabel.php?id=' . $labelid . '">Print</a>';
        $ALERT_CONFIRM .= '<a href="index.php?pid=40">Close</a>';
    }
    
?>

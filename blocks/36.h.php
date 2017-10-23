<?php
    
    $prop = cleantext($_GET['prop']);
    $rt = cleantext($_GET['rt']);
    $start = cleantext($_GET['start']);
    $end = cleantext($_GET['end']);
    
    $errorcount = 0;
    if(!is_numeric($prop) || !is_numeric($rt) || !is_numeric($start) || !is_numeric($end)) {$errorcount++; }
    if($errorcount != 0) {
        header('Location: index.php?pid=35'); exit;
    }
    
    if($prop != 0) {$propquery = " AND PropertyID=" . $prop; } else {$propquery = ""; }
    if($rt != 0) {$rtquery = " AND RecordTypeID=" . $rt; } else {$rtquery = ""; }
    
    $query = "SELECT * FROM records WHERE Active=1 AND EndDate>=$start AND StartDate<=$end" . $propquery . $rtquery . " ORDER BY StartDate ASC";
    
    if(isset($_GET['cancelrequest'])) {unset($_SESSION['RCWarehouse_RequestedBoxes']); }
    
    if(isset($_GET['requestall'])) {
        $result = sqlsrv_query($connection, $query);
        while($row = sqlsrv_fetch_array($result)) {$_SESSION['RCWarehouse_RequestedBoxes'][] = $row['BoxID']; }
        $_SESSION['RCWarehouse_RequestedBoxes'] = array_unique($_SESSION['RCWarehouse_RequestedBoxes']);
    }
    
?>

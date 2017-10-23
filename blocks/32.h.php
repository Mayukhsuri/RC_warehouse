<?php
    
    $prop = cleantext($_GET['prop']);
    $rt = cleantext($_GET['rt']);
    $start = cleantext($_GET['start']);
    $end = cleantext($_GET['end']);
    
    $errorcount = 0;
    if(!is_numeric($prop) || !is_numeric($rt) || !is_numeric($start) || !is_numeric($end)) {$errorcount++; }
    if($errorcount != 0) {
        header('Location: index.php?pid=31'); exit;
    }
    
    if($prop != 0) {$propquery = " AND PropertyID=" . $prop; } else {$propquery = ""; }
    if($rt != 0) {$rtquery = " AND RecordTypeID=" . $rt; } else {$rtquery = ""; }
    
    $query = "SELECT * FROM records WHERE Active=1 AND EndDate>=$start AND StartDate<=$end" . $propquery . $rtquery . " ORDER BY StartDate ASC";
    
?>

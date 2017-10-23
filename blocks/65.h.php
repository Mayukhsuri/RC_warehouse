<?php
    
    function kick() {header("Location: index.php?pid=61"); }
    
    if(isset($_GET['rec']) && is_numeric($_GET['rec'])) {$whid = $_GET['rec']; } else {kick(); }
    $row = sqlsrv_fetch_array(sqlsrv_query("SELECT Name FROM warehouses WHERE ID=$whid"));
    if(!$row) {kick(); }
    $whname = $row['Name'];
    
    if(isset($_GET['row']) && is_numeric($_GET['row'])) {$rowid = $_GET['row']; } else {kick(); }
    $row = sqlsrv_fetch_array(sqlsrv_query("SELECT ID FROM locations WHERE Warehouse=$whid AND Row='$rowid'"));
    if(!$row) {kick(); }
    
    $row = sqlsrv_fetch_array(sqlsrv_query("SELECT MAX(Bay) FROM locations WHERE Warehouse=$whid AND Row=$rowid"));
    $nextbay = $row['MAX(Bay)'] + 1;
    
    include('includes/ah_form.php');
    
    $form = new ah_form('addlocations');
    $form->addTitle('Add Bays - ' . $whname . ' (Row ' . $rowid . ')');
    $form->setValidationType(2);
    $form->textInput('startbay','Start Bay',$nextbay);
    $form->textInput('endbay','End Bay');
    $form->textInput('shelves','Shelves per Bay');
    
    if($form->formValidated == 1) {
        
        $errorcount = 0;
        $startbay = $form->validated['startbay']['clean'];
        $endbay = $form->validated['endbay']['clean'];
        $shelves = $form->validated['shelves']['clean'];
        
        if($endbay < $startbay) {$errorcount++; $errormessage = "Your END BAY number must be smaller than your START BAY number."; }
        $row = sqlsrv_fetch_array(sqlsrv_query("SELECT * FROM locations WHERE Warehouse=$whid AND Row=$rowid AND Bay>=$startbay AND Bay<=$endbay"));
        if($row) {$errorcount++; $errormessage = "This action would duplicate bays and/or shelves on this row.  Unable to continue."; }
        if((($endbay - $startbay + 1) * $shelves) > 400) {$errorcount++; $errormessage = "You may not add more than 400 BAY / SHELF locations at once using this function."; }
        if($shelves > 15) {$errorcount++; $errormessage = "You may not have more than 15 shelves per bay.  Unable to continue."; }
        
        if($errorcount == 0) {
            $row = sqlsrv_fetch_array(sqlsrv_query("SELECT MAX(ID) FROM locations"));
            if(!$row) {$nextID = 1; } else {$nextID = $row['MAX(ID)'] + 1; }
            for($b=$startbay; $b<=$endbay; $b++) {
                for($s=1; $s<=$shelves; $s++) {
                    $bar = 'LCN' . str_repeat('0',(6-strlen($nextID))) . $nextID;
                    $name = $whname . ' Row ' . $rowid . ' - Bay ' . $b . ' - Shelf ' . $s;
                    $query = "INSERT INTO locations (Barcode, Name, LocationType, Warehouse, Row, Bay, Shelf, Active) VALUES (";
                    $query .= "'$bar', '$name', 'W', $whid, $rowid, $b, $s, 1)";
                    sqlsrv_query($query);
                    $nextID++;
                }
            }
            header('Location: index.php?pid=64&rec=' . $whid . '&row=' . $rowid); exit;
        } else {
            $ALERT_WARNING = $errormessage;
        }
    
    }
    
?>

<?php
    
    function kick() {header("Location: index.php?pid=61"); }
    
    if(isset($_GET['rec']) && is_numeric($_GET['rec'])) {$whid = $_GET['rec']; } else {kick(); }
    $row = sqlsrv_fetch_array(sqlsrv_query("SELECT Name FROM warehouses WHERE ID=$whid"));
    if(!$row) {kick(); }
    $whname = $row['Name'];
    
    $row = sqlsrv_fetch_array(sqlsrv_query("SELECT MAX(Row) FROM locations WHERE Warehouse=$whid"));
    $nextrow = $row['MAX(Row)'] + 1;
    
    include('includes/ah_form.php');
    
    $form = new ah_form('addlocations');
    $form->addTitle('Add Rows - ' . $whname);
    $form->setValidationType(2);
    $form->textInput('startrow','Start Row',$nextrow);
    $form->textInput('endrow','End Row');
    $form->textInput('bays','Bays per Row');
    $form->textInput('shelves','Shelves per Bay');
    
    if($form->formValidated == 1) {
        
        $errorcount = 0;
        $startrow = $form->validated['startrow']['clean'];
        $endrow = $form->validated['endrow']['clean'];
        $bays = $form->validated['bays']['clean'];
        $shelves = $form->validated['shelves']['clean'];
        
        if($endrow < $startrow) {$errorcount++; $errormessage = "Your END ROW number must be smaller than your START ROW number."; }
        $row = sqlsrv_fetch_array(sqlsrv_query("SELECT * FROM locations WHERE Warehouse=$whid AND Row>=$startrow AND Row<=$endrow"));
        if($row) {$errorcount++; $errormessage = "This action would duplicate rows in this warehouse.  Unable to continue."; }
        if((($endrow - $startrow + 1) * $bays * $shelves) > 1500) {$errorcount++; $errormessage = "You may not add more than 1500 locations at once using this function."; }
        if($shelves > 15) {$errorcount++; $errormessage = "You may not have more than 15 shelves per bay.  Unable to continue."; }
        if($shelves < 1 || $bays < 1) {$errorcount++; $errormessage = "You must have at least one bay per row and at least one shelf per bay."; }
        
        if($errorcount == 0) {
            $row = sqlsrv_fetch_array(sqlsrv_query("SELECT MAX(ID) FROM locations"));
            if(!$row) {$nextID = 1; } else {$nextID = $row['MAX(ID)'] + 1; }
            for($r=$startrow; $r<=$endrow; $r++) {
                for($b=1; $b<=$bays; $b++) {
                    for($s=1; $s<=$shelves; $s++) {
                        $bar = 'LCN' . str_repeat('0',(6-strlen($nextID))) . $nextID;
                        $name = $whname . ' Row ' . $r . ' - Bay ' . $b . ' - Shelf ' . $s;
                        $query = "INSERT INTO locations (Barcode, Name, LocationType, Warehouse, Row, Bay, Shelf, Active) VALUES (";
                        $query .= "'$bar', '$name', 'W', $whid, $r, $b, $s, 1)";
                        sqlsrv_query($query);
                        $nextID++;
                    }
                }
            }
            header('Location: index.php?pid=62&rec=' . $whid); exit;
        } else {
            $ALERT_WARNING = $errormessage;
        }
    
    }
    
?>

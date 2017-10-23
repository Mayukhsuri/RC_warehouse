<?php
    
    include('includes/ah_form.php');
    
    $form = new ah_form('addoutlet');
    $form->addTitle('Add Field Location');
    $form->textInput('name','Field Location Name');
    
    //$form->addOption('W','Warehouse');
    $form->addOption('O','Field Location');
    $form->addOption('C','Courier');
    $form->addOption('T','Temporary');
    $form->addOption('D','Destruction');
    $form->selectInput('type','Field Location Type');
    
    $form->addOption(1,'Active');
    $form->addOption(2,'Inactive');
    $form->selectInput('active','Status');
    
    if($form->formValidated == 1) {
        $name = $form->validated['name']['clean'];
        $type = $form->validated['type']['clean'];
        $active = $form->validated['active']['clean'];
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT MAX(ID) as MAXID FROM locations"));
        if(!$row) {$nextid = 1; } else {$nextid = $row['MAXID'] + 1; }
        $newcode = 'LCN' . str_repeat('0',6-strlen($nextid)) . $nextid;

        $sql = "INSERT INTO locations (Barcode, Name, LocationType, Warehouse, Row, Bay, Shelf, Active, LabelPrinted) VALUES ('$newcode','$name','$type',-1,-1,-1,-1,$active,0)";
        sqlsrv_query($connection, $sql);
        header('Location: index.php?pid=71'); exit;
    }
    
?>

<?php
    
    include('includes/ah_form.php');
    
    if(isset($_GET['rec']) && is_numeric($_GET['rec'])) {
        $rec = $_GET['rec'];
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM locations WHERE ID=$rec"));
        if(!$row) {header('Location: index.php?pid=71'); exit; }
        $currentoutletname = $row['Name'];
        $currentlocationtype = $row['LocationType'];
        $currentactivestate = $row['Active'];
        $default1 = ''; $default0 = ''; if($currentactivestate == 1) {$default1 = '1'; } else {$default0 = '1'; }
    } else {
        header('Location: index.php?pid=91'); exit;
    }
    //try now ok
    function typeselected($type,$curtype) {if ($type == $curtype) {return 1; } else {return 0; } }
    
    $form = new ah_form('editoutlet');
    $form->addTitle('Edit Field Location');
    $form->textInput('name','Field Location',$currentoutletname);
    
    //$form->addOption('W','Warehouse',typeselected('W',$currentlocationtype));
    $form->addOption('O','Field Location',typeselected('O',$currentlocationtype));
    $form->addOption('C','Courier',typeselected('C',$currentlocationtype));
    $form->addOption('T','Temporary',typeselected('T',$currentlocationtype));
    $form->addOption('D','Destruction',typeselected('D',$currentlocationtype));
    $form->selectInput('type','Field Location Type');
    
    $form->addOption(1,'Active',$default1);
    $form->addOption(0,'Inactive',$default0);
    $form->selectInput('active','Status');
    
    if($form->formValidated == 1) {
        $name = $form->validated['name']['clean'];
        $loctype = $form->validated['type']['clean'];
        $active = $form->validated['active']['clean'];
        sqlsrv_query($connection, "UPDATE locations SET Name='$name', LocationType='$loctype', Active=$active WHERE ID=$rec");
        header('Location: index.php?pid=71'); exit;
    }
    
?>

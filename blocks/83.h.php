<?php
    
    include('includes/ah_form.php');
    
    if(isset($_GET['rec']) && is_numeric($_GET['rec'])) {
        $rec = $_GET['rec'];
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM properties WHERE ID=$rec"));
        if(!$row) {header('Location: index.php?pid=81'); exit; }
        $currentpropertycode = $row['Code'];
        $currentpropertyname = $row['Name'];
        $currentactivestate = $row['Active'];
        $default1 = ''; $default0 = ''; if($currentactivestate == 1) {$default1 = '1'; } else {$default0 = '1'; }
    } else {
        header('Location: index.php?pid=91'); exit;
    }
    
    $form = new ah_form('editproperty');
    $form->addTitle('Edit Property');
    $form->textInput('code','Property Code',$currentpropertycode);
    $form->textInput('name','Property Name',$currentpropertyname);
    $form->addOption(1,'Active',$default1);
    $form->addOption(0,'Inactive',$default0);
    $form->selectInput('active','Status');
    
    if($form->formValidated == 1) {
        $code = $form->validated['code']['clean'];
        $name = $form->validated['name']['clean'];
        $active = $form->validated['active']['clean'];
        sqlsrv_query($connection, "UPDATE properties SET Code='$code', Name='$name', Active=$active WHERE ID=$rec");
        header('Location: index.php?pid=81'); exit;
    }
    
?>

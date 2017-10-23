<?php
    
    include('includes/ah_form.php');
    
    $form = new ah_form('addwarehouse');
    $form->addTitle('Add Warehouse');
    $form->textInput('name','Name');
    $form->addOption(1,'Active');
    $form->addOption(2,'Inactive');
    $form->selectInput('active','Status');
    
    if($form->formValidated == 1) {
        $name = $form->validated['name']['clean'];
        $active = $form->validated['active']['clean'];
        sqlsrv_query($connection, "INSERT INTO warehouses (Name, Active) VALUES ('$name',$active)");
        header('Location: index.php?pid=67'); exit;
    }
    
?>

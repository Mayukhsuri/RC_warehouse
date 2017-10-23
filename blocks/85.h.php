<?php
    
    include('includes/ah_form.php');
    
    $form = new ah_form('adddepartment');
    $form->addTitle('Add Department');
    $form->textInput('name','Department Name');
    $form->addOption(1,'Active');
    $form->addOption(2,'Inactive');
    $form->selectInput('active','Status');
    
    if($form->formValidated == 1) {
        $name = $form->validated['name']['clean'];
        $active = $form->validated['active']['clean'];
        sqlsrv_query($connection, "INSERT INTO departments (Name, Active) VALUES ('$name',$active)");
        header('Location: index.php?pid=84'); exit;
    }
    
?>

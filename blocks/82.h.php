<?php
    
    include('includes/ah_form.php');
    
    $form = new ah_form('addproperty');
    $form->addTitle('Add Property');
    $form->textInput('code','Property Code');
    $form->textInput('name','Property Name');
    $form->addOption(1,'Active');
    $form->addOption(2,'Inactive');
    $form->selectInput('active','Status');
    
    if($form->formValidated == 1) {
        $code = $form->validated['code']['clean'];
        $name = $form->validated['name']['clean'];
        $active = $form->validated['active']['clean'];
        sqlsrv_query($connection, "INSERT INTO properties (Code, Name, Active) VALUES ('$code','$name',$active)");
        header('Location: index.php?pid=81'); exit;
    }
    
?>

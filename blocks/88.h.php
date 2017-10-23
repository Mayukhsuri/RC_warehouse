<?php
    
    include('includes/ah_form.php');
    
    $form = new ah_form('addrecordtype');
    $form->addTitle('Add Record Type');
    $form->textInput('name','Name');
    
    $result = sqlsrv_query($connection, "SELECT * FROM departments WHERE Active=1 ORDER BY Name ASC");
    while($row = sqlsrv_fetch_array($result)) {$form->addoption($row['ID'],$row['Name']); }
    $form->selectInput('department','Department');
    
    $form->setValidationType(2);
    $form->textInput('retainfor','Retention (Years)');
    
    $form->addOption(1,'Active');
    $form->addOption(2,'Inactive');
    $form->selectInput('active','Status');
    
    if($form->formValidated == 1) {
        $name = $form->validated['name']['clean'];
        $dept = $form->validated['department']['clean'];
        $retainfor = $form->validated['retainfor']['clean'];
        $active = $form->validated['active']['clean'];
        sqlsrv_query($connection, "INSERT INTO recordtypes (Description, RetainFor, Active, DepartmentID) VALUES ('$name',$retainfor,$active,$dept)");
        header('Location: index.php?pid=87'); exit;
    }
    
?>

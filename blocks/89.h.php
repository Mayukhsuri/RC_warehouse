<?php
    
    include('includes/ah_form.php');
    
    if(isset($_GET['rec']) && is_numeric($_GET['rec'])) {
        $rec = $_GET['rec'];
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM recordtypes WHERE ID=$rec"));
        if(!$row) {header('Location: index.php?pid=87'); exit; }
        $currentdeptid = $row['DepartmentID'];
        $currentname = $row['Description'];
        $currentretainfor = $row['RetainFor'];
        $currentactivestate = $row['Active'];
        $default1 = ''; $default0 = ''; if($currentactivestate == 1) {$default1 = '1'; } else {$default0 = '1'; }
    } else {
        header('Location: index.php?pid=91'); exit;
    }
    
    $form = new ah_form('editrecordtype');
    $form->addTitle('Edit Record Type');
    $form->textInput('name','Name',$currentname);
    
    function deptselected($current,$selected) {if($current == $selected) {return 1; } else {return 0; } }
    $result = sqlsrv_query($connection, "SELECT * FROM departments WHERE Active=1 ORDER BY Name ASC");
    while($row = sqlsrv_fetch_array($result)) {$form->addoption($row['ID'],$row['Name'],deptselected($row['ID'],$currentdeptid)); }
    $form->selectInput('department','Department');
    
    $form->setValidationType(2);
    $form->textInput('retainfor','Retention (Years)',$currentretainfor);
    
    $form->addOption(1,'Active',$default1);
    $form->addOption(0,'Inactive',$default0);
    $form->selectInput('active','Status');
    
    if($form->formValidated == 1) {
        $name = $form->validated['name']['clean'];
        $retainfor = $form->validated['retainfor']['clean'];
        $active = $form->validated['active']['clean'];
        $departmentid = $form->validated['department']['clean'];
        sqlsrv_query($connection, "UPDATE recordtypes SET Description='$name', RetainFor=$retainfor, Active=$active, DepartmentID=$departmentid WHERE ID=$rec");
        header('Location: index.php?pid=87'); exit;
    }
    
?>

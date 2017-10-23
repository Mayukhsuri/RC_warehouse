<?php
    
    include('includes/ah_form.php');
    
    if(isset($_GET['rec']) && is_numeric($_GET['rec'])) {
        $rec = $_GET['rec'];
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM departments WHERE ID=$rec"));
        if(!$row) {header('Location: index.php?pid=84'); exit; }
        $currentpropertyname = $row['Name'];
        $currentactivestate = $row['Active'];
        $default1 = ''; $default0 = ''; if($currentactivestate == 1) {$default1 = '1'; } else {$default0 = '1'; }
    } else {
        header('Location: index.php?pid=91'); exit;
    }
    
    $form = new ah_form('editdepartment');
    $form->addTitle('Edit Department');
    $form->textInput('name','Department Name',$currentpropertyname);
    $form->addOption(1,'Active',$default1);
    $form->addOption(0,'Inactive',$default0);
    $form->selectInput('active','Status');
    
    if($form->formValidated == 1) {
        $name = $form->validated['name']['clean'];
        $active = $form->validated['active']['clean'];
        sqlsrv_query($connection, "UPDATE departments SET Name='$name', Active=$active WHERE ID=$rec");
        header('Location: index.php?pid=84'); exit;
    }
    
?>

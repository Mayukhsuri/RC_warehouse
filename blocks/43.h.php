<?php
    
    include('includes/ah_form.php');
    
    if(isset($_GET['rec']) && is_numeric($_GET['rec'])) {
        $boxid = $_GET['rec'];
        $row = sqlsrv_fetch_array(sqlsrv_query("SELECT * FROM boxes WHERE ID=$boxid AND StartedBy=$userid"));
        if(!$row) {header('Location: index.php?pid=40'); exit; }
        $currentname = $row['Name'];
        $currentlocation = $row['LocationID'];
    } else {
        header('Location: index.php?pid=40'); exit;
    }
    
    $form = new ah_form('editbox');
    $form->addTitle('Edit Box Information');
    $form->textInput('name','Description',$row['Name']);
    $userlocationsarray = explode('|',$userlocations);
    foreach($userlocationsarray as $value) {
        $row = sqlsrv_fetch_array(sqlsrv_query("SELECT Name FROM locations WHERE ID=$value"));
        if($currentlocation == $value) {$selected = 1; } else {$selected = 0; }
        $form->addOption($value,$row['Name'],$selected);
    }
    $form->selectInput('boxlocation','Box Location');
    
    if($form->formValidated == 1) {
        $name = $form->validated['name']['clean'];
        $time = time();
        $location = $form->validated['boxlocation']['clean'];
        sqlsrv_query("UPDATE boxes SET Name='$name', LocationID=$location, LastActivity=$time WHERE ID=$boxid");
        header('Location: index.php?pid=42&rec=' . $boxid); exit;
    }
    
?>

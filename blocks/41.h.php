<?php
    
    write_log(__FILE__,__LINE__);

    include('includes/ah_form.php');
    
    $form = new ah_form('createbox');
    $form->addTitle('Create Box');
    $form->textInput('name','Description');
    write_log(__FILE__,__LINE__,'User Locations: ' . $userlocations);

    if (strlen($userlocations) < 1) {
        echo '<h3>The usergroup you are in does not have a "Can Create From" location set</h3><br>';
        echo '<p>Please contact an administrator with this message</p>';
        echo '<p>Use the back button to return to the application</p>';
        exit;
        //header('Location: index.php?pid=1'); exit;
    } 

    $userlocationsarray = explode('|',$userlocations);

    foreach($userlocationsarray as $value) {
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT Name FROM locations WHERE ID=$value"));
        $form->addOption($value,$row['Name']);
    }
    $form->selectInput('boxlocation','Box Location');
    
    if($form->formValidated == 1) {
        
        $name = $form->validated['name']['clean'];
        $time = time();
        $token = $time . generatetoken(25);
        $location = $form->validated['boxlocation']['clean'];
        sqlsrv_query($connection, "INSERT INTO boxes (Token, Barcode, Name, Status, LocationID, LastActivity, StartedBy, IronMtnBox, DestroyDate, Comments) VALUES ('$token', 'B0', '$name', 'P', $location, $time, $userid , 0, 0, '')");
       $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT ID FROM boxes WHERE Token='$token'"));
       $newid = $row['ID'];
       $code = 'B' . str_repeat('0',8-strlen($newid)) . $newid;
       sqlsrv_query($connection, "UPDATE boxes SET Barcode='$code' WHERE ID=$newid");
       header('Location: index.php?pid=42&rec=' . $newid); exit;
    
    }
    
?>

<?php
    
    include('includes/ah_form.php');
    
    $deviceprefix = 'EOFRCWD';
    
    $form = new ah_form('adddevice');
    $form->addTitle('Add Device');
    $form->addInstructions($connection, "Select a UNIQUE name for your device (i.e. Courier iPhone 2)");
    $form->textInput('name','Device Name');
    $form->addOption(1,'Courier Device');
    $form->addOption(2,'RC Device');
    $form->selectInput('type','Application Type');
    $form->addSpacer();
    
    if($form->formValidated == 1) {
        $foundnewtoken = 0;
        while($foundnewtoken == 0) {
            $token = strtoupper(generateToken(4));
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM devices WHERE Token='$token'"));
            if(!$row) {$foundnewtoken = 1; }
        }
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT MAX(ID) FROM devices"));
        if(!$row) {$nextid = 1; } else {$nextid = $row['MAX(ID)'] + 1; }
        $deviceid = $deviceprefix . str_repeat('0',(7 - strlen($nextid))) . $nextid;
        $devicename = $form->validated['name']['clean'];
        $apptypeid = $form->validated['type']['clean'];
        sqlsrv_query($connection, "INSERT INTO devices (DeviceID, DeviceName, ApplicationType, Token, ActivateDate, Active) VALUES ('$deviceid', '$devicename', $apptypeid, '$token', -1, 1)");
        header('Location: index.php?pid=53&did=' . $nextid);
        exit;
    }
    
?>

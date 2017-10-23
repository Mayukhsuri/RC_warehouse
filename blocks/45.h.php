<?php
    
write_log(__FILE__,__LINE__);

    if(isset($_GET['lg']) || isset($_POST['formsubmitted'])) {
        
        $checktask = 0;
        
        include('includes/ah_form.php');
        
        $sql = "SELECT ID, Name FROM departments";
        write_log(__FILE__,__LINE__, $sql);

        $result9 = sqlsrv_query($connection, $sql);
        write_log(__FILE__,__LINE__, 'Result Type: ' . gettype($result9));

        while($row9 = sqlsrv_fetch_array($result9)) {
            $rdeptname[$row9['ID']] = $row9['Name'];
        }
        
        $form = new ah_form('createlabels');
        $form->addTitle('Create RC Labels');
        $userlocationsarray = explode('|',$userlocations);

        foreach($userlocationsarray as $value) {
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT Name FROM locations WHERE ID=$value"));
            $form->addOption($value,$row['Name']);
        }

        $form->selectInput('boxlocation','Location');
        $rtarray = explode('|',$userrecordtypes);
        foreach($rtarray as $value) {
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT Description, DepartmentID FROM recordtypes WHERE ID=$value"));
            $form->addOption($value,$row['Description'] . ' (' . $rdeptname[$row['DepartmentID']] . ')');
        }
        $form->selectInput('recordtype','Record Type');
        $form->setValidationType(2);
        $form->textInput('labelcount','Label Count');
        
        if($form->formValidated == 1) {
            
            $errorcount = 0;
            $locationid = $form->validated['boxlocation']['clean'];
            $labelcount = $form->validated['labelcount']['clean'];
            $recordtype = $form->validated['recordtype']['clean'];
            if($labelcount<0 || $labelcount>100) {$errorcount++; $ALERT_WARNING = 'You may not generate more than 100 labels at a time.'; }
            
            if($errorcount == 0) {
                $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT DepartmentID FROM recordtypes WHERE ID=$recordtype"));
                $deptid = $row['DepartmentID'];
                $timestamp = time();
                $token = $timestamp . generatetoken(20);
                $query = "INSERT INTO boxes (Token, Name, Barcode, Status, LocationID, LastActivity, DestroyDate, StartedBy, Comments, IronMtnBox) VALUES (
                 '$token', 'RC PRE-GENERATED BOX', 'PENDING', 'P', $locationid, $timestamp, 0, $userid, '', 0)";
                for($i=1; $i<=$labelcount; $i++) {sqlsrv_query($connection, $query); }
                $result = sqlsrv_query($connection, "SELECT * FROM boxes WHERE Token='$token'");
                while($row = sqlsrv_fetch_array($result)) {
                    $tempid = $row['ID'];
                    $barcode = 'B' . str_repeat('0',(8-strlen($tempid))) . $tempid;
                    sqlsrv_query($connection, "UPDATE boxes SET Barcode='$barcode' WHERE ID=$tempid");
                    $query = "INSERT INTO records (BoxID, PropertyID, DepartmentID, RecordTypeID, StartDate, EndDate, DestructionDate, Active, ContactPerson) VALUES
                    ($tempid, -1, $deptid, $recordtype, -1, -1, -1, 0, '')";
                    sqlsrv_query($connection, $query);
                }
                header('Location: printfiles/RCBoxLabels.php?token=' . $token); exit;
            }
        
        }
        
    } else {
        
        $checktask = 1;
        
    }
    
?>

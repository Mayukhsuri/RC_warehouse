<?php
    
    if(isset($_GET['rec']) && is_numeric($_GET['rec'])) {
        $boxid = $_GET['rec'];
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM boxes WHERE ID=$boxid AND Status='P' AND StartedBy=$userid"));
        if(!$row) {header('Location: index.php?pid=40'); exit; }
        $boxcode = $row['Barcode'];
        $boxname = $row['Name'];
        $startedbyid = $row['StartedBy'];
        $locationid = $row['LocationID'];
    } else {
        header('Location: index.php?pid=40'); exit;
    }
    
    if(isset($_GET['del']) && is_numeric($_GET['del'])) {
        $recordtodelete = $_GET['del'];
        if(isset($_GET['con']) && is_numeric($_GET['con'])) {
            if($_GET['con'] == 1) {
                sqlsrv_query($connection, "UPDATE records SET Active=0 WHERE BoxID=$boxid AND ID=$recordtodelete");
            } else {
                $ALERT_MESSAGE = 'Are you sure you want to remove this record?  <a href="index.php?pid=42&rec=' . $boxid . '&del=' . $recordtodelete . '&con=1">Yes</a>';
                $ALERT_MESSAGE .= '<a href="index.php?pid=42&rec=' . $boxid . '">No</a>';
            }
        }
    }
    
    if(isset($_GET['com']) && is_numeric($_GET['com'])) {
        $row3 = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM records WHERE BoxID=$boxid AND Active=1"));
        
        if($row3) {
            if(isset($_GET['con']) && is_numeric($_GET['con'])) {
                if($_GET['con'] == 1) {
                    sqlsrv_query($connection, "UPDATE boxes SET Status='O' WHERE ID=$boxid");
                    $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM boxes WHERE ID=$boxid"));
                    $barcode = $row['Barcode'];
                    $location = 'LCN' . str_repeat('0',6-strlen($row['LocationID'])) . $row['LocationID'];
                    $timestamp = time();
                    $query = "INSERT INTO transactions (Barcode, Location, BatchID, Timestamp, DeviceID, Active) VALUES (";
                    $query .= "'$barcode', '$location', -1, $timestamp, -1, 1)";
                    sqlsrv_query($connection, $query);
                    header('Location: index.php?pid=40&pl=' . $boxid); exit;
                } else {
                    $ALERT_MESSAGE = 'Are you sure you want to complete this box?  <a href="index.php?pid=42&rec=' . $boxid . '&com=1&con=1">Yes</a>';
                    $ALERT_MESSAGE .= '<a href="index.php?pid=42&rec=' . $boxid . '">No</a>';
                }
            }
        } else {
            $ALERT_WARNING = "You must add at least one record to a box before marking it complete.";
        }
    }
    
    $result2 = sqlsrv_query($connection, "SELECT ID, Description FROM recordtypes WHERE Active=1");
    while($row2 = sqlsrv_fetch_array($result2)) {$recordtypename[$row2['ID']] = $row2['Description']; }
    
    $result2 = sqlsrv_query($connection, "SELECT ID, Code, Name FROM properties WHERE Active=1");
    while($row2 = sqlsrv_fetch_array($result2)) {$propertyname[$row2['ID']] = $row2['Code']; $propertynamefull[$row2['ID']] = $row2['Code'] . ' - ' . $row2['Name']; }
    
    include('includes/ah_form.php');
    $form = new ah_form('addrecord');
    $form->addTitle('Add Record to Box');
    $rctarray = explode('|',$userrecordtypes);
    foreach($rctarray as $rcttemp) {if(array_key_exists($rcttemp,$recordtypename)) {$form->addOption($rcttemp,$recordtypename[$rcttemp]); } }
    $form->selectInput('recordtype','Record Type');
    $proparray = explode('|',$userproperties);
    foreach($proparray as $proptemp) {if(array_key_exists($proptemp,$propertyname)) {$form->addOption($proptemp,$propertynamefull[$proptemp]); } }
    $form->addOption(-1,'---------------');
    $form->addOption(999999999,"All Properties");
    $form->selectInput('property','Property');
    $form->setDateRange((date('Y',time()) - 7),date('Y',time()));
    $form->datePickerinput('startdate','Start Date');
    $form->datePickerinput('enddate','End Date');
    
    if($form->formValidated == 1) {
        $currenttime = time();
        $propertyid = $form->validated['property']['clean'];
        $recordtypeid = $form->validated['recordtype']['clean'];
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT RetainFor, DepartmentID FROM recordtypes WHERE ID=$recordtypeid"));
        $departmentid = $row['DepartmentID'];
        $startdate = $form->validated['startdate']['clean'];
        $enddate = $form->validated['enddate']['clean'];
        $destructiondate = $enddate + ($row['RetainFor'] * 365.25);
        if($propertyid == 999999999) {
            foreach($proparray as $proptemp) {if(array_key_exists($proptemp,$propertyname)) {
                    $query = "INSERT INTO records (BoxID, PropertyID, DepartmentID, RecordTypeID, StartDate, EndDate, DestructionDate, Active) VALUES (";
                    $query .= "$boxid, $proptemp, $departmentid, $recordtypeid, $startdate, $enddate, $destructiondate, 1)";
                    sqlsrv_query($connection, $query);
                }
            }
        } else {

            $UID = $_SESSION['rcw_currentUserID'];

            $fields = array('BoxID',
                            'PropertyID',
                            'DepartmentID',
                            'RecordTypeID',
                            'StartDate',
                            'EndDate',
                            'DestructionDate',
                            'Active',
                            'ContactPerson'
                            );
            $values = array("$boxid",
                            "$propertyid",
                            "$departmentid",
                            "$recordtypeid",
                            "$startdate",
                            "$enddate",
                            "$destructiondate",
                            "1",
                            "'$UID'"
                            );
            $query = "INSERT INTO records (" . join(', ',$fields) . ") values (" . join(', ',$values) . ")";
            sqlsrv_query($connection, $query);
        }
        header('Location: index.php?pid=42&rec=' . $boxid); exit;
    }
    
?>

<?php
    
   // include('includes/connection.php');
    include('includes/ah_form.php');
    
    if(isset($_GET['barcode'])) {
        $barcode = cleantext($_GET['barcode']);
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT ID, Barcode FROM boxes WHERE Barcode='$barcode' AND Status='P'"));


        $sql = "SELECT count(*) as ROW_COUNT FROM boxes WHERE Barcode='$barcode' AND Status='P'";
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
        $row_count = $row['ROW_COUNT'];


        $sql = "SELECT ID, Barcode FROM boxes WHERE Barcode='$barcode' AND Status='P'";
        $result = sqlsrv_query($connection, $sql); 

        if($row_count > 1) {
            $_SESSION['rcw_ALERT_WARNING'] = 'Box [' . $barcode . '] not found in system or not in PENDING status.';
            header('Location: index.php?pid=46'); exit;
        } else {
            $boxid = $row['ID'];
        }
    }
    
    $form = new ah_form('finalizercbox');
    $form->addTitle('Finalize RC Box');
    
    $result = sqlsrv_query($connection, "SELECT ID, Code, Name FROM properties");
    while($row = sqlsrv_fetch_array($result)) {
        $propertycode[$row['ID']] = $row['Code'] . ' - ' . $row['Name'];
        $form->addOption($row['ID'],$propertycode[$row['ID']]);
    }
    $form->selectInput('prop','Property');
    $startdaterange = date('Y',time()) - 3;
    $enddaterange = date('Y',time()) + 1;
    $form->setDateRange($startdaterange,$enddaterange);
    $form->datePickerInput('start','Start Range',1);
    $form->datePickerInput('end','End Range',1);
    $form->addSpacer();
    
    if($form->formValidated == 1) {
        $propertyid = $form->validated['prop']['clean'];
        $startdate = $form->validated['start']['clean'];
        $enddate = $form->validated['end']['clean'];
        $currenttime = time();
        if($startdate < $enddate) {
            sqlsrv_query($connection, "UPDATE records SET PropertyID=$propertyid, StartDate=$startdate, EndDate=$enddate, Active=1 WHERE BoxID=$boxid") or die(sqlsrv_errors());
            sqlsrv_query($connection, "UPDATE boxes SET Status='O', LastActivity='$currenttime' WHERE ID=$boxid");
            $_SESSION['rcw_ALERT_CONFIRM'] = 'Box [' . $barcode . '] successfully finalized in system!';
            header('Location: index.php?pid=46'); exit;
        } else {
            $ALERT_WARNING = 'Record range [Start Date] must be earlier than [End Date].  Unable to continue.';
        }
    }
    
?>

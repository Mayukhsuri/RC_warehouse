<?php

    //include('includes/connection.php');
    include('includes/ah_form.php');
    
    $result9 = sqlsrv_query($connection, "SELECT ID, Name FROM departments");
    while($row9 = sqlsrv_fetch_array($result9)) {$rdeptname[$row9['ID']] = $row9['Name']; }
    
    $form = new ah_form('searchrecords');
    $form->addTitle('Search Records');
    
    $form->addOption(0,'All Properties');
    $result = sqlsrv_query($connection, "SELECT * FROM properties WHERE Active=1 ORDER BY Name ASC");
    while($row=sqlsrv_fetch_array($result)) {$form->addOption($row['ID'],$row['Code'] . ' - ' . $row['Name']); }
    $form->selectInput('property','Property');
    
    $form->addOption(0,'All Record Types');
    $result = sqlsrv_query($connection, "SELECT * FROM recordtypes WHERE Active=1 ORDER BY Description ASC");
    while($row=sqlsrv_fetch_array($result)) {$form->addOption($row['ID'],$row['Description'] . ' (' . $rdeptname[$row['DepartmentID']] . ')'); }
    $form->selectInput('recordtype','Record Type');
    
    $form->datePickerInput('start','Start Range',1);
    $form->datePickerInput('end','End Range',1);
    
    if($form->formValidated == 1) {
        $property = $form->validated['property']['clean'];
        $recordtype = $form->validated['recordtype']['clean'];
        $start = $form->validated['start']['clean'];
        $end = $form->validated['end']['clean'];
        if($property != 0) {$propquery = " AND PropertyID=" . $property; } else {$propquery = ""; }
        if($recordtype != 0) {$rtquery = " AND RecordTypeID=" . $recordtype; } else {$rtquery = ""; }
        $query = "SELECT * FROM records WHERE EndDate>=$start AND StartDate<=$end" . $propquery . $rtquery . " ORDER BY StartDate ASC";
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, $query));
        if($row) {
            header('Location: index.php?pid=32&prop=' . $property . '&rt=' . $recordtype . '&start=' . $start . '&end=' . $end); exit;
        } else {
            $ALERT_WARNING = "No records found for search criteria given.";
        }
    }
    
?>

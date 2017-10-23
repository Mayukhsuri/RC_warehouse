<?php
    
    include('includes/ah_form.php');
    
    function kick() {header('Location: index.php?pid=51'); exit; }
    
    if(!isset($_GET['did']) || !is_numeric($_GET['did'])) {kick(); }
    $deviceid = $_GET['did'];
    $row = sqlsrv_fetch_array(sqlsrv_query("SELECT * FROM devices WHERE ID=$deviceid"));
    if(!$row) {kick(); }
    $curstat = $row['Active'];
    $devidtext = $row['DeviceID'];
    $devname = $row['DeviceName'];
    $apptypetext = 'Undefined';
    if($row['ApplicationType'] == 1) {$apptypetext = 'Courier Device'; }
    if($row['ApplicationType'] == 2) {$apptypetext = 'RC Device'; }
    $activetext = 'Active';
    if($row['Active'] != 1) {$activetext = 'Inactive'; }
    if($row['ActivateDate'] == -1) {$activetext = 'Unregistered'; $adatetext = 'N/A'; } else {$adatetext = date('n/j/Y g:ia',$row['ActivateDate']); }
    
    function checkselect($a,$b) {if($a == $b) {return 1; } else {return 0; } }
    $form = new ah_form('updatedevice');
    $form->addOption(1,'Active',checkselect(1,$curstat));
    $form->addOption(0,'Inactive',checkselect(0,$curstat));
    $form->selectInput('status','Device Status');
    
    if($form->formValidated == 1) {
        $newstatus = $form->validated['status']['clean'];
        sqlsrv_query("UPDATE devices SET Active=$newstatus WHERE ID=$deviceid");
        header('Location: index.php?pid=54&did=' . $deviceid . '&upd=1');
        exit;
    }
    
?>

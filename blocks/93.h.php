<?php
    
    include('includes/ah_form.php');
    
    if(isset($_GET['rec']) && is_numeric($_GET['rec'])) {
        $rec = $_GET['rec'];
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM usergroups WHERE ID=$rec"));
        if(!$row) {header('Location: index.php?pid=91'); exit; }
        $currentgroupname = $row['GroupName'];
        $currentactivestate = $row['Active'];
        $permissionsarray = explode('|',$row['Permissions']);
        $urecordtypesarray = explode('|',$row['RecordTypes']);
        $upropertiesarray = explode('|',$row['Properties']);
        $ulocationsarray = explode('|',$row['Locations']);
        $default1 = ''; $default0 = ''; if($currentactivestate == 1) {$default1 = '1'; } else {$default0 = '1'; }
    } else {
        header('Location: index.php?pid=91'); exit;
    }
    
    $form = new ah_form('editusergroup');
    $form->addTitle('Edit User Group');
    $form->textInput('groupname','Group Name',$currentgroupname);
    $form->addOption(1,'Active',$default1);
    $form->addOption(0,'Inactive',$default0);
    $form->selectInput('active','Status');
    
    $form->addSpacer();
    foreach($menuitem as $key=>$value) {
        $checked = 0; if(in_array($value[0],$permissionsarray)) {$checked = 1; }
        $form->addOption($key,$value[4],$checked);
    }
    $form->checkInput('permissions','Permissions');
    
    $form->addSpacer();
    $result2 = sqlsrv_query($connection, "SELECT * FROM recordtypes WHERE Active=1 ORDER BY Description ASC");
    while($row2 = sqlsrv_fetch_array($result2)) {
        $checked = 0; if(in_array($row2['ID'],$urecordtypesarray)) {$checked = 1; }
        $form->addOption($row2['ID'],$row2['Description'],$checked);
    }
    $form->checkInput('recordtypes','Record Types');
    
    $form->addSpacer();
    $result2 = sqlsrv_query($connection, "SELECT * FROM properties WHERE Active=1 ORDER BY Code ASC");
    while($row2 = sqlsrv_fetch_array($result2)) {
        $checked = 0; if(in_array($row2['ID'],$upropertiesarray)) {$checked = 1; }
        $form->addOption($row2['ID'],$row2['Code'] . ' - ' . $row2['Name'],$checked);
    }
    $form->checkInput('properties','Casinos');
    $form->addSpacer();
    
    $form->addSpacer();
    $result2 = sqlsrv_query($connection, "SELECT * FROM locations WHERE LocationType='O' AND Active=1 ORDER BY Name ASC");
    while($row2 = sqlsrv_fetch_array($result2)) {
        $checked = 0; if(in_array($row2['ID'],$ulocationsarray)) {$checked = 1; }
        $form->addOption($row2['ID'],$row2['Name'],$checked);
    }
    $form->checkInput('locations','Can Create From');
    $form->addSpacer();
    
    
    if($form->formValidated == 1) {
        $groupname = $form->validated['groupname']['clean'];
        $active = $form->validated['active']['clean'];
        
        $permissions = '0|1|2|3|4|5|6|7|8|9|';
        foreach($form->validated['permissions']['clean'] as $key=>$value) {
            $permissions .= $menuitem[$key][3] . '|';
        }
        if(strlen($permissions) > 0) {$permissions = substr($permissions,0,strlen($permissions)-1); }
        
        $recordtypes = '';
        foreach($form->validated['recordtypes']['clean'] as $key=>$value) {
            $recordtypes .= $key . '|';
        }
        if(strlen($recordtypes) > 0) {$recordtypes = substr($recordtypes,0,strlen($recordtypes)-1); }
        
        $properties = '';
        foreach($form->validated['properties']['clean'] as $key=>$value) {
            $properties .= $key . '|';
        }
        if(strlen($properties) > 0) {$properties = substr($properties,0,strlen($properties)-1); }
        
        $locations = '';
        foreach($form->validated['locations']['clean'] as $key=>$value) {
            $locations .= $key . '|';
        }
        if(strlen($locations) > 0) {$locations = substr($locations,0,strlen($locations)-1); }
        
        sqlsrv_query($connection, "UPDATE usergroups SET GroupName='$groupname', Permissions='$permissions', RecordTypes='$recordtypes', Properties='$properties', Locations='$locations', Active=$active WHERE ID=$rec");
        header('Location: index.php?pid=91'); exit;
    }
    
?>

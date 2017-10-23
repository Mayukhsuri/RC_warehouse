<?php
    
    include('includes/ah_form.php');
    
    if(isset($_GET['rec']) && is_numeric($_GET['rec'])) {
        $rec = $_GET['rec'];
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM users WHERE ID=$rec"));
        if(!$row) {header('Location: index.php?pid=96'); exit; }
        $employeeid = $row['EmployeeID'];
        $firstname = $row['FirstName'];
        $lastname = $row['LastName'];
        $usergroupid = $row['UserGroupID'];
        $currentactivestate = $row['Active'];
        $default1 = ''; $default0 = ''; if($currentactivestate == 1) {$default1 = '1'; } else {$default0 = '1'; }
    } else {
        header('Location: index.php?pid=96'); exit;
    }
    
    $form = new ah_form('edituser');
    $form->addTitle('Edit User - ' . $employeeid);
    $form->textInput('firstname','First Name',$firstname);
    $form->textInput('lastname','Last Name',$lastname);
    $result = sqlsrv_query($connection, "SELECT * FROM usergroups WHERE Active=1 AND SysAdmin=0 ORDER BY GroupName ASC");
    while($row = sqlsrv_fetch_array($result)) {
        if($usergroupid == $row['ID']) {$setcheck = 1; } else {$setcheck = ''; }
        $form->addOption($row['ID'],$row['GroupName'],$setcheck);
    }
    $form->addOption(-1,'------------------');
    $result = sqlsrv_query($connection, "SELECT * FROM usergroups WHERE Active=1 AND SysAdmin=1 ORDER BY GroupName ASC");
    while($row = sqlsrv_fetch_array($result)) {
        if($usergroupid == $row['ID']) {$setcheck = 1; } else {$setcheck = ''; }
        $form->addOption($row['ID'],$row['GroupName'],$setcheck);
    }
    $form->selectInput('usergroup','User Group');
    $form->addOption(1,'Active',$default1);
    $form->addOption(0,'Inactive',$default0);
    $form->selectInput('active','Status');
    $form->addSpacer();
    $form->addOption(0,'No');
    $form->addOption(1,'Yes');
    $form->selectInput('resetpw','Reset Password?');
    $form->textInput('password','New Password','Password1');
    $form->addSpacer();
    
    if($form->formValidated == 1) {
        if($form->validated['resetpw']['clean'] == 1) {
            $resetdate = time();
            $password = hashup($bigpepper,$form->validated['password']['clean']);
            sqlsrv_query($connection, "UPDATE users SET Password='$password', PasswordResetDate=-1 WHERE ID=$rec");
        }
        $firstname = $form->validated['firstname']['clean'];
        $lastname = $form->validated['lastname']['clean'];
        $usergroupid = $form->validated['usergroup']['clean'];
        $active = $form->validated['active']['clean'];
        sqlsrv_query($connection, "UPDATE users SET FirstName='$firstname', LastName='$lastname', UserGroupID=$usergroupid, Active=$active WHERE ID=$rec");
        header('Location: index.php?pid=96'); exit;
    }
    
?>

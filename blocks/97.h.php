<?php
    
    include('includes/ah_form.php');
    
    $eeidvalid = 0;
    $isgroupid = 0;
    if(isset($_POST['employeeid'])) {
        if(is_numeric($_POST['employeeid'])) {
            if(strlen($_POST['employeeid']) == 9) {
                $testeeid = $_POST['employeeid'];
                $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM users WHERE EmployeeID=$testeeid"));
                if(!$row) {$eeidvalid = 1; }
            } elseif (strlen($_POST['employeeid']) == 4) {
                $testeeid = $_POST['employeeid'];
                $isgroupid = 1;
                $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM users WHERE EmployeeID=$testeeid"));
                if(!$row) {$eeidvalid = 1; }
            }
        }
    }
    
    $nextgroupnumber = 1000;
    $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT MAX(EmployeeID) AS empid FROM users WHERE EmployeeID<10000 AND EmployeeID>999"));
    if($row && $row['empid'] != 0) {$nextgroupnumber = $row['empid'] + 1; }
    if($isgroupid == 1) {
        if($_POST['employeeid'] != $nextgroupnumber) {
            $eeidvalid = 0;
        }
    }
    
    
    $form = new ah_form('adduser');
    $form->addTitle('Add User');
    $form->addInstructions('Use 800# for individual employees or ' . $nextgroupnumber . ' for group accounts.  Also for group accounts, use GROUP as first name.');
    $form->textInput('employeeid','Employee ID');
    $form->textInput('firstname','First Name');
    $form->textInput('lastname','Last Name');
    $result = sqlsrv_query($connection, "SELECT * FROM usergroups WHERE Active=1 AND SysAdmin=0 ORDER BY GroupName ASC");
    while($row = sqlsrv_fetch_array($result)) {$form->addOption($row['ID'],$row['GroupName']); }
    $form->addOption(-1,'------------------');
    $result = sqlsrv_query($connection, "SELECT * FROM usergroups WHERE Active=1 AND SysAdmin=1 ORDER BY GroupName ASC");
    while($row = sqlsrv_fetch_array($result)) {$form->addOption($row['ID'],$row['GroupName']); }
    $form->selectInput('usergroup','User Group');
    $form->textInput('password','Initial Password','Password1');
    $form->addOption(1,'Active');
    $form->addOption(0,'Inactive');
    $form->selectInput('active','Status');
    
    if($form->formValidated == 1) {
        if($eeidvalid == 1) {
            $employeeid = $testeeid;
            $firstname = $form->validated['firstname']['clean'];
            $lastname = $form->validated['lastname']['clean'];
            $usergroup = $form->validated['usergroup']['clean'];
            $password = hashup($bigpepper,$form->validated['password']['clean']);
            $passwordresetdate = time();
            $active = $form->validated['active']['clean'];
            $query = "INSERT INTO users (EmployeeID, FirstName, LastName, UserGroupID, Password, PasswordResetDate, Active) VALUES (
               $employeeid, '$firstname', '$lastname', $usergroup, '$password', -1, $active)";
            sqlsrv_query($connection, $query);
            header('Location: index.php?pid=96'); exit;
        } else {
            $ALERT_WARNING = "Invalid user ID.";
        }
    }
    
?>

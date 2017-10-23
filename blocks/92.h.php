<?php
    
    include('includes/ah_form.php');
    
    $form = new ah_form('addusergroup');
    $form->addTitle('Add User Group');
    $form->textInput('groupname','Group Name');
    $form->addOption(1,'Active');
    $form->addOption(2,'Inactive');
    $form->selectInput('active','Status');
    
    if($form->formValidated == 1) {
        $groupname = $form->validated['groupname']['clean'];
        $active = $form->validated['active']['clean'];
        $permissions = '0|1|2|3|4|5|6|7|8|9|';
        sqlsrv_query($connection, "INSERT INTO usergroups (GroupName, Permissions, Active) VALUES ('$groupname','$permissions',$active)");
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM usergroups WHERE GroupName='$groupname' ORDER BY ID DESC"));
        $newgroup = $row['ID'];
        header('Location: index.php?pid=93&rec=' . $newgroup); exit;
    }
    
?>

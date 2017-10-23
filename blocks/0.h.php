<?php
    
    include('includes/ah_form.php');
    
    $passverify = '';
    if(isset($_POST['userid'])) {
        if(is_numeric($_POST['userid'])) {
            $userid = $_POST['userid'];

            $sql = "SELECT * FROM users WHERE EmployeeID=$userid";
        //    write_log(__FILE__,__LINE__,$sql);
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));

            $passverify = $row['Password'];
            $userFirst = $row['FirstName'];
            $userLast = $row['LastName'];
            $userGroupID = $row['UserGroupID'];
            $passwordResetDate = $row['PasswordResetDate'];
            $userActive = $row['Active'];
        }
    }
    
    $form = new ah_form('login');
    $form->addTitle('System Login');
    $form->textInput('userid','User ID');
    $form->passwordInput('password','Password',$bigpepper,$passverify);
    
    if($form->formValidated == 1) {
        $_SESSION['rcw_currentUserID'] = $userid;
        $_SESSION['rcw_userFirst'] = $userFirst;
        $_SESSION['rcw_userLast'] = $userLast;
        $_SESSION['rcw_userFullName'] = $userFirst . ' ' . $userLast;
        $sql = "SELECT * FROM usergroups WHERE ID=$userGroupID";
       // write_log(__FILE__,__LINE__,$sql);

        $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));

        $_SESSION['rcw_userPermissions'] = $row['Permissions'];
        $_SESSION['rcw_userLocations'] = $row['Locations'];
        $_SESSION['rcw_userRecordTypes'] = $row['RecordTypes'];
        $_SESSION['rcw_userProperties'] = $row['Properties'];
        $_SESSION['rcw_userDepartments'] = $row['Departments'];
        $_SESSION['rcw_userLoggedIn'] = 1;
        $_SESSION['rcw_userLastActivity'] = time();
        $_SESSION['rcw_userPasswordResetDate'] = $passwordResetDate;
        header('Location: index.php?pid=1'); exit;
    }
    
?>

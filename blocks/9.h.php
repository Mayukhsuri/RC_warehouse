<?php
    
    include('includes/ah_form.php');
    
    $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM users WHERE EmployeeID=$userid"));
    $passverify = $row['Password'];
    
    $form = new ah_form('login');
    $form->addTitle('Change Password');
    $form->passwordInput('currentpass','Current Password',$bigpepper,$passverify);
    $form->addSpacer();
    $form->setPasswordValidation(7,2);
    $form->passwordSetInput('newpass','New Password',$bigpepper);
    $form->addSpacer();
    
    if($form->formValidated == 1) {
        $passwordresetdate = time() + ($requirePasswordUpdateDays * 86400);
        $newpassword = $form->validated['newpass']['clean'];
        $query = "UPDATE users SET Password='$newpassword', PasswordResetDate=$passwordresetdate WHERE EmployeeID=$userid";
        sqlsrv_query($connection, $query);
        $_SESSION['rcw_userPasswordResetDate'] = $passwordresetdate;
        header('Location: index.php?pid=1'); exit;
    }
    
?>

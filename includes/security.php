<?php
    
    $pageValidated = 0;
    $requirePasswordUpdateDays = 365;
    
    if(isset($_GET['pid'])) {
        if(is_numeric($_GET['pid'])) {
            $pidreq = $_GET['pid'];
            $pageValidated = 1;
        }
    }
    
    if($pidreq == -1) {
        unset($_SESSION['rcw_userLoggedIn']);
        session_destroy();
        session_start();
        header('Location: index.php?pid=0&loggedout=1'); exit;
    }
    
    if(isset($_GET['loggedout'])) {
        $ALERT_MESSAGE = "User successfully logged out of system.";
    }
    
    if(isset($pidreq) && $pidreq != 0) {
        
        if(!isset($_SESSION['rcw_userLoggedIn'])) {
            $pageValidated = 0;
            session_destroy();
            session_start();
            $ALERT_WARNING = "You must be logged in to use the system.";
        } else {
            $userid = $_SESSION['rcw_currentUserID'];
            $userfirst = $_SESSION['rcw_userFirst'];
            $userlast = $_SESSION['rcw_userLast'];
            $userfull = $userfirst . ' ' . $userlast;
            $userlastactivity = $_SESSION['rcw_userLastActivity'];
            $userpermissions = $_SESSION['rcw_userPermissions'];
            $userlocations = $_SESSION['rcw_userLocations'];
            $userproperties = $_SESSION['rcw_userProperties'];
            $userdepartments = $_SESSION['rcw_userDepartments'];
            $userrecordtypes = $_SESSION['rcw_userRecordTypes'];
          //  write_log(__FILE__,__LINE__,"User: " . $userfirst . ' ' . $userlast);

        }
        
        function pagePermissed($pid,$userpermissions) {
            $permissionsArray = explode('|',$userpermissions);
            $permissionFound = 0;
            foreach($permissionsArray as $value) {
                if($value == $pid) {$permissionFound = 1; }
            }
            if($permissionFound == 1) {return true; } else {return false; }
        }
        
        // ---------------------------------------------------------------------------------------------------- COMMENTED OUT FOR DEMO PRESENTATION ONLY ------<<
        if($pageValidated == 1) {
            if((time() - $userlastactivity) > 7200) {
                $pageValidated = 0;
                session_destroy();
                session_start();
                $ALERT_WARNING = "You have been logged out due to inactivity.";
            }
        }
        
        if($pageValidated == 1) {
            if(pagePermissed($pidreq,$userpermissions) == false) {
                $ALERT_WARNING = "You are not permitted to view the requested page.  Please see an administrator.";
                $pidreq = 1;
            }
        }
        
    }
    
    if($pageValidated == 1) {
        $pid = $pidreq;
        $_SESSION['rcw_userLastActivity'] = time();
        if(isset($_SESSION['rcw_userLoggedIn']) && time() > $_SESSION['rcw_userPasswordResetDate']) {
            $pid = 9;
            $ALERT_MESSAGE = "You must reset your password to continute using the system.";
        }
    } else {
        $pid = 0;
        session_destroy();
        session_start();
    }

?>

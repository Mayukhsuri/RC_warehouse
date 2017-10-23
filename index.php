<?php

if(!isset($_GET['pid'])) {
    header('Location: index.php?pid=0');
}

session_start();
require_once('includes/settings.php'); 
require_once('includes/g1functions.php');
require_once('includes/connection.php');


//write_log(__FILE__,__LINE__);

include('includes/security.php');
include('includes/directory.php');
    
include('blocks/' . $pid . '.h.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
 <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
 <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  
<head>
    <title>Record Warehouse Inventory System</title>
    <LINK REL=StyleSheet HREF="css/style.css" TYPE="text/css" MEDIA=screen />
</head>
    
<body>
    
        <div class="menubar">
            <h2>Record Warehouse Inventory System</h2> 
            <?php
                if(isset($_SESSION['rcw_userLoggedIn'])) {
                    echo '<a href="index.php?pid=-1">Logout</a>';
                    echo '<a href="index.php?pid=9">' . $_SESSION['rcw_userFullName'] . '</a>';
                }

            ?>
        </div>
        
        <?php
            if(isset($_SESSION['rcw_ALERT_CONFIRM'])) {$ALERT_CONFIRM = $_SESSION['rcw_ALERT_CONFIRM']; unset($_SESSION['rcw_ALERT_CONFIRM']); }
            if(isset($_SESSION['rcw_ALERT_MESSAGE'])) {$ALERT_MESSAGE = $_SESSION['rcw_ALERT_MESSAGE']; unset($_SESSION['rcw_ALERT_MESSAGE']); }
            if(isset($_SESSION['rcw_ALERT_WARNING'])) {$ALERT_WARNING = $_SESSION['rcw_ALERT_WARNING']; unset($_SESSION['rcw_ALERT_WARNING']); }
            if(isset($ALERT_CONFIRM)) {
                echo '<div class="main"><div class="column20"><div class="menublock"></div></div><div class="column60"><div class="contentblock CONFIRM">';
                echo $ALERT_CONFIRM . '</div></div></div>' . PHP_EOL;
            }
            if(isset($ALERT_MESSAGE)) {
                echo '<div class="main"><div class="column20"><div class="menublock"></div></div><div class="column60"><div class="contentblock MESSAGE">';
                echo $ALERT_MESSAGE . '</div></div></div>' . PHP_EOL;
            }
            if(isset($ALERT_WARNING)) {
                echo '<div class="main"><div class="column20"><div class="menublock"></div></div><div class="column60"><div class="contentblock WARNING">';
                echo $ALERT_WARNING . '</div></div></div>' . PHP_EOL;
            }
        ?>
        
        <div class="main">
        
            <div class="column20">
                <div class="menublock">

                    <?php echo $RCW_MENU; ?>

                </div>
            </div>
            
            <?php include('blocks/' . $pid . '.m.php'); ?>
        
        </div>
        
        <div class="footer"></div>
    
</body>
</html>
<?php
   //
   // Close the database connectionon exit
   //
   sqlsrv_close ( $connection )
?>

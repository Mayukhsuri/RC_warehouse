<?php

  //require("private/dbconstants.php");

//write_log(__FILE__,__LINE__,"Connection User: " . get_current_user());

$bigpepper = $INI['salt'];
$connectionInfo = array( "Database"=>$INI['dbname']);

$connection = sqlsrv_connect($INI['host'],$connectionInfo);
   
if (!$connection) {
    Die ("Connection to database failed: " . print_r(sqlsrv_errors()));
}

?>

<?php

//
// We need to ensure that the database connection is closed even
// when thee is an errror, so we set an error handler
//
function OnDie($errno, $errstr) {
  echo "<b>Error:</b> [$errno] $errstr<br>";
  echo "<p>" . print_r(debug_backtrace()) . "</p>";
  echo "Ending Script";
 // sqlsrv_close ( $connection );
  die();
}

set_error_handler('OnDie',E_ALL & ~E_NOTICE & ~E_USER_NOTICE);

//
// Load INI settings
//
$INI = parse_ini_file(__DIR__ . "/settings.ini");



//----------------------------------------------------------------------
// Constants and common variables
//----------------------------------------------------------------------

//
// Logging, based on INI settings
//
define('LOGFILE',$INI['logpath'] . '/' . $INI['logname'] . '-' . date('Y-m-d') . '.log');
if (strtolower($INI['writelog']) == 'true') {
    define('WRITELOG',true);
} else { 
    define('WRITELOG',false);
}

// Request status values
define('REQUEST_CANCELED',-1);
define('REQUEST_HOLD',0);
define('REQUEST_STANDARD',1);
define('REQUEST_URGENT',2);
define('REQUEST_PULLING',3);
define('REQUEST_ENROUTE',4);
define('REQUEST_DELIVERED',7);
define('REQUEST_RETURN_REQUEST',8);
define('REQUEST_COMPLETE',9);

// Box table status values
define('BOX_WAREHOUSE','W');   // Box is shelved in warehouse
define('BOX_COURIER','C');     // In the posession of a courier, in transit.
define('BOX_OUTLET','O');      // At an outlet, some place other than a warehouse
define('BOX_PENDING','P');     // In the process of being created, not yet finalized
define('BOX_TEMP', 'T');       // In a temporary staging/processing area
define('BOX_DESTROYED','D');   // Sent off for destruction

// Location table type values
define('LOC_WAREHOUSE','W');    // W - Warehouse, shelving area
define('LOC_COURIER','C');      // C - Courier -- Deprecated, we aren't considering people as locations, we can track this by the Badge ID from the scan
define('LOC_OUTLET','O');       // O - Outlet
define('LOC_TEMP','T');         // T - Temporary - temporary outlet, staging area, usually in a warehouse

?>
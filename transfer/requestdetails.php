<?php

    include('../includes/connection.php');
    include('../includes/g1functions.php');
    
    if(isset($_GET['id']) && is_numeric($_GET['id'])) {
        $rid = $_GET['id'];
    } else {die('REQUEST NOT FOUND.'); }
    
    // Perform Primary Request Query
    $requestresult = sqlsrv_query("SELECT * FROM requests WHERE ID=$rid");
    if(!$requestresult) {die('REQUEST NOT FOUND.'); }
    $row = sqlsrv_fetch_array($requestresult);
    $boxarray = explode(',',$row['BoxData']);
    
    // Create Box Data Query
    $boxarray = array_unique($boxarray);
    $boxrequestquery = "SELECT * FROM boxes WHERE";
    $boxrequestcount = 0;
    $recordrequestquery = "SELECT BoxID, RecordTypeID, StartDate, EndDate FROM records WHERE";
    foreach($boxarray as $tempbox) {
        $boxrequestcount++;
            if($boxrequestcount > 1) {$boxrequestquery .= " OR"; $recordrequestquery .= " OR"; }
            $boxrequestquery .= " ID=" . $tempbox;
            $recordrequestquery .= " BoxID=" . $tempbox;
    }
    
    // Get Box Data
    $boxrequestresult = sqlsrv_query($boxrequestquery);
    while($row = sqlsrv_fetch_array($boxrequestresult)) {
        $boxlocationid[$row['ID']] = $row['LocationID'];
        $locationarray[] = $row['LocationID'];
        $boxbarcode[$row['ID']] = $row['Barcode'];
        $boxstatus[$row['ID']] = $row['Status'];
        $boxlastactivity[$row['ID']] = $row['LastActivity'];
    }
    
    // Build Location Name Query
    $locationarray = array_unique($locationarray);
    $locationquery = "SELECT ID, Barcode, Name, LocationType FROM locations WHERE";
    $locationcount = 0;
    foreach($locationarray as $temploc) {
        $locationcount++;
        if($locationcount > 1) {$locationquery .= " OR"; }
        $locationquery .= " ID=" . $temploc;
    }
    $locationquery .= " ORDER BY LocationType DESC, Warehouse ASC, Row ASC, Bay ASC, Shelf ASC";
    
    // Get Location Data
    $locationresult = sqlsrv_query($locationquery);
    $locationcount = 0;
    while($row = sqlsrv_fetch_array($locationresult)) {
        $locationcount++;
        $locationid[$locationcount] = $row['ID'];
        $locationtitle[$locationcount] = $row['Name'];
        $locationbarcode[$row['ID']] = $row['Barcode'];
        $locationname[$row['ID']] = $row['Name'];
        $locationtype[$row['ID']] = $row['LocationType'];
    }
    
    // Get RecordType Data
    $recordtyperesult = sqlsrv_query("SELECT ID, Description FROM recordtypes");
    while($row = sqlsrv_fetch_array($recordtyperesult)) {$recordtypename[$row['ID']] = $row['Description']; }
    
    // Generate Record Data
    $recordrequestresult = sqlsrv_query($recordrequestquery);
    while($row = sqlsrv_fetch_array($recordrequestresult)) {
        $tempboxid = $row['BoxID'];
        $temprecordtype = $row['RecordTypeID'];
        $tempstartdate = date('n/j/Y',$row['StartDate']);
        $tempenddate = date('n/j/Y',$row['EndDate']);
        if($row['StartDate'] == -1) {$tempstartdate = 'N/A'; }
        if($row['EndDate'] == -1) {$tempenddate = 'N/A'; }
        if(!isset($recorddata[$tempboxid])) {
            $recorddata[$tempboxid] = '   ' . $recordtypename[$temprecordtype] . PHP_EOL . "   " . $tempstartdate . " - " . $tempenddate . PHP_EOL . PHP_EOL;
        } else {
            $recorddata[$tempboxid] .= '   ' . $recordtypename[$temprecordtype] . PHP_EOL . "   " . $tempstartdate . " - " . $tempenddate . PHP_EOL . PHP_EOL;
        }
    }
    
    // Generate Output from Primary Request Query
    $row = sqlsrv_fetch_array(sqlsrv_query("SELECT * FROM requests WHERE ID=$rid"));
    $boxarray = explode(',',$row['BoxData']);
    asort($boxarray);
    if($row['Status'] == 8) {
        echo '==================' . PHP_EOL;
        echo ' RETURN REQUESTED' . PHP_EOL;
        echo '==================' . PHP_EOL . PHP_EOL;
    }
    echo 'Request #: ' . $rid . PHP_EOL;
    if($row['Urgency'] == 2) {echo 'URGENT' . PHP_EOL; }
    echo 'Deliver to ' . $row['DeliverTo'] . PHP_EOL;
    echo $row['Location'] . PHP_EOL;
    echo 'Total boxes: ' . $row['BoxCount'] . PHP_EOL . PHP_EOL;
    if(strlen($row['Comments']) > 0) {echo str_replace('<br />',PHP_EOL,str_replace('<br/>',PHP_EOL,$row['Comments'])) . PHP_EOL . PHP_EOL; }
    for($i=1; $i<=$locationcount; $i++) {
        echo PHP_EOL;
        echo $locationbarcode[$locationid[$i]] . PHP_EOL . $locationtitle[$i] . PHP_EOL . '----------------------------------------' . PHP_EOL;
        foreach($boxarray as $boxrequested) {
            if($boxlocationid[$boxrequested] == $locationid[$i]) {
                echo ' - ' . $boxbarcode[$boxrequested] . PHP_EOL;
                echo $recorddata[$boxrequested];
            }
        }
        echo PHP_EOL;
    }
    
    echo PHP_EOL . PHP_EOL . ' - End of Request -' . PHP_EOL . PHP_EOL;
    
    

?>

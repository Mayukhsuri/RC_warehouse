<?php

    include('../includes/connection.php');
    include('../includes/g1functions.php');
    
    $propertybreak = '[::PROP::]';
    $databreak = '[::DATA::]';
    
    $output = '';
    $resultcount = 0;
    $result = sqlsrv_query($connection, "SELECT * FROM properties WHERE Active=1 ORDER BY Code ASC, Name ASC");
    while($row = sqlsrv_fetch_array($result)) {
        $resultcount++;
        if($resultcount > 1) {$output .= $propertybreak; }
        $output .= $row['ID'] . $databreak . $row['Code'] . ' - ' . $row['Name'];
    }
    
    echo $output;

?>

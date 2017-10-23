<?php

    include('../includes/connection.php');
    include('../includes/g1functions.php');
    
    $requestcount = 0;
    $result = sqlsrv_query($connection, "SELECT * FROM requests WHERE Status>0 AND Status<9 AND Status!=7 ORDER BY Urgency DESC, RequestTime ASC");
    while($row = sqlsrv_fetch_array($result)) {
        $requestcount++;
        if($requestcount > 1) {echo '[::REQBRK::]'; }
        echo $row['ID'] . '[::REQDATA::]';
        echo date('n/j/Y g:ia',$row['RequestTime']) . '[::REQDATA::]';
        if($row['BoxCount'] == 1) {$boxtext = ' Box'; } else {$boxtext = ' Boxes'; }
        echo $row['BoxCount'] . $boxtext . '[::REQDATA::]';
        if($row['Urgency'] == 2) {echo '(URGENT) ';  }
        echo $row['DeliverTo'] . ' - ' . $row['Location'] . '[::REQDATA::]';
        echo $row['Status'];
    }
    
?>

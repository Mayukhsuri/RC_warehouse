<?php
    
   // include('includes/connection.php');
    include('includes/ah_form.php');
    
    $allowopenrequest = 0;
    
    $result9 = sqlsrv_query($connection, "SELECT ID, Name FROM departments");
    while($row9 = sqlsrv_fetch_array($result9)) {$rdeptname[$row9['ID']] = $row9['Name']; }
    
    
    
?>

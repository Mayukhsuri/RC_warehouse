<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
 <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
 <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="refresh" content="20">
    
<head>
    <title>EOF RC Warehouse System</title>
    <LINK REL=StyleSheet HREF="../css/style.css" TYPE="text/css" MEDIA=screen />
</head>
    
<body>
    
    <div class="main">
    <div class="column20"></div>
    <div class="column60">
    <div class="contentblock">
        <table>
            <tr>
                <th>Device Name</th>
                <th>Batch ID</th>
                <th>Scan Time</th>
                <th>Barcode (Box ID)</th>
                <th>Location ID</th>
            </tr>
        
        <?php
        
            include('../includes/connection.php');
            $result = sqlsrv_query($connection, "SELECT * FROM transactions ORDER BY ID DESC LIMIT 100") or die(sqlsrv_errors());
            while($row = sqlsrv_fetch_array($result)) {
                echo '<tr>';
                echo '<td>' . $row['DeviceID'] . '</td>';
                echo '<td>' . $row['BatchID'] . '</td>';
                echo '<td>' . date('n/j/Y g:ia',$row['TimeStamp']) . '</td>';
                echo '<td>' . $row['Barcode'] . '</td>';
                echo '<td>' . $row['Location'] . '</td>';
                echo '</tr>';
            }
            
        ?>
        
        </table>
    </div>
    </div>
    </div>
    
</body>

</html>

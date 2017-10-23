<?php
    
    $loc = cleantext($_GET['loc']);
    $boxID = cleantext($_GET['box']);
   if($boxID != '') {$boxquery = " AND PARSENAME(REPLACE(Split.a.value('.', 'VARCHAR(100)'),'-','.'),1) = " . $boxID; } else {$boxquery = ""; }
    
if($loc == 0) {
        header('Location: index.php?pid=111'); exit;
    }    


$query = "SELECT a.ID, Location,
PARSENAME(REPLACE(Split.a.value('.', 'VARCHAR(100)'),'-','.'),1) 'Values' , b.Barcode
FROM  
(
     SELECT ID, [Location],
     CAST ('<M>' + REPLACE([BoxData], ',', '</M><M>') + '</M>' AS XML) AS Data 
     FROM requests     
) AS A 
CROSS APPLY Data.nodes ('/M') AS Split(a)
JOIN boxes b ON PARSENAME(REPLACE(Split.a.value('.', 'VARCHAR(100)'),'-','.'),1)=b.ID
WHERE PARSENAME(REPLACE(Split.a.value('.', 'VARCHAR(100)'),'-','.'),1) IS NOT NULL
AND Location = '$loc'" .$boxquery;
    


    if(isset($_GET['cancelrequest'])) {unset($_SESSION['RCWarehouse_RequestedBoxes']); }
    
    if(isset($_GET['requestall'])) {
        $result = sqlsrv_query($connection, $query);
        while($row = sqlsrv_fetch_array($result)) {$_SESSION['RCWarehouse_RequestedBoxes'][] = $row['Values']; }
        $_SESSION['RCWarehouse_RequestedBoxes'] = array_unique($_SESSION['RCWarehouse_RequestedBoxes']);
    }
    
?>
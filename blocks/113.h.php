<?php
    


    $box = cleantext($_GET['box']);
   

    $boxfound = 0;

    if(isset($_GET['box']) && is_numeric($_GET['box'])) {
        $boxfound = 1;
        $boxid = $_GET['box'];
        $row = sqlsrv_fetch_array(sqlsrv_query($connection,"SELECT ID, Barcode FROM boxes WHERE ID=$boxid"));
        if(!$row) {
            $boxfound = 0;
        } else {
            $barcode = $row['Barcode'];
        }
    }
    
    if($boxfound == 0) {header('Location: index.php?pid=111'); exit; }
    
    if(isset($_GET['requested'])) {
        $_SESSION['RCWarehouse_RequestedBoxes'][] = $boxid;
        header('Location: index.php?pid=112&loc=' . $_SESSION['loc'] . '&box='); exit;
    }

?>

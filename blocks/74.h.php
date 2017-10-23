<?php
if(isset($_GET['rec']) && is_numeric($_GET['rec'])) {
    $printrec = $_GET['rec'];

    write_log(__FILE__,__LINE__,"prntrec = $printrec");

    $sql = "UPDATE locations SET LabelPrinted=0 WHERE ID=$printrec";
    sqlsrv_query($connection, $sql);
    write_log(__FILE__,__LINE__,$sql);
    header('Location: index.php?pid=77'); exit;
}
    
?>

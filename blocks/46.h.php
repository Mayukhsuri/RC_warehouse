<?php
    
  // include('includes/connection.php');
include('includes/ah_form.php');
    
$form = new ah_form('searchboxes');
$form->addTitle('Search Boxes');
    
$form->textInput('barcode','Box Barcode');
    
if($form->formValidated == 1) {
    $boxcode = $form->validated['barcode']['clean'];
    if(is_numeric($boxcode) && strlen($boxcode) < 9) {
        $bclen = strlen($boxcode);
        $boxcode = 'B' . str_repeat('0',(8-$bclen)) . $boxcode;
    }
    $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM boxes WHERE Barcode='$boxcode'"));

    $sql = "SELECT count(*) as ROW_COUNT FROM boxes WHERE Barcode='$boxcode'";
    $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
    $row_count = $row['ROW_COUNT'];

    $sql = "SELECT * FROM boxes WHERE Barcode='$boxcode'";
    $result = sqlsrv_query($connection, $sql);
        
    if( $row_count < 1 )  {
        header('Location: index.php?pid=47&barcode=' . $row['Barcode']); exit;
           
    } 
        
    else {
        $ALERT_WARNING = "Box " . $boxcode . " not found in system.";
    }
}
    
?>

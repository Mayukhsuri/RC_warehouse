<?php
    
    write_log(__FILE__,__LINE__);

    include('includes/connection.php');
    include('includes/ah_form.php');
    
    $form = new ah_form('searchboxes');
    $form->addTitle('Search Boxes');
    
    $form->textInput('barcode','Box Barcode');
    
    if($form->formValidated == 1) {
        $boxcode = $form->validated['barcode']['clean'];
        /* $sql = "SELECT * */
        /*         FROM transactions  */
        /*         WHERE Barcode='$boxcode' AND Active=1"; */
        $sql = "SELECT *
                FROM transactions 
                WHERE Barcode='$boxcode'";

        write_log(__FILE__,__LINE__,$sql);
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
        if(!$row) {
            $ALERT_WARNING = "Box " . $boxcode . " not found in system.";
        } else {
            header('Location: index.php?pid=34&barcode=' . $row['Barcode']); exit;
        }
    }
    
?>

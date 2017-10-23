<?php

write_log(__FILE__,__LINE__);

    include('includes/ah_form.php');

    $form = new ah_form('reprintboxlabel');
    $form->textInput('barcode','Barcode');

    if($form->formValidated == 1) {

        $reprintbarcode = $form->validated['barcode']['clean'];
        $sql = "SELECT ID FROM boxes WHERE Barcode='$reprintbarcode'";
        write_log(__FILE__,__LINE__,$sql);
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
        if($row) {
            $reprintboxid = $row['ID'];

write_log(__FILE__,__LINE__,"printfiles/BoxLabel.php?id=" . $reprintboxid);

            header('Location: printfiles/BoxLabel.php?id=' . $reprintboxid); exit;
write_log(__FILE__,__LINE__);

        }
        $ALERT_WARNING = "Box " . $reprintbarcode . " not found in database.";
    }

    
?>

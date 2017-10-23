<div class="column80">
    <div class="contentblock">
        <?php
        
            echo '<h2>Box Details</h2>' . PHP_EOL;
            echo '<div class="spacer"></div>' . PHP_EOL;
            echo '<div class="datalist">' . PHP_EOL;
            $boxcode = $_GET['barcode'];
            $result = sqlsrv_query($connection, "SELECT ID, Name FROM departments");
            while($row = sqlsrv_fetch_array($result)) {$deptname[$row['ID']] = $row['Name']; }
            
            $result = sqlsrv_query($connection, "SELECT ID, Description FROM recordtypes");
            while($row = sqlsrv_fetch_array($result)) {$recordtype[$row['ID']] = $row['Description']; }
        
            function dline($label,$data) {echo '<div class="dline"><div class="label">' . $label . '</div><div class="data">' . $data . '</div></div>' . PHP_EOL; }
            
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM boxes WHERE Barcode='$boxcode'"));
            dline('Box ID',$row['Barcode']);
            dline('Box Status','Pending (Awaiting Information)');
            echo '<div class="spacer"></div>' . PHP_EOL;
            $boxid = $row['ID'];
           $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM records WHERE BoxID= $boxid"));
           $departmenttext = $deptname[$row['DepartmentID']];
           $recordtypetext = $recordtype[$row['RecordTypeID']];
           dline('Department',$departmenttext);
           dline('Record Type',$recordtypetext);
            
        ?>
        </div>
    </div>
</div>

<div class="column60">
    <div class="contentblock">
        <?php $form->displayForm('Finalize'); ?>
    </div>
</div>

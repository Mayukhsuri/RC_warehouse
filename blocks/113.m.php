<div class="column80">
    <div class="contentblock">
        <?php
        


            echo '<h2>Box Details</h2>' . PHP_EOL;
            echo '<div class="buttonleft"><a href="index.php?pid=113&box=' . $boxid . '&requested=1&loc=' . $_SESSION['loc'] . '">Request Box</a>
            <a href="index.php?pid=112&loc=' . $_SESSION['loc'] . '">Back to List</a></div>' . PHP_EOL;
            echo '<div class="spacer" style="clear: both;"></div>' . PHP_EOL;
            echo '<div class="datalist">' . PHP_EOL;

            $result = sqlsrv_query($connection, "SELECT ID, Code, Name FROM properties");
            while($row = sqlsrv_fetch_array($result)) {$propertycode[$row['ID']] = $row['Code'] . ' - ' . $row['Name']; }
            
            $result = sqlsrv_query($connection, "SELECT ID, Name FROM departments");
            while($row = sqlsrv_fetch_array($result)) {$deptname[$row['ID']] = $row['Name']; }

            $result = sqlsrv_query($connection, "SELECT ID, Description FROM recordtypes");
            while($row = sqlsrv_fetch_array($result)) {$recordtype[$row['ID']] = $row['Description']; }
        
            function dline($label,$data) {echo '<div class="dline"><div class="label">' . $label . '</div><div class="data">' . $data . '</div></div>' . PHP_EOL; }
            
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM boxes WHERE ID=$boxid"));
            dline('Box ID',$row['Barcode']);

            $locationbarcode = $row['Location'];

            $row2 = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT Name FROM locations WHERE Barcode='$locationbarcode'"));
            //dline('Current Location',$row2['Name']);

            dline('Last Activity',date('n/j/Y',$row['TimeStamp']));

            $result = sqlsrv_query($connection, "SELECT * FROM records WHERE BoxID=$boxid AND Active=1");


            while($row = sqlsrv_fetch_array($result)) {
                echo '<div class="spacer"></div>' . PHP_EOL;
                dline('Property',$propertycode[$row['PropertyID']]);
                dline('Record Type',$recordtype[$row['RecordTypeID']]);
                dline('Originator',$deptname[$row['DepartmentID']]);
                dline('Start Date',date('n/j/Y',$row['StartDate']));
                dline('End Date',date('n/j/Y',$row['EndDate']));
            }
            echo '<div class="spacer"></div>' . PHP_EOL;

            
            $lnamequery = "SELECT Barcode, Name FROM locations WHERE";
            $lnamequerycount = 0;



            $result = sqlsrv_query($connection, "SELECT * FROM transactions WHERE Barcode = '$barcode'");

            while($row = sqlsrv_fetch_array($result)) {

                $lnamequerycount++;
                if($lnamequerycount > 1) {$lnamequery .= " OR"; }
                $lnamequery .= " Barcode='" . $row['Location'] . "'";
            }
            if ($lnamequerycount) {
                $result = sqlsrv_query($connection, $lnamequery);
                if( $result === false ) {
                    die( print_r( sqlsrv_errors(), true));
                }

                while($row = sqlsrv_fetch_array($result)) {$locationname[$row['Barcode']] = $row['Barcode'] . ' - ' . $row['Name']; }
                
                $recordcount = 0;
                
                $result = sqlsrv_query($connection, "SELECT * FROM transactions WHERE Barcode='$barcode' ORDER BY TimeStamp DESC");
                while($row = sqlsrv_fetch_array($result)) {
                    $recordcount++;
                    if($recordcount == 1) {$header = "Box History"; } else {$header = ""; }
                    $dataline = date('n/j/Y g:ia',$row['TimeStamp']) . ' - ' . $locationname[$row['Location']];
                    dline($header,$dataline);
                }
            }

        ?>
        </div>
    </div>
</div>

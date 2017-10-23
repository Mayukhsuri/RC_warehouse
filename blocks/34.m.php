<div class="column80">
    <div class="contentblock">
        <?php



            echo '<h2>Box Details</h2>' . PHP_EOL;
            echo '<div class="spacer"></div>' . PHP_EOL;
            echo '<div class="datalist">' . PHP_EOL;

        
            $sql = "SELECT ID, Code, Name FROM properties";
            write_log(__FILE__,__LINE__,$sql);

            $result = sqlsrv_query($connection, $sql);
            while($row = sqlsrv_fetch_array($result)) {
                $propertycode[$row['ID']] = $row['Code'] . ' - ' . $row['Name'];
            }
            
            $sql = "SELECT ID, Name FROM departments";
            write_log(__FILE__,__LINE__,$sql);

            $result = sqlsrv_query($connection, $sql);
            while($row = sqlsrv_fetch_array($result)) {
                $deptname[$row['ID']] = $row['Name'];
            }
            
            $sql = "SELECT ID, Description FROM recordtypes";
            write_log(__FILE__,__LINE__,$sql);

            $result = sqlsrv_query($connection, $sql);
            while($row = sqlsrv_fetch_array($result)) {
                $recordtype[$row['ID']] = $row['Description'];
            }
        
            function dline($label,$data) {
                echo '<div class="dline"><div class="label">' . $label . '</div><div class="data">' . $data . '</div></div>' . PHP_EOL;
            }


            $sql = "SELECT * FROM boxes WHERE ID=$boxid";
            write_log(__FILE__,__LINE__,$sql);

            $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
            dline('Box ID',$row['Barcode']);


            $sql = "SELECT top 1 * FROM transactions WHERE Barcode='$barcode' ORDER BY TimeStamp DESC";
            write_log(__FILE__,__LINE__,$sql);

            $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));

            $locationbarcode = $row['Location'];

            $sql = "SELECT Name FROM locations WHERE Barcode='$locationbarcode'";
            write_log(__FILE__,__LINE__,$sql);

            $row2 = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
            dline('Current Location',$row2['Name']);
            dline('Last Activity',date('n/j/Y',$row['TimeStamp']));

            $sql = "SELECT * FROM records WHERE BoxID=$boxid AND Active=1";
            $result = sqlsrv_query($connection, $sql);
            write_log(__FILE__,__LINE__,$sql);

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

            $sql = "SELECT * FROM transactions WHERE Barcode = '$barcode'";
            write_log(__FILE__,__LINE__,$sql);

            $result = sqlsrv_query($connection, $sql);
            while($row = sqlsrv_fetch_array($result)) {
                $lnamequerycount++;
                if($lnamequerycount > 1) {$lnamequery .= " OR"; }
                $lnamequery .= " Barcode='" . $row['Location'] . "'";
            }

            $result = sqlsrv_query($connection, $lnamequery);
            if ($lnamequerycount) {
                while($row = sqlsrv_fetch_array($result)) {
                    $locationname[$row['Barcode']] = $row['Barcode'] . ' - ' . $row['Operation'] . ' - ' . $row['Name'];
                }
            }
            
            $recordcount = 0;

            $sql = "SELECT * FROM [dbo].[vwBoxHistory] WHERE Barcode='$barcode' ORDER BY TimeStamp DESC";
            write_log(__FILE__,__LINE__, $sql);
            $result = sqlsrv_query($connection, $sql);

            while($row = sqlsrv_fetch_array($result)) {
                $recordcount++;
                if($recordcount == 1) {$header = "Box History"; } else {$header = ""; }
                //$dataline = date('n/j/Y g:ia',$row['TimeStamp']) . ' - ' . $locationname[$row['Location']];
                $dataline = date_format($row['TimeStamp'], 'Y-m-d H:i:s') . ' - ';
                //$dataline .= $row['Barcode'] . ' - ';
                $dataline .= $row['Location'] . ' - ';
                $dataline .= $row['Operation'] . ' - ';
                $dataline .= $row['LastHandler'];

                dline($header,$dataline);
            }
            
            
            //echo '<div class="spacer"></div>' . PHP_EOL;
            //echo '<div class="buttonleft"><a href="' . $pathPrep . '&pid=61">Return</a></div>' . PHP_EOL;
            
        ?>
        </div>
    </div>
</div>

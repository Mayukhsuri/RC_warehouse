<div class="column80">
    <div class="contentblock">
        <?php
            ob_start();
            $tfilter = '';
        if (!isset($_GET['lastactivity'])){
             $tfilter = '';
        }
        else{
            
            
            if ($_GET['lastactivity'] == 24) {
                
              $tfilter =  'AND RequestTime >= DATEADD(day, -1, GETDATE())';
            }
            else if ($_GET['lastactivity'] == 7) {
                
              $tfilter =  'AND RequestTime >= DATEADD(day, -7, GETDATE())';
            }
            else if ($_GET['lastactivity'] == 30) {
                
              $tfilter =  'AND RequestTime >= DATEADD(month, -1, GETDATE())';
            }
            
        };
        
         $sql = "SELECT count(*) as ROW_COUNT FROM vwWarehouseBoxPickList where [ReqStatus] <> 'Delivered'";

            $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
            $row_count = $row['ROW_COUNT'];
 
         $sql = "SELECT [RequestID]
                         ,[ReqStatus]
                         ,[RequestBy]
                         ,[RequestTime]
                         ,[LocationBarcode]
                         ,[Warehouse]
                         ,[Row]
                         ,[Bay]
                         ,[Shelf]
                         ,[LocationName]
                         ,[BoxBarcode]
                         ,[BoxStatus]
                  FROM [dbo].[vwWarehouseBoxPickList]
                  WHERE ReqStatus in ('Standard' , 'Urgent') "
                  . $tfilter .
                  " ORDER BY Warehouse,Row,Bay,Shelf "
;


//write_log(__FILE__,__LINE__,$sql);

             $result = sqlsrv_query($connection, $sql);



             $row_number = $row_count;
           // include('includes/connection.php');
            if(isset($_GET['inactive'])) {$activeQuery = "Active=0"; $activeheader = " (Inactive)"; } else {$activeQuery = "Active=1"; $activeheader = " "; }
            echo '<h2>Pick List' . $activeheader . '</h2>' . PHP_EOL;
            echo '<div class="linklist">';
            echo '<a href="index.php?pid=51&lastactivity=\'\'">Open</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=51&lastactivity=24">Last 24 Hours</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=51&lastactivity=7">Last 7 days</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=51&lastactivity=30">Last 1 Month</a>&nbsp&nbsp&nbsp';
            echo '<form method="get" action="export.php">
                      <input type="submit" name="export_excel" class="btn btn-success" value="Export to Excel">
                      <input type="hidden" name="lastactivity" value="';
            echo $tfilter;
            echo      '">';
            echo    '</form>';
            echo '</div>' . PHP_EOL;
            echo '<div class="spacer"></div>' . PHP_EOL;


      
         

            if($row_number >= 1) {
               echo '<table>' . PHP_EOL;
                echo '<tr>' . PHP_EOL;
                echo '<th>Request ID</th>' . PHP_EOL;
                echo '<th>Requested By</th>' . PHP_EOL;
                echo '<th>Request Status</th>' . PHP_EOL;
                echo '<th>Warehouse</th>' . PHP_EOL;
                echo '<th>Row</th>' . PHP_EOL;
                echo '<th>Bay</th>' . PHP_EOL;
                echo '<th>Shelf</th>' . PHP_EOL;
                echo '<th>Shelf Barcode</th>' . PHP_EOL;
                echo '<th>Box Barcode</th>' . PHP_EOL;

                echo '</tr>' . PHP_EOL;
                $resultcount = 0;
                while($row = sqlsrv_fetch_array($result)) {
                    $resultcount++;
                    if(($resultcount % 2) == 0) {
                        $classname = ' class="alt"';
                    } else {
                        $classname = ''; 
                    }

                    /*
                    if($row['ActivateDate'] == 1) {
                        echo '<tr onclick="location.href=\'index.php?pid=53&did=' . $row['ID'] . '\'"' . $classname . '>' . PHP_EOL;
                    } else {
                        echo '<tr onclick="location.href=\'index.php?pid=54&did=' . $row['ID'] . '\'"' . $classname . '>' . PHP_EOL;
                    }
                    */
                    echo '<td>' . $row['RequestID'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['RequestBy'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['ReqStatus'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['Warehouse'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['Row'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['Bay'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['Shelf'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['LocationBarcode'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['BoxBarcode'] . '</td>' . PHP_EOL;
                    /*
                    $applicationtypetext = 'Not Defined';
                    if($row['ApplicationType'] == 1) {$applicationtypetext = 'Courier Device'; }
                    if($row['ApplicationType'] == 2) {$applicationtypetext = 'RC Device'; }
                    echo '<td>' . $applicationtypetext . '</td>' . PHP_EOL;
                    if($row['Active'] == 1) {$statustext = 'Active'; } else {$statustext = 'Inactive'; }
                    if($row['ActivateDate'] == -1) {$statustext = 'Unregistered'; }
                    echo '<td>' . $statustext . '</td>' . PHP_EOL;
                    */
                    echo '</tr>' . PHP_EOL;
                }
                echo '</table>' . PHP_EOL;
            } else {
                echo '<p>No list found.</p>' . PHP_EOL;
            }
 ob_end_flush();
    ?>
    </div>
</div>
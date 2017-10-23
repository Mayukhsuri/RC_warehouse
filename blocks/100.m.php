<div class="column80">
    <div class="contentblock">
        <?php
        
            $sttext = '';
            $pageheader = 'Open Requests';
            $stquery = 'Status>0 AND Status<9 AND Status!=7';
            if(isset($_GET['st'])) {
                $sttext = '&st=0';
                $stquery = 'LastActivity>' . (time()-864000) . ' AND (Status<1 OR Status>8)';
                $pageheader = 'Recently Closed Requests';
            }
            $tempuserid = $_SESSION['rcw_currentUserID'];
            $obtext = "RequestTime";
            if(isset($_GET['ob']) && is_numeric($_GET['ob'])) {
                if($_GET['ob'] == 1) {$obtext = "DeliverTo"; }
                if($_GET['ob'] == 2) {$obtext = "Location"; }
                if($_GET['ob'] == 3) {$obtext = "Status"; }
            }
            echo '<h2>' . $pageheader . '</h2>' . PHP_EOL;
            echo '<div class="linklist">' . PHP_EOL;
            echo '<a href="index.php?pid=100">Open Requests</a>' . PHP_EOL;
            echo '<a href="index.php?pid=102">Delivered</a>' . PHP_EOL;
            echo '<a href="index.php?pid=100&st=0">Recently Closed</a>' . PHP_EOL;
            echo '</div>' . PHP_EOL;

            $sql = "SELECT count(*) as ROW_COUNT FROM [dbo].[requests] WHERE $stquery";
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
            $row_count = $row['ROW_COUNT'];

$sql = "SELECT *, NewStatus=(CASE [Status] WHEN 0 THEN 'On hold' 
 WHEN 1 THEN 'Requested (Standard)'
 WHEN 2 THEN 'Requested (Urgent)'
 WHEN 3 THEN 'Pulling Boxes'
 WHEN 4 THEN 'En-Route / Delivering'
 WHEN 7 THEN 'Delivered'
 WHEN 8 THEN 'Return Requested'
 WHEN 9 THEN 'Complete'
 ELSE 'Canceled' 
 END)
FROM [dbo].[requests] WHERE $stquery ORDER BY $obtext ASC ";

$result = sqlsrv_query($connection, $sql);

if( $result === false ) {
     die( print_r( sqlsrv_errors(), true));
}

//$row_count = sqlsrv_num_rows($result);
            if($row_count >= 1) {
                $statustext[-1] = 'Canceled';
                $statustext[0] = 'On-Hold';
                $statustext[1] = 'Requested (Standard)';
                $statustext[2] = 'Requested (Urgent)';
                $statustext[3] = 'Pulling Boxes';
                $statustext[4] = 'En-Route / Delivering';
                $statustext[7] = 'Delivered';
                $statustext[8] = 'Return Requested';
                $statustext[9] = 'Complete';
               
                echo '<table>';
                echo '<tr>';
                echo '<th class="thlink" onclick="location.href=\'index.php?pid=100' . $sttext . '\'">Request Date</th>';
                echo '<th class="thlink" onclick="location.href=\'index.php?pid=100' . $sttext . '&ob=1\'">Deliver To</th>';
                echo '<th class="thlink" onclick="location.href=\'index.php?pid=100' . $sttext . '&ob=2\'">Location</th>';
                echo '<th class="thlink" onclick="location.href=\'index.php?pid=100' . $sttext . '&ob=3\'">Status</th>';
                echo '</tr>';
                $rowcount = 0;
                while($row = sqlsrv_fetch_array($result)) {
                    $rowcount++;
                    if(($rowcount/2) == intval($rowcount/2)) {$style = ' class="alt" style="background-color: #d2e8dc"'; } else {$style=''; }
                    echo '<tr onclick="location.href=\'index.php?pid=101&rid=' . $row['ID'] . '\'"' . $style . '>';
                    echo '<td>' . date('n/j/Y g:ia',$row['RequestTime']) . '</td>';
                    echo '<td>' . $row['DeliverTo'] . '</td>';
                    echo '<td>' . $row['Location'] . '</td>';
                    echo '<td>' . $row['NewStatus'] . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p>No requests found.</p>';
            }
            
        ?>
    </div>
</div>

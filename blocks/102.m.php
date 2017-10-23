<div class="column80">
    <div class="contentblock">
        <?php
        
            $tempuserid = $_SESSION['rcw_currentUserID'];
            $obtext = "RequestTime";
            if(isset($_GET['ob']) && is_numeric($_GET['ob'])) {
                if($_GET['ob'] == 1) {$obtext = "DeliverTo"; }
                if($_GET['ob'] == 2) {$obtext = "Location"; }
                if($_GET['ob'] == 3) {$obtext = "Status"; }
            }
            echo '<h2>Delivered Requests</h2>' . PHP_EOL;
            echo '<div class="linklist">' . PHP_EOL;
            echo '<a href="index.php?pid=100">Open Requests</a>' . PHP_EOL;
            echo '<a href="index.php?pid=102">Delivered</a>' . PHP_EOL;
            echo '<a href="index.php?pid=100&st=0">Recently Closed</a>' . PHP_EOL;
            echo '</div>' . PHP_EOL;

            $sql = "SELECT count(*) as ROW_COUNT FROM requests WHERE Status=7";
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
            $row_count = $row['ROW_COUNT'];

            $sql = "SELECT * FROM requests WHERE Status=7 ORDER BY $obtext ASC";
            $result = sqlsrv_query($connection, $sql);

            $result = sqlsrv_query($connection, "SELECT * FROM requests WHERE Status=7 ORDER BY $obtext ASC");


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
                echo '<th class="thlink" onclick="location.href=\'index.php?pid=102">Request Date</th>';
                echo '<th class="thlink" onclick="location.href=\'index.php?pid=102&ob=1\'">Deliver To</th>';
                echo '<th class="thlink" onclick="location.href=\'index.php?pid=102&ob=2\'">Location</th>';
                echo '<th class="thlink" onclick="location.href=\'index.php?pid=102&ob=3\'">Status</th>';
                echo '</tr>';
                $rowcount = 0;
                while($row = sqlsrv_fetch_array($result)) {
                    $rowcount++;
                    if(($rowcount/2) == intval($rowcount/2)) {$style = ' class="alt" style="background-color: #d2e8dc"'; } else {$style=''; }
                    echo '<tr onclick="location.href=\'index.php?pid=101&rid=' . $row['ID'] . '\'"' . $style . '>';
                    echo '<td>' . date('n/j/Y g:ia',$row['RequestTime']) . '</td>';
                    echo '<td>' . $row['DeliverTo'] . '</td>';
                    echo '<td>' . $row['Location'] . '</td>';
                    
                    echo '<td>Delivered</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p>No requests found.</p>';
            }
            
        ?>
    </div>
</div>

<div class="column80">
    <div class="contentblock">
        <h2>Scan Activity <span style="font-size: 12px; color: #555;">&nbsp(LAST 72 HOURS...)</span></h2>
        <?php
            
            echo '<div class="buttonleft"><a href="index.php?pid=30">Refresh</a></div>' . PHP_EOL;
            echo '<div class="spacer" style="clear: both;"></div>' . PHP_EOL;
            
            //include('includes/connection.php');
            
            $lastfifteen = time() - (86400 * 3);

            $sql = "SELECT count(*) as ROW_COUNT FROM transactions WHERE TimeStamp>$lastfifteen";
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
            $row_count = $row['ROW_COUNT'];

            $sql = "SELECT TOP 1000 * FROM transactions WHERE TimeStamp>$lastfifteen ORDER BY TimeStamp DESC";
          //  write_log(__FILE__,__LINE__,$sql);
            $result = sqlsrv_query($connection, $sql);
            
            if($row_count >= 1) {
                echo '<table>';
                echo '<tr>';
                //echo '<th>Device Name</th>';
                echo '<th>Scan Time</th>';
                echo '<th>Barcode (Box ID)</th>';
                echo '<th>Operation</th>';
                echo '<th>Location</th>';
                echo '</tr>';
                $rowcount = 0;
                while($row = sqlsrv_fetch_array($result)) {
                    $rowcount++;
                    if(($rowcount/2) == intval($rowcount/2)) {$style = ' class="alt"'; } else {$style=''; }
                    echo '<tr onclick="location.href=\'index.php?pid=34&barcode=' . $row['Barcode'] . '\'"' . $style . '>';
                    //echo '<td>' . $row['DeviceID'] . '</td>';
                    echo '<td>' . date('n/j/Y g:ia',$row['TimeStamp']) . '</td>';
                    echo '<td>' . $row['Barcode'] . '</td>';
                    echo '<td>' . $row['Operation'] . '</td>';
                    $locationbarcode = $row['Location'];
                    $row2 = sqlsrv_fetch_array(sqlsrv_query($connection,"SELECT Name FROM locations WHERE Barcode='$locationbarcode'"));
                    echo '<td>' . $row['Location'] . ' - ' . $row2['Name'] . '</td>';
                    echo "</tr>\n";
                }
                echo '</table>';
            } else {
                echo '<p>No recent activity.</p>';
            }
            
        ?>
    </div>
</div>

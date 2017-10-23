<div class="column60">
    <div class="contentblock">
        <?php
            
            function loctypetext($type) {
                switch($type) {
                    case 'W': $output = 'Warehouse'; break;
                    case 'O': $output = 'Field Location'; break; //'Outlet'; break;
                    case 'C': $output = 'Courier'; break;
                    case 'T': $output = 'Temporary'; break;
                    case 'D': $output = 'Destruction'; break;
                    case 'P': $output = 'Pending'; break;
                    default: $output = 'Unknown';
                }
                return $output;
            }
            
            echo '<h2>Open Boxes (' . $_SESSION['rcw_userFullName'] . ')</h2>' . PHP_EOL;
            echo '<div class="linklist">';
            echo '<a href="index.php?pid=41">Start New Box</a>&nbsp&nbsp&nbsp';
            echo '</div>' . PHP_EOL;
            echo '<div class="spacer"></div>' . PHP_EOL;
        //   -- StartedBy = $userid AND----- took this out may be this helps check in production.    

            $sql = "SELECT count(*) as ROW_COUNT FROM boxes WHERE Status='P' AND Name!='RC PRE-GENERATED BOX'";
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
            $row_count = $row['ROW_COUNT'];


            $sql = "SELECT * FROM boxes WHERE Status='P' AND Name!='RC PRE-GENERATED BOX' ORDER BY LastActivity DESC";
             $result = sqlsrv_query($connection, $sql);
        
            if($row_count >= 1) { 
            echo '<table>' . PHP_EOL;
                echo '<tr>' . PHP_EOL;
                echo '<th>Name</th>' . PHP_EOL;
                echo '<th>Barcode</th>' . PHP_EOL;
                echo '<th>Last Activity</th>' . PHP_EOL;
                echo '</tr>' . PHP_EOL;
                $resultcount = 0;
                while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    $resultcount++;
                    if(($resultcount / 2) == intval($resultcount / 2)) {$classname = ' class="alt"'; } else {$classname = ''; }
                    echo '<tr onclick="location.href=\'index.php?pid=42&rec=' . $row['ID'] . '\'"' . $classname . '>' . PHP_EOL;
                    echo '<td>' . $row['Name'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['Barcode'] . '</td>' . PHP_EOL;
                    echo '<td>' . date('n/j/Y',$row['LastActivity']) . '</td>' . PHP_EOL;
                    echo '</tr>' . PHP_EOL;
                }
                echo '</table>' . PHP_EOL;            
            } else {
            echo '<p>No records found.</p>' . PHP_EOL;
        }
            
        ?>
    </div>
</div>

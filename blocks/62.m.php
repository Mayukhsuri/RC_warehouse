<div class="column60">
    <div class="contentblock">
        <?php
            
            //include('includes/connection.php');
            //if(isset($_GET['inactive'])) {$activeQuery = "Active=0"; } else {$activeQuery = "Active=1"; }
            echo '<h2>Storage Locations</h2>' . PHP_EOL;
            echo '<div class="linklist">';
            echo '<a href="index.php?pid=61">Back</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=63&rec=' . $whid . '">Add Row</a>&nbsp&nbsp&nbsp';
            //echo '<a href="index.php?pid=67&inactive=1">View Inactive</a>&nbsp&nbsp&nbsp';
            //echo '<a href="index.php?pid=68">Add Warehouse</a>';
            echo '</div>' . PHP_EOL;
            echo '<div class="spacer"></div>' . PHP_EOL;

            $sql = "SELECT count(*) as ROW_COUNT FROM locations WHERE Warehouse=$whid";
write_log(__FILE__,__LINE__,$sql);

            $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
            $row_count = $row['ROW_COUNT'];

            $sql = "SELECT DISTINCT Row, ID FROM locations WHERE Warehouse=$whid ORDER BY ID ASC";
            $result = sqlsrv_query($connection, $sql);
                                             //$row_count = sqlsrv_num_rows($result);
        if($row_count >= 1) {
                echo '<table>' . PHP_EOL;
                echo '<tr>' . PHP_EOL;
                echo '<th>Warehouse</th>' . PHP_EOL;
                echo '<th>Row</th>' . PHP_EOL;
                echo '<th>Bays</th>' . PHP_EOL;
                echo '<th>Shelves</th>' . PHP_EOL;
                echo '</tr>' . PHP_EOL;
                $resultcount = 0;
                while($row = sqlsrv_fetch_array($result)) {
                    $resultcount++;
                    if(($resultcount / 2) == intval($resultcount / 2)) {$classname = ' class="alt"'; } else {$classname = ''; }
                    $rownum = $row['Row'];
                    echo '<tr onclick="location.href=\'index.php?pid=64&rec=' . $whid . '&row=' . $rownum . '\'"' . $classname . '>' . PHP_EOL;
                    echo '<td>' . $whname . '</td>' . PHP_EOL;
                    echo '<td>' . $rownum . '</td>' . PHP_EOL;
                    
                    $row2 = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT COUNT(DISTINCT Row) AS newst FROM locations WHERE Warehouse=$whid AND Row=$rownum"));
                    echo '<td>' . $row2['newst'] . '</td>' . PHP_EOL;
                    $row2 = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT COUNT(ID) AS id FROM locations WHERE Warehouse=$whid AND Row=$rownum"));
                    echo '<td>' . $row2['id'] . '</td>' . PHP_EOL;
                    
                    echo '</tr>' . PHP_EOL;
                }
                echo '</table>' . PHP_EOL;
                            } else {

                echo '<p>No rows found.</p>' . PHP_EOL;

            }
            
        ?>
    </div>
</div>

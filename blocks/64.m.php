<div class="column60">
    <div class="contentblock">
        <?php
            
            include('includes/connection.php');
            //if(isset($_GET['inactive'])) {$activeQuery = "Active=0"; } else {$activeQuery = "Active=1"; }
            echo '<h2>Storage Locations</h2>' . PHP_EOL;
            echo '<div class="linklist">';
            echo '<a href="index.php?pid=62&rec=' . $whid . '">Back</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=65&rec=' . $whid . '&row=' . $rowid . '">Add Bays / Shelves</a>&nbsp&nbsp&nbsp';
            //echo '<a href="index.php?pid=67&inactive=1">View Inactive</a>&nbsp&nbsp&nbsp';
            //echo '<a href="index.php?pid=68">Add Warehouse</a>';
            echo '</div>' . PHP_EOL;
            echo '<div class="spacer"></div>' . PHP_EOL;

            $sql = "SELECT count(*) as ROW_COUNT FROM locations WHERE Warehouse=$whid and Row=$rowid";
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
            $row_count = $row['ROW_COUNT'];

            $sql = "SELECT DISTINCT Bay FROM locations WHERE Warehouse=$whid and Row=$rowid ORDER BY ID ASC";
            $result = sqlsrv_query($connection, $sql);

            if($row_count < 1) {
                echo '<p>No bays found.</p>' . PHP_EOL;
            } else {
                echo '<table>' . PHP_EOL;
                echo '<tr>' . PHP_EOL;
                echo '<th>Warehouse</th>' . PHP_EOL;
                echo '<th>Row</th>' . PHP_EOL;
                echo '<th>Bay</th>' . PHP_EOL;
                echo '<th>Shelves</th>' . PHP_EOL;
                echo '</tr>' . PHP_EOL;
                $resultcount = 0;
                while($row = sqlsrv_fetch_array($result)) {
                    $resultcount++;
                    if(($resultcount / 2) == intval($resultcount / 2)) {$classname = ' class="alt nolink"'; } else {$classname = 'class="nolink"'; }
                    echo '<tr ' . $classname . '>' . PHP_EOL;
                    echo '<td>' . $whname . '</td>' . PHP_EOL;
                    echo '<td>' . $rowid . '</td>' . PHP_EOL;
                    $bayid = $row['Bay'];
                    echo '<td>' . $bayid . '</td>' . PHP_EOL;
                    $row2 = sqlsrv_fetch_array(sqlsrv_query("SELECT COUNT(ID) FROM locations WHERE Warehouse=$whid AND Row=$rowid AND Bay=$bayid"));
                    echo '<td>' . $row2['COUNT(ID)'] . '</td>' . PHP_EOL;
                    
                    echo '</tr>' . PHP_EOL;
                }
                echo '</table>' . PHP_EOL;
            }
            
        ?>
    </div>
</div>

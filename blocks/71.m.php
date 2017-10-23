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
            
          //  include('includes/connection.php');
            if(isset($_GET['inactive'])) {$activeQuery = "Active!=1"; } else {$activeQuery = "Active=1"; }
            echo '<h2>Field Locations</h2>' . PHP_EOL;
            echo '<div class="linklist">';
            echo '<a href="index.php?pid=71">View Active</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=71&inactive=1">View Inactive</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=72">Add Field Location</a>';
            echo '</div>' . PHP_EOL;
            echo '<div class="spacer"></div>' . PHP_EOL;


            $sql = "SELECT count(*) as ROW_COUNT FROM locations WHERE $activeQuery AND LocationType!='W'";
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
            $row_count = $row['ROW_COUNT'];

             $sql = "SELECT * FROM locations WHERE $activeQuery AND LocationType!='W' ORDER BY Name ASC";
            $result = sqlsrv_query($connection, $sql);
            if($row_count >= 1) {
                echo '<table>' . PHP_EOL;
                echo '<tr>' . PHP_EOL;
                echo '<th>Field Location Name</th>' . PHP_EOL;
                echo '<th>Barcode</th>' . PHP_EOL;
                echo '<th>Type</th>' . PHP_EOL;
                echo '<th>Active</th>' . PHP_EOL;
                echo '</tr>' . PHP_EOL;
                $resultcount = 0;
                while($row = sqlsrv_fetch_array($result)) {
                    $resultcount++;
                    if(($resultcount / 2) == intval($resultcount / 2)) {$classname = ' class="alt"'; } else {$classname = ''; }
                    echo '<tr onclick="location.href=\'index.php?pid=73&rec=' . $row['ID'] . '\'"' . $classname . '>' . PHP_EOL;
                    echo '<td>' . $row['Name'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['Barcode'] . '</td>' . PHP_EOL;
                    echo '<td>' . loctypetext($row['LocationType']) . '</td>' . PHP_EOL;
                    if($row['Active'] == 1) {$activetext = 'Yes'; } else {$activetext = 'No'; }
                    echo '<td>' . $activetext . '</td>' . PHP_EOL;
                    echo '</tr>' . PHP_EOL;
                }
                echo '</table>' . PHP_EOL;
                } else {
                echo '<p>No records found.</p>' . PHP_EOL;               
            }
            
        ?>
    </div>
</div>

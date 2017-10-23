<div class="column60">
    <div class="contentblock">
        <?php
            
write_log(__FILE__,__LINE__);

            function loctypetext($type) {
                switch($type) {
                    case 'W': $output = 'Warehouse'; break;
                    case 'O': $output = 'Field Location'; break;
                    case 'C': $output = 'Courier'; break;
                    case 'T': $output = 'Temporary'; break;
                    case 'D': $output = 'Destruction'; break;
                    case 'P': $output = 'Pending'; break;
                    default: $output = 'Unknown';
                }
                return $output;
            }
            
           // include('includes/connection.php');
            echo '<h2>Field Location Labels</h2>' . PHP_EOL;
            echo '<div class="linklist">';
            echo '<a href="index.php?pid=74">Field Locations</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=75">Warehouse Locations</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=76">Boxes</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=77">Printfiles</a>';
            echo '</div>' . PHP_EOL;
            echo '<div class="spacer"></div>' . PHP_EOL;


            $sql = "SELECT count(*) as ROW_COUNT FROM locations WHERE Active=1 AND LocationType!='W'";
write_log(__FILE__,__LINE__,$sql);
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
            $row_count = $row['ROW_COUNT'];
write_log(__FILE__,__LINE__,"row_count: $row_count");

            $sql = "SELECT * FROM locations WHERE Active=1 AND LocationType!='W' ORDER BY Name ASC";
            $result = sqlsrv_query($connection, $sql);
           
        if($row_count < 1) {
                echo '<p>No records found.</p>' . PHP_EOL;
            } else {
                echo '<table>' . PHP_EOL;
                echo '<tr>' . PHP_EOL;
                echo '<th>Field Location Name</th>' . PHP_EOL;
                echo '<th>Barcode</th>' . PHP_EOL;
                echo '<th>Type</th>' . PHP_EOL;
                echo '</tr>' . PHP_EOL;
                $resultcount = 0;
                while($row = sqlsrv_fetch_array($result)) {
                    $resultcount++;
                    if(($resultcount / 2) == intval($resultcount / 2)) {$classname = ' class="alt"'; } else {$classname = ''; }
                    echo '<tr onclick="location.href=\'index.php?pid=74&rec=' . $row['ID'] . '\'"' . $classname . '>' . PHP_EOL;
                    echo '<td>' . $row['Name'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['Barcode'] . '</td>' . PHP_EOL;
                    echo '<td>' . loctypetext($row['LocationType']) . '</td>' . PHP_EOL;
                    echo '</tr>' . PHP_EOL;
                }
                echo '</table>' . PHP_EOL;
            }
            
        ?>
    </div>
</div>

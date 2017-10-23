<div class="column60">
    <div class="contentblock">
        <?php
        
            $result = sqlsrv_query($connection, "SELECT * FROM departments");
            while($row = sqlsrv_fetch_array($result)) {$deptname[$row['ID']] = $row['Name']; }
            
           // include('includes/connection.php');
            if(isset($_GET['inactive'])) {$activeQuery = "Active!=1"; } else {$activeQuery = "Active=1"; }
            echo '<h2>Record Types</h2>' . PHP_EOL;
            echo '<div class="linklist">';
            echo '<a href="index.php?pid=87">View Active</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=87&inactive=1">View Inactive</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=88">Add Record Type</a>';
            echo '</div>' . PHP_EOL;
            echo '<div class="spacer"></div>' . PHP_EOL;

            $sql = "SELECT count(*) as ROW_COUNT FROM recordtypes WHERE $activeQuery";
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
            $row_count = $row['ROW_COUNT'];

            $sql = "SELECT *, CAST(Active AS int) AS newst FROM recordtypes WHERE $activeQuery ORDER BY Description ASC";
            $result = sqlsrv_query($connection, $sql);
        
            if($row_count >= 1) { 
                
                echo '<table>' . PHP_EOL;
                echo '<tr>' . PHP_EOL;
                echo '<th>Department</th>' . PHP_EOL;
                echo '<th>Description</th>' . PHP_EOL;
                echo '<th>Retain For</th>' . PHP_EOL;
                echo '<th>Active</th>' . PHP_EOL;
                echo '</tr>' . PHP_EOL;
                $resultcount = 0;
                while($row = sqlsrv_fetch_array($result)) {
                    $resultcount++;
                    if(($resultcount / 2) == intval($resultcount / 2)) {$classname = ' class="alt"'; } else {$classname = ''; }
                    echo '<tr onclick="location.href=\'index.php?pid=89&rec=' . $row['ID'] . '\'"' . $classname . '>' . PHP_EOL;
                    echo '<td>' . $deptname[$row['DepartmentID']] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['Description'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['RetainFor'] . '</td>' . PHP_EOL;
                    if($row['newst'] == 1) {$activetext = 'Yes'; } else {$activetext = 'No'; }
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

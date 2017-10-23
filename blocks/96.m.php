<div class="column60">
    <div class="contentblock">
        <?php
        
            $result2 = sqlsrv_query($connection, 'SELECT ID, GroupName FROM usergroups');
            while($row2 = sqlsrv_fetch_array($result2)) {$usergroupname[$row2['ID']] = $row2['GroupName']; }
            
           // include('includes/connection.php');
            if(isset($_GET['inactive'])) {$activeQuery = "Active=0"; } else {$activeQuery = "Active=1"; }
            echo '<h2>System Users</h2>' . PHP_EOL;
            echo '<div class="linklist">';
            echo '<a href="index.php?pid=96">View Active</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=96&inactive=1">View Inactive</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=97">Add User</a>';
            echo '</div>' . PHP_EOL;
            echo '<div class="spacer"></div>' . PHP_EOL;


            $sql = "SELECT count(*) as ROW_COUNT FROM users WHERE $activeQuery";
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
            $row_count = $row['ROW_COUNT'];

            $sql = "SELECT *, cast(Active as int) AS newst FROM users WHERE $activeQuery ORDER BY LastName ASC, FirstName ASC";
            $result = sqlsrv_query($connection, $sql);
        
        if($row_count >= 1) {
            
             echo '<table>' . PHP_EOL;
                echo '<tr>' . PHP_EOL;
                echo '<th>User Name</th>' . PHP_EOL;
                echo '<th>Employee ID</th>' . PHP_EOL;
                echo '<th>User Group</th>' . PHP_EOL;
                echo '<th>Active</th>' . PHP_EOL;
                echo '</tr>' . PHP_EOL;
                $resultcount = 0;
                while($row = sqlsrv_fetch_array($result)) {
                    $resultcount++;
                    if(($resultcount / 2) == intval($resultcount / 2)) {$classname = ' class="alt"'; } else {$classname = ''; }
                    echo '<tr onclick="location.href=\'index.php?pid=98&rec=' . $row['ID'] . '\'"' . $classname . '>' . PHP_EOL;
                    echo '<td>' . $row['LastName'] . ', ' . $row['FirstName'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['EmployeeID'] . '</td>' . PHP_EOL;
                    echo '<td>' . $usergroupname[$row['UserGroupID']] . '</td>' . PHP_EOL;
                    if($row['newst'] == 1) {$activetext = 'Yes'; } else {$activetext = 'No'; }
                    echo '<td>' . $activetext . '</td>' . PHP_EOL;
                    echo '</tr>' . PHP_EOL;
                }
                
            } else {
               echo '<p>No records found.</p>' . PHP_EOL;
                }
                echo '</table>' . PHP_EOL;
            
            
        ?>
    </div>
</div>

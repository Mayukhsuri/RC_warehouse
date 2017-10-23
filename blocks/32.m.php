<div class="column80">
    <div class="contentblock">
        <?php
            
            $result = sqlsrv_query($connection, $query);
            $nextquery = "SELECT ID, Barcode, LocationID FROM boxes WHERE";
            $nextquerycount = 0;
            while($row = sqlsrv_fetch_array($result)) {
                $nextquerycount++;
                if($nextquerycount > 1) {$nextquery .= " OR"; }
                $nextquery .= " ID=" . $row['BoxID'];
            }
            
            $nextquery2 = "SELECT ID, Name FROM locations WHERE";
            $nextquery2count = 0;
            $result = sqlsrv_query($connection, $nextquery);
            while($row = sqlsrv_fetch_array($result)) {
                $boxbarcode[$row['ID']] = $row['Barcode'];
                $boxlocationid[$row['ID']] = $row['LocationID'];
                $nextquery2count++;
                if($nextquery2count > 1) {$nextquery2 .= " OR"; }
                $nextquery2 .= " ID=" . $row['LocationID'];
            }
            
            $result = sqlsrv_query($connection, $nextquery2);
            while($row = sqlsrv_fetch_array($result)) {$locationname[$row['ID']] = $row['Name']; }
            
            $result = sqlsrv_query($connection, "SELECT ID, Code FROM properties");
            while($row = sqlsrv_fetch_array($result)) {$propertycode[$row['ID']] = $row['Code']; }
            
            $result = sqlsrv_query($connection, "SELECT ID, Description FROM recordtypes");
            while($row = sqlsrv_fetch_array($result)) {$recordtype[$row['ID']] = $row['Description']; }
            
            echo '<h2>Search Results</h2>' . PHP_EOL;
            
            $result = sqlsrv_query($connection, $query);
            if($result) {
                echo '<table>';
                echo '<tr>';
                echo '<th>Property</th>';
                echo '<th>Record Type</th>';
                echo '<th>Start Date</th>';
                echo '<th>End Date</th>';
                echo '<th>Box ID</th>';
                echo '<th>Current Location</th>';
                echo '</tr>';
                $rowcount = 0;
                while($row = sqlsrv_fetch_array($result)) {
                    $rowcount++;
                    if(($rowcount/2) == intval($rowcount/2)) {$style = ' class="alt"'; } else {$style=''; }
                    echo '<tr onclick="location.href=\'index.php?pid=34&box=' . $row['BoxID'] . '\'"' . $style . '>';
                    echo '<td>' . $propertycode[$row['PropertyID']] . '</td>';
                    echo '<td>' . $recordtype[$row['RecordTypeID']] . '</td>';
                    echo '<td>' . date('n/j/Y',$row['StartDate']) . '</td>';
                    echo '<td>' . date('n/j/Y',$row['EndDate']) . '</td>';
                    echo '<td>' . $boxbarcode[$row['BoxID']] . '</td>';
                    echo '<td>' . $locationname[$boxlocationid[$row['BoxID']]] . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p>No recent activity.</p>';
            }
            
        ?>
    </div>
</div>

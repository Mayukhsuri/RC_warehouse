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
            echo '<p>(Select a specific record to add an individual box to your record request...)</p>';
            
            $result = sqlsrv_query($connection, $query);// the problem is caught on line 4.
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
                echo "<!-- " . $_SESSION['RCWarehouse_RequestedBoxes'] . " -->";
                while($row = sqlsrv_fetch_array($result)) {
                    if((! isset($_SESSION['RCWarehouse_RequestedBoxes'])) || (! in_array($row['ID'],$_SESSION['RCWarehouse_RequestedBoxes']))) {
                            $rowcount++;
                            if(($rowcount/2) == intval($rowcount/2)) {$style = ' class="alt"'; } else {$style=''; }
                            echo '<tr onclick="location.href=\'index.php?pid=37&box=' . $row['BoxID'] . '&prop=' . $prop . '&rt=' . $rt . '&start=' . $start . '&end=' . $end . '\'"' . $style . '>';
                            echo '<td>' . $propertycode[$row['PropertyID']] . '</td>';
                            echo '<td>' . $recordtype[$row['RecordTypeID']] . '</td>';
                            echo '<td>' . date('n/j/Y',$row['StartDate']) . '</td>';
                            echo '<td>' . date('n/j/Y',$row['EndDate']) . '</td>';
                            echo '<td>' . $boxbarcode[$row['BoxID']] . '</td>';
                            echo '<td>' . $locationname[$boxlocationid[$row['BoxID']]] . '</td>';
                            echo '</tr>';
                            }
                    //  }
                }


                if($rowcount == 0) {echo '<tr class="nolink"><td colspan="6" style="padding: 20px;">No boxes in active record request.</td></tr>'; }
                echo '</table>';
            } else {
                echo '<p>No recent activity.</p>';
            }
            
            if($rowcount != 0) {echo '<div class="buttonleft"><a href="index.php?pid=36&prop=' . $prop . '&rt=' . $rt . '&start=' . $start . '&end=' . $end . '&requestall=1">Add All Boxes to Request</a><a href="index.php?pid=38">Request Unlisted Boxes</a></div>' . PHP_EOL; } else {echo '<div class="buttonleft"><a href="index.php?pid=38">Request Unlisted Boxes</a></div>'; }
        ?>
    </div>
    
    <?php
        
        if(isset($_SESSION['RCWarehouse_RequestedBoxes'])) {
            echo '<div class="contentblock" style="background-color: #e4ffec;">' . PHP_EOL;
            if(count($_SESSION['RCWarehouse_RequestedBoxes']) == 1) {$plural = ''; } else {$plural = 'es'; }
            echo '<h2>Current Request (' . count($_SESSION['RCWarehouse_RequestedBoxes']) . ' Box' . $plural . ')</h2>' . PHP_EOL;
            
            $requestquery = "SELECT * FROM records WHERE Active=1 AND BoxID IN (";
            $requestquerycount = 0;
            foreach($_SESSION['RCWarehouse_RequestedBoxes'] as $requestedbox) {
                $requestquerycount++;
                if($requestquerycount > 1) {$requestquery .= ","; }
                $requestquery .= $requestedbox;
            }
            $requestquery .= ") ORDER BY StartDate ASC, EndDate ASC";
            $result = sqlsrv_query($connection, $requestquery);
            if($result) {
                echo '<table>';
                echo '<tr>';
                echo '<th>Property</th>';
                echo '<th>Record Type</th>';
                echo '<th>Start Date</th>';
                echo '<th>End Date</th>';
                echo '<th>Box ID</th>';
                echo '</tr>';
                $rowcount = 0;
                while($row = sqlsrv_fetch_array($result)) {
                    $rowcount++;
                    if(($rowcount/2) == intval($rowcount/2)) {$style = ' class="alt nolink" style="background-color: #d2e8dc"'; } else {$style=' class="nolink"'; }
                    echo '<tr' . $style . '>';
                    echo '<td>' . $propertycode[$row['PropertyID']] . '</td>';
                    echo '<td>' . $recordtype[$row['RecordTypeID']] . '</td>';
                    echo '<td>' . date('n/j/Y',$row['StartDate']) . '</td>';
                    echo '<td>' . date('n/j/Y',$row['EndDate']) . '</td>';
                    echo '<td>' . $boxbarcode[$row['BoxID']] . '</td>';
                    echo '</tr>';
                }
                if($rowcount == 0) {echo '<tr><td colspan="6" style="padding: 20px;">No boxes in active record request.</td></tr>'; }
                echo '</table>';
            } else {echo $requestquery; }
            echo '<div class="buttonleft"><a href="index.php?pid=36&prop=' . $prop . '&rt=' . $rt . '&start=' . $start . '&end=' . $end . '&cancelrequest=1">Cancel Request</a><a href="index.php?pid=38">Finalize Request</a></div>' . PHP_EOL;
            echo '</div>' . PHP_EOL;
        }
        
    ?>
    
</div>

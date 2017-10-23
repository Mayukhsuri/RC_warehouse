<div class="column60">
    
    <?php
    
        if(isset($_SESSION['RCWarehouse_RequestedBoxes'])) {
            echo '<div class="contentblock" style="background-color: #e4ffec;">' . PHP_EOL;
            if(count($_SESSION['RCWarehouse_RequestedBoxes']) == 1) {$plural = ''; } else {$plural = 'es'; }
            echo '<h2>Current Request (' . count($_SESSION['RCWarehouse_RequestedBoxes']) . ' Box' . $plural . ')</h2>' . PHP_EOL;
            
            $result = sqlsrv_query ($connection, "SELECT ID, Code, Name FROM properties");
            while($row = sqlsrv_fetch_array($result)) {$propertycode[$row['ID']] = $row['Code'] . ' - ' . $row['Name']; }
            
            $result = sqlsrv_query($connection, "SELECT ID, Name FROM departments");
            while($row = sqlsrv_fetch_array($result)) {$deptname[$row['ID']] = $row['Name']; }
            
            $result = sqlsrv_query($connection, "SELECT ID, Description FROM recordtypes");
            while($row = sqlsrv_fetch_array($result)) {$recordtype[$row['ID']] = $row['Description']; }
            
            $requestquery = "SELECT * FROM records WHERE Active=1 AND (";
            $requestquerycount = 0;
            foreach($_SESSION['RCWarehouse_RequestedBoxes'] as $requestedbox) {
                $requestquerycount++;
                if($requestquerycount > 1) {$requestquery .= " OR"; }
                $requestquery .= " BoxID=" . $requestedbox;
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
                    echo '</tr>';
                }
                if($rowcount == 0) {echo '<tr><td colspan="6" style="padding: 20px;">No boxes in active record request.</td></tr>'; }
                echo '</table>';
            } else {echo $requestquery; }
            echo '<div class="buttonleft"><a href="index.php?pid=35&cancelrequest=1">Cancel Request</a></div>' . PHP_EOL;
            echo '</div>' . PHP_EOL;
        }
        
        echo '<div class="contentblock">';
        $form->displayForm('Submit Record Request');
        echo '</div>';
        
    ?>
</div>

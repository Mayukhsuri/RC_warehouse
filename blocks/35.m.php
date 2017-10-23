<div class="column60">
    <div class="contentblock">
        <?php
            
            $form->displayForm('Search');
            
        ?>
    </div>
    <?php
        
        if(isset($_SESSION['RCWarehouse_RequestedBoxes'])) {
            echo '<div class="contentblock" style="background-color: #e4ffec;">' . PHP_EOL;
            if(count($_SESSION['RCWarehouse_RequestedBoxes']) == 1) {$plural = ''; } else {$plural = 'es'; }
            echo '<h2>Current Request (' . count($_SESSION['RCWarehouse_RequestedBoxes']) . ' Box' . $plural . ')</h2>' . PHP_EOL;
            
            $result = sqlsrv_query($connection, "SELECT ID, Code, Name FROM properties");
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
            echo '<div class="buttonleft"><a href="index.php?pid=35&cancelrequest=1">Cancel Request</a><a href="index.php?pid=38">Finalize Request</a></div>' . PHP_EOL;
            echo '</div>' . PHP_EOL;
        }
        
        if($allowopenrequest == 1) {
            echo '<div class="contentblock">' . PHP_EOL;
            echo '<h2>Unlisted Records Request</h2>' . PHP_EOL;
            echo '<p>If you were unable to locate the necessary records in your search, click the button below and detail your request in the comments section on the following page.</p>' . PHP_EOL;
            echo '<div class="buttonleft"><a href="index.php?pid=38">Request Unlisted Boxes</a></div>' . PHP_EOL;
            echo '</div>' . PHP_EOL;
        }
        
        $tempuserid = $_SESSION['rcw_currentUserID'];

        $sql = "SELECT count(*) as ROW_COUNT FROM requests WHERE RequestedBy=$tempuserid AND Status>0 AND Status<9";
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
        $row_count = $row['ROW_COUNT'];

        $sql = "SELECT * FROM requests WHERE RequestedBy=$tempuserid AND Status>0 AND Status<9 ORDER BY RequestTime ASC";
        $result = sqlsrv_query($connection, $sql);

        if($row_count > 0) {
            $statustext[-1] = 'Canceled';
            $statustext[0] = 'On-Hold';
            $statustext[1] = 'Requested (Standard)';
            $statustext[2] = 'Requested (Urgent)';
            $statustext[3] = 'Pulling Boxes';
            $statustext[4] = 'En-Route / Delivering';
            $statustext[7] = 'Delivered';
            $statustext[8] = 'Return Requested';
            $statustext[9] = 'Complete';
            echo '<div class="contentblock">' . PHP_EOL;
            echo '<h2>Open Requests</h2>' . PHP_EOL;
            echo '<table>';
            echo '<tr>';
            echo '<th>Request Date</th>';
            echo '<th>Deliver To</th>';
            echo '<th>Status</th>';
            echo '</tr>';
            $rowcount = 0;
            while($row = sqlsrv_fetch_array($result)) {
                $rowcount++;
                if(($rowcount/2) == intval($rowcount/2)) {$style = ' class="alt" style="background-color: #d2e8dc"'; } else {$style=''; }
                echo '<tr onclick="location.href=\'index.php?pid=39&rid=' . $row['ID'] . '\'"' . $style . '>';
                echo '<td>' . date('n/j/Y g:ia',$row['RequestTime']) . '</td>';
                echo '<td>' . $row['DeliverTo'] . '</td>';
                echo '<td>' . $statustext[$row['Status']] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</div>' . PHP_EOL;
        }
        
    ?>
</div>

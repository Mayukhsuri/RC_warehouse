<div class="column80">
    <div class="contentblock">
        <?php
     
            $result = sqlsrv_query($connection, $query);
            echo '<p>(Select a specific record to add an individual box to your record request...)</p>';
            
            $result = sqlsrv_query($connection, $query);// the problem is caught on line 4.
           
        if($result) {
                echo '<table>';
                echo '<tr>';
                echo '<th>BoxID</th>';
                echo '<th>Box Barcode</th>';
                $rowcount = 0; 
                while($row = sqlsrv_fetch_array($result)) {
                    if((! isset($_SESSION['RCWarehouse_RequestedBoxes'])) || (! in_array($row['ID'],$_SESSION['RCWarehouse_RequestedBoxes']))) {
                            $rowcount++;
                            if(($rowcount/2) == intval($rowcount/2)) {$style = ' class="alt"'; } else {$style=''; }
                            echo '<tr onclick="location.href=\'index.php?pid=113&box=' . $row['Values'] . '\'"' . $style . '>';
                            echo '<td>' . $row['Values'] . '</td>';
                            echo '<td>' . $row['Barcode'] . '</td>';
                            echo '</tr>';
                            }
                }


                if($rowcount == 0) {echo '<tr class="nolink"><td colspan="6" style="padding: 20px;">No boxes in active record request.</td></tr>'; }
                echo '</table>';
            } else {
                echo '<p>No recent activity.</p>';
            }
            
            if($rowcount != 0) {echo '<div class="buttonleft"><a href="index.php?pid=112&requestall=1&loc='.$_SESSION['loc']. '&box=">Add All Boxes to Request</a><a href="#">Request Unlisted Boxes</a></div>' . PHP_EOL; }
        ?>
    </div>
     <?php
    
   
        
        if(isset($_SESSION['RCWarehouse_RequestedBoxes'])) {
            echo '<div class="contentblock" style="background-color: #e4ffec;">' . PHP_EOL;
            if(count($_SESSION['RCWarehouse_RequestedBoxes']) == 1) {$plural = ''; } else {$plural = 'es'; }
            echo '<h2>Current Request (' . count($_SESSION['RCWarehouse_RequestedBoxes']) . ' Box' . $plural . ')</h2>' . PHP_EOL;
         
            $requestquery = "SELECT ID, Barcode FROM boxes WHERE ID IN (";
            $requestquerycount = 0;
            foreach($_SESSION['RCWarehouse_RequestedBoxes'] as $requestedbox) {
                $requestquerycount++;
                if($requestquerycount > 1) {$requestquery .= ","; }
                $requestquery .= $requestedbox;
            }
            $requestquery .= ") ORDER BY ID ASC";
            $result = sqlsrv_query($connection, $requestquery);
            if($result) {
                echo '<table>';
                echo '<tr>';
                echo '<th>BoxID</th>';
                echo '<th>Barcode</th>';
                echo '</tr>';
                $rowcount = 0;
                while($row = sqlsrv_fetch_array($result)) {
                    $rowcount++;
                     if(($rowcount/2) == intval($rowcount/2)) {$style = ' class="alt nolink" style="background-color: #d2e8dc"'; } else {$style=' class="nolink"'; }
                    echo '<tr' . $style . '>';
                            echo '<td>' . $row['ID'] . '</td>';
                      echo '<td>' . $row['Barcode'] . '</td>';
                    echo '</tr>';
                }
                if($rowcount == 0) {echo '<tr><td colspan="6" style="padding: 20px;">No boxes in active record request.</td></tr>'; }
                echo '</table>';
            } else {echo $requestquery; }
            echo '<div class="buttonleft"><a href="index.php?pid=112&cancelrequest=1&loc=' . $_SESSION['loc'] . '&box=">Cancel Pick Request</a><a href="index.php?pid=114">Finalize Pick Request</a></div>' . PHP_EOL;
            echo '</div>' . PHP_EOL;
        }
        
    ?>
    
</div> 

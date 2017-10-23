<div class="column60">
    <div class="contentblock">
        <?php
            $form->displayForm('Update');
        ?>
    </div>
</div>
<div class="column80">
    <div class="contentblock">
        <?php
        
            echo '<h2>Request Details</h2>' . PHP_EOL;
            echo '<div class="spacer"></div>' . PHP_EOL;
            echo '<div class="datalist">' . PHP_EOL;
            
            $statustext[-1] = 'Canceled';
            $statustext[0] = 'On-Hold';
            $statustext[1] = 'Requested (Standard)';
            $statustext[2] = 'Requested (Urgent)';
            $statustext[3] = 'Pulling Boxes';
            $statustext[4] = 'En-Route / Delivering';
            $statustext[7] = 'Delivered';
            $statustext[8] = 'Return Requested';
            $statustext[9] = 'Complete';
            
            function dline($label,$data) {echo '<div class="dline"><div class="label">' . $label . '</div><div class="data">' . $data . '</div></div>' . PHP_EOL; }
            
            dline('Request ID',$row['ID']);
            $tempeeid = $row['RequestedBy'];
            $temprow = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT FirstName, LastName FROM users WHERE EmployeeID=$tempeeid"));
            dline('Requested By',$temprow['FirstName'] . ' ' . $temprow['LastName']);
            dline('Request Time',date('n/j/Y g:ia',$row['RequestTime']));
            dline('Last Activity',date('n/j/Y g:ia',$row['LastActivity']));
            
            //dline('Deliver To',$row['DeliverTo']);
            //dline('Location',$row['Location']);
            //if(strlen($row['Comments'])>0) {dline('Comments',$row['Comments']); }
            //dline('Status',$statustext[$row['Status']]);
            
            echo '<div class="spacer"></div>';
            
            if($row['BoxCount'] > 0) {
                
                $boxdataarray = explode(',',$row['BoxData']);
                $boxquerycount = 0;
                $boxquery = "SELECT ID, Barcode, LocationID, LastActivity FROM boxes WHERE";
                foreach($boxdataarray as $boxid) {
                    $boxquerycount++;
                    if($boxquerycount > 1) {$boxquery .= " OR"; }
                    $boxquery .= " ID=" . $boxid;
                }
                $result = sqlsrv_query($connection, $boxquery);
                $locationquery = "SELECT ID, Name FROM locations WHERE";
                $locationquerycount = 0;
                while($row = sqlsrv_fetch_array($result)) {
                    $boxcode[$row['ID']] = $row['Barcode'];
                    $boxlocation[$row['ID']] = $row['LocationID'];
                    $boxlastactivity[$row['ID']] = date('n/j/Y g:ia',$row['LastActivity']);
                    $locationquerycount++;
                    if($locationquerycount > 1) {$locationquery .= " OR"; }
                    $locationquery .= " ID=" . $row['LocationID'];
                }
                $result = sqlsrv_query($connection, $locationquery);
                while($row = sqlsrv_fetch_array($result)) {$locationname[$row['ID']] = $row['Name']; }
                
                echo '<table>';
                echo '<tr>';
                echo '<th>Box ID</th>';
                echo '<th>Last Activity</th>';
                echo '<th>Current Location</th>';
                echo '</tr>';
                $rowcount = 0;
                foreach($boxdataarray as $boxid) {
                    $rowcount++;
                    if(($rowcount/2) == intval($rowcount/2)) {$style = ' class="alt" style="background-color: #d2e8dc"'; } else {$style=''; }
                    echo '<tr' . $style . ' onclick="location.href=\'index.php?pid=34&barcode=' . $boxcode[$boxid] . '\'">';
                    echo '<td>' . $boxcode[$boxid] . '</td>';
                    echo '<td>' . $boxlastactivity[$boxid] . '</td>';
                    echo '<td>' . $locationname[$boxlocation[$boxid]] . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
                
            } else {
                echo '<p style="font-size: 13px; font-style: italic; padding-bottom: 0;">This request has no tracked boxes.  Boxes will be added into the system once they are located.</p>';
            }
            
            
        ?>
        </div>
    </div>
</div>

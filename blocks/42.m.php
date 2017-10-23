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
            
            function dline($label,$data) {echo '<div class="dline"><div class="label">' . $label . '</div><div class="data">' . $data . '</div></div>' . PHP_EOL; }
            
            echo '<h2>Box Contents</h2>' . PHP_EOL;
            echo '<div class="linklist">' . PHP_EOL;
            echo '<a href="index.php?pid=42&rec=' . $boxid . '&com=1&con=0">Complete this Box</a>&nbsp&nbsp&nbsp' . PHP_EOL;
            echo '<a href="index.php?pid=43&rec=' . $boxid . '">Edit Box Information</a>' . PHP_EOL;
            echo '</div>' . PHP_EOL;
            echo '<div class="spacer"></div>' . PHP_EOL;
            echo '<div class="datalist">' . PHP_EOL;
            dline('Box Barcode',$boxcode);
            dline('Description',$boxname);
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM locations WHERE ID=$locationid"));
            dline('Location',$row['Name']);
            $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT FirstName, LastName FROM users WHERE EmployeeID=$startedbyid"));
            dline('Started By',$row['LastName'] . ', ' . $row['FirstName']);
            echo '</div>';

            $sql = "SELECT * FROM records WHERE BoxID=$boxid AND Active=1 ORDER BY StartDate ASC, ID ASC";

            $result = sqlsrv_query($connection, $sql);

            if(! sqlsrv_has_rows($result)) {
                echo '<p>No records in box.</p>' . PHP_EOL;
            } else {
                echo '<table>' . PHP_EOL;
                echo '<tr>' . PHP_EOL;
                echo '<th>Record Type</th>' . PHP_EOL;
                echo '<th>Property</th>' . PHP_EOL;
                echo '<th>Start Date</th>' . PHP_EOL;
                echo '<th>End Date</th>' . PHP_EOL;
                echo '<th style="text-align: center;"></th>' . PHP_EOL;
                echo '</tr>' . PHP_EOL;
                $resultcount = 0;
                while($row = sqlsrv_fetch_array($result)) {
                    $resultcount++;
                    if(($resultcount / 2) == intval($resultcount / 2)) {$classname = ' class="alt nolink"'; } else {$classname = ' class="nolink"'; }
                    echo '<tr' . $classname . '>' . PHP_EOL;
                    echo '<td>' . $recordtypename[$row['RecordTypeID']] . '</td>' . PHP_EOL;
                    echo '<td>' . $propertyname[$row['PropertyID']] . '</td>' . PHP_EOL;
                    echo '<td>' . date('n/j/Y',$row['StartDate']) . '</td>' . PHP_EOL;
                    echo '<td>' . date('n/j/Y',$row['EndDate']) . '</td>' . PHP_EOL;
                    echo '<td style="text-align: center;"><a href="index.php?pid=42&rec=' . $boxid . '&del=' . $row['ID'] . '&con=0">Delete</a>' . PHP_EOL;
                    echo '</tr>' . PHP_EOL;
                }
                echo '</table>' . PHP_EOL;
            }
            
        ?>
    </div>
    <div class="contentblock">
        <?php $form->displayForm('Add Record'); ?>
    </div>
</div>


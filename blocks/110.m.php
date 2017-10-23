<div class="column80">
    <div class="contentblock">
        <?php
            
           // include('includes/connection.php');
             
            
            
            echo '<h2>Destroy Data</h2>' . PHP_EOL;
            echo '<div class="linklist">';
            echo '<a href="index.php?pid=110">Last 24 Hours</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=110&list=7">Upcoming Seven Days</a>&nbsp&nbsp&nbsp';
            echo '<a href="index.php?pid=110&list=30">Upcoming One Month</a>';
            //echo '<a href="index.php?pid=110&export=1">Export In Excel</a>';
            echo '</div>' . PHP_EOL;
            echo '<div class="spacer"></div>' . PHP_EOL;
            $tfilter = '';
            if(isset($_GET['list']))
            { if ($_GET['list'] == 7){$tfilter = "DATEADD(day, 7, GETDATE())"; } else if ($_GET['list'] == 30)  {$tfilter = "DATEADD(month, 1, GETDATE())";}
            } 
        
               else {$tfilter = "DATEADD(DAY, 1, GETDATE())";}
            $sql = "  SELECT *, DATEADD(S, [DestroyDate], '1970-01-01') as newDestroyDate,  l.Name, u.FirstName, u.LastName FROM boxes b
                       JOIN locations l ON b.LocationID = l.ID 
					   JOIN users u ON u.EmployeeID = b.StartedBy
                    WHERE DATEADD(S, [DestroyDate], '1970-01-01') BETWEEN DATEADD(S, 1, GETDATE()) AND  " . $tfilter . " AND DestroyDate <> 2079549306";
            $result = sqlsrv_query($connection, $sql);
            $row_number = sqlsrv_num_rows($result);
            if($row_number < 1) {
               echo '<table>' . PHP_EOL;
                echo '<tr>' . PHP_EOL;
                echo '<th>Box ID </th>' . PHP_EOL;
                echo '<th>Box Name</th>' . PHP_EOL;
                echo '<th>Box Barcode</th>' . PHP_EOL;
                echo '<th>Location Of the Box</th>' . PHP_EOL;
                echo '<th>Shelf</th>' . PHP_EOL;
                echo '<th>Bay</th>' . PHP_EOL;
                echo '<th>Row</th>' . PHP_EOL;
                echo '<th>Started</th>' . PHP_EOL;
                echo '<th>By</th>' . PHP_EOL;
                echo '</tr>' . PHP_EOL;
                $resultcount = 0;
                while($row = sqlsrv_fetch_array($result)) {
                    $resultcount++;
                  
                    if(($resultcount / 2) == intval($resultcount / 2)) {$classname = ' class="alt"'; } else {$classname = ''; }
                    echo '<td>' . $row['ID'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['Name'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['Barcode'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['Name'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['Shelf'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['Bay'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['Row'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['FirstName'] . '</td>' . PHP_EOL;
                    echo '<td>' . $row['LastName'] . '</td>' . PHP_EOL;
                    
                    echo '</tr>' . PHP_EOL;
                }
                echo '</table>' . PHP_EOL;
            } else {
                echo '<p>No list found.</p>' . PHP_EOL;
            }
        
       
            
        
        ?>
    </div>
</div>

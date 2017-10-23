<div class="column60">
     <div class="contentblock">
     <?php
            
     function loctypetext($type) {
     switch($type) {
     case 'W': $output = 'Warehouse'; break;
     case 'O': $output = 'Field Location'; break;
     case 'C': $output = 'Courier'; break;
     case 'T': $output = 'Temporary'; break;
     case 'D': $output = 'Destruction'; break;
     case 'P': $output = 'Pending'; break;
     default: $output = 'Unknown';
     }
     return $output;
 }
            
 // include('includes/connection.php');
echo '<h2>Warehouse Location Labels</h2>' . PHP_EOL;
echo '<div class="linklist">';
echo '<a href="index.php?pid=74">Field Locations</a>&nbsp&nbsp&nbsp';
echo '<a href="index.php?pid=75">Warehouse Locations</a>&nbsp&nbsp&nbsp';
echo '<a href="index.php?pid=76">Boxes</a>&nbsp&nbsp&nbsp';
echo '<a href="index.php?pid=77">Printfiles</a>';
echo '</div>' . PHP_EOL;
echo '<div class="spacer"></div>' . PHP_EOL;

$sql = "SELECT ID, Name FROM warehouses";
write_log(__FILE__,__LINE__,$sql);
$result = sqlsrv_query($connection, $sql);

while($row = sqlsrv_fetch_array($result)) {
    $whName[$row['ID']] = $row['Name'];
}

//$sql = "SELECT * FROM locations WHERE Active=1 AND LocationType='W'"; // GROUP BY Warehouse, Row
$sql = "SELECT distinct Warehouse, Row FROM locations WHERE Active=1 AND LocationType='W' order by Warehouse, Row"; // GROUP BY Warehouse, Row
write_log(__FILE__,__LINE__,$sql);
$result = sqlsrv_query($connection, $sql);
            
$row_count = 0; //sqlsrv_num_rows($result);    

$html = '';
$html .= '<table>' . PHP_EOL;
$html .= '<tr>' . PHP_EOL;
$html .= '<th>Warehouse</th>' . PHP_EOL;
$html .= '</tr>' . PHP_EOL;

while($row = sqlsrv_fetch_array($result)) {
    $row_count++;
    if (($row_count % 2) == 0) {
        $classname = ' class="alt"';
    } else {
        $classname = '';
    }
    $html .= '<tr onclick="location.href=\'index.php?pid=75&rec=' . $row['Warehouse'] . '&row=' . $row['Row'] . '\'"' . $classname . '>' . PHP_EOL;
    $html .= '<td>' . $whName[$row['Warehouse']] . ' - Row ' . $row['Row'] . '</td>' . PHP_EOL;
    //$html .= '<td>' . $whName[$row['Warehouse']] . ' - Row ' . $row['Row'] . ' - Bay ' . $row['Bay'] . ' - Shelf ' . $row['Shelf'] . '</td>' . PHP_EOL;
    $html .= '</tr>' . PHP_EOL;
}
$html .= '</table>' . PHP_EOL;

if ($row_count) {
write_log(__FILE__,__LINE__,"row_count final: $row_count");
    echo $html;
} else {
    echo '<p>No records found.</p>' . PHP_EOL;
}
            
/* $row_count = 0; //sqlsrv_num_rows($result);     */
/* write_log(__FILE__,__LINE__,"Row Count: $row_count"); */
/* if($row_count < 1) { */
/*     echo '<table>' . PHP_EOL; */
/*     echo '<tr>' . PHP_EOL; */
/*     echo '<th>Warehouse</th>' . PHP_EOL; */
/*     echo '</tr>' . PHP_EOL; */
/*     $resultcount = 0; */
/*     while($row = sqlsrv_fetch_array($result)) { */
/*         $resultcount++; */
/*         if(($resultcount / 2) == intval($resultcount / 2)) {$classname = ' class="alt"'; } else {$classname = ''; } */
/*         echo '<tr onclick="location.href=\'index.php?pid=75&rec=' . $row['Warehouse'] . '&row=' . $row['Row'] . '\'"' . $classname . '>' . PHP_EOL; */
/*         echo '<td>' . $whName[$row['Warehouse']] . ' - Row ' . $row['Row'] . '</td>' . PHP_EOL; */
/*         echo '</tr>' . PHP_EOL; */
/*     } */
/*     echo '</table>' . PHP_EOL; */
/* } else { */
/*     echo '<p>No records found.</p>' . PHP_EOL; */
/* } */
            
?>
</div>
</div>

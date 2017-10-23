<div class="column60">
     <div class="contentblock">
     <?php
            
     write_log(__FILE__,__LINE__);


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
            
include('includes/connection.php');
echo '<h2>Labels to Print</h2>' . PHP_EOL;
echo '<div class="linklist">';
echo '<a href="index.php?pid=74">Field Locations</a>&nbsp&nbsp&nbsp';
echo '<a href="index.php?pid=75">Warehouse Locations</a>&nbsp&nbsp&nbsp';
echo '<a href="index.php?pid=76">Boxes</a>&nbsp&nbsp&nbsp';
echo '<a href="index.php?pid=77">Printfiles</a>';
echo '</div>' . PHP_EOL;
echo '<div class="spacer"></div>' . PHP_EOL;
            
function tableLine($linktext,$path,$linkcount,$removelink) {
    if(($linkcount / 2) == intval($linkcount / 2)) {$classname = ' class="alt"'; } else {$classname = ''; }
    $output = '<tr onclick="location.href=\'' . $path . '\'"' . $classname . '>' . PHP_EOL;
    $output .= '<td>' . $linktext . '</td>' . PHP_EOL;
    $output .= '<td style="text-align: right;"><a href="' . $removelink . '">Clear</a></td>' . PHP_EOL;
    $output .= '</tr>' . PHP_EOL;
    return $output;
}
            
$tableRows = '';
$linkcount = 0;
            
$sql = "SELECT COUNT(ID) as count FROM locations WHERE Active=1 AND LabelPrinted=0 AND LocationType!='W'";
write_log(__FILE__,__LINE__,$sql);
$row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
$lclabelcount = $row['count'];
write_log(__FILE__,__LINE__,"Field Location count: $lclabelcount");
if($lclabelcount > 0) {
    $linkcount++;
    $removelink = 'index.php?pid=77&rl=0';
    $tableRows .= tableLine('Field Location Labels (' . $lclabelcount . ')','printfiles/NonWHOutletLabels.php',$linkcount,$removelink);
}
            
$sql = "SELECT ID, Name FROM warehouses";
write_log(__FILE__,__LINE__,$sql);
$result = sqlsrv_query($connection, $sql);
while($row = sqlsrv_fetch_array($result)) {
    $whName[$row['ID']] = $row['Name'];
}

$sql = "SELECT Warehouse, Row FROM locations WHERE Active=1 AND LabelPrinted=0 AND LocationType='W' GROUP BY Warehouse, Row";
write_log(__FILE__,__LINE__,$sql);
$result = sqlsrv_query($connection, $sql);
while($row = sqlsrv_fetch_array($result)) {
    $linkcount++;
    $removelink = 'index.php?pid=77&rl=' . $row['Warehouse'] . '&rr=' . $row['Row'];
    $tableRows .= tableLine($whName[$row['Warehouse']] . ' - Row ' .
                            $row['Row'],'printfiles/WarehouseOutletLabels.php?wh=' .
                            $row['Warehouse'] . '&row=' . $row['Row'],$linkcount,$removelink);
}
            
write_log(__FILE__,__LINE__);
if($linkcount < 1) {
    echo '<p>No labels marked for printing.</p>' . PHP_EOL;
} else {
    echo '<table>' . PHP_EOL;
    echo '<tr>' . PHP_EOL;
    echo '<th>Label Group</th>' . PHP_EOL;
    echo '<th></th>';
    echo '</tr>' . PHP_EOL;
    echo $tableRows;
    echo '</table>' . PHP_EOL;
}
            
?>
</div>
</div>

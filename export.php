<?php
 
session_start();
require_once('includes/settings.php'); 
require_once('includes/g1functions.php');
require_once('includes/connection.php');

write_log(__FILE__,__LINE__);

ob_start();

//
// For the future...  What if the 'lastactivity' element held the name
// of a stored procedure, or we defined a new get field, then executed
// the stored procedure and returned the results dynamically.  This
// would make this script more generalizable for exporting data.
//

$tfilter = $_GET['lastactivity'];
        
$sql = "SELECT count(*) as ROW_COUNT FROM vwWarehouseBoxPickList where [ReqStatus] <> 'Delivered'";

$row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
$row_count = $row['ROW_COUNT'];
$sql = "SELECT [RequestID]
                         ,[ReqStatus]
                         ,[RequestBy]
                         ,[RequestTime]
                         ,[LocationBarcode]
                         ,[Warehouse]
                         ,[Row]
                         ,[Bay]
                         ,[Shelf]
                         ,[LocationName]
                         ,[BoxBarcode]
                         ,[BoxStatus]
                  FROM [dbo].[vwWarehouseBoxPickList]
                  WHERE ReqStatus in ('Standard' , 'Urgent')"
    . $tfilter .
    " ORDER BY Warehouse,Row,Bay,Shelf";
 
write_log(__FILE__,__LINE__,$sql);

$result = sqlsrv_query($connection, $sql);
$row_number = $row_count;
$output ='';
if($row_number >= 1) {
 
    $output = '';                
    while($row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC))
        {
            if (strlen($output) == 0) {
                $row_headings = array_keys($row);
                $output = join(',', $row_headings) . "\n";
            }

            //
            // The array_values can return an object, which would most
            // likely to be of class DateTime, which needs to be
            // specifically converted to a date string or it throws an
            // error, so we cannot just join, we have to itterate over
            // all the columns and check their datatype.
            //
            $values = array();
            foreach (array_values($row) as $value ) {
                if (gettype($value) == 'object') {
                    if (get_class($value) == 'DateTime') {
                        $value = $value->format('Y-m-d H:i:s');
                    } else {
                        $value = 'Unknown data type';
                    }
                }
                array_push($values, $value);
            }
            $output .= join(',', array_values($values)) . "\n";
        }

    header('Content-Type: application/CSV');
    header('Content-Disposition: attachment; filename=download.csv');
    /* 
       header('Content-Type: application/vnd.ms-excel');
       header('Content-Type: application/xls');
       header('Content-Disposition: attachment; filename=download.xlsx'); 
    */
    
    echo $output; 
}
?>
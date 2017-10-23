<?php
    
    include('includes/ah_form.php');
    
    $form = new ah_form('createPickUprequest');
    $form->addTitle('Create Pick up request');
 
    $form->addOption(0,'All Location Types');
    $result = sqlsrv_query($connection, "WITH CTE 
                                            AS
                                            (
                                            SELECT Distinct Location FROM [RCWarehouse].[dbo].[requests]
                                            )
                                            SELECT ROW_NUMBER() OVER (ORDER BY Location) AS ID, Location FROM CTE
                                            ");
    while($row=sqlsrv_fetch_array($result)) {$form->addOption($row['ID'],$row['Location']);}
    $form->selectInput('location','Location');
    if(isset($_GET['rs'])) {$ALERT_CONFIRM = 'Box request successful.'; }
    if($form->formValidated == 1) {
        
        $loca = $form->validated['location']['clean'];
        
       
$query = "IF OBJECT_ID('tempdb.dbo.#temp', 'U') IS NOT NULL
  DROP TABLE #temp;

 WITH CTE 
 AS
  (
   SELECT Distinct Location FROM [RCWarehouse].[dbo].[requests]
     )
    SELECT ROW_NUMBER() OVER (ORDER BY Location) AS ID, Location 
	Into #temp
	FROM CTE";
    sqlsrv_query($connection, $query);
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT * FROM #temp WHERE ID='$loca'"));
    
        $l = $row['Location'];
        $_SESSION['loc'] = $l;
        
       header('Location: index.php?pid=112&loc=' . $l . '&box='); exit;
    
    }

?>

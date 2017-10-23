<?php

    if(isset($_GET['rid']) && is_numeric($_GET['rid'])) {
        $requestid = $_GET['rid'];
        $row = sqlsrv_fetch_array(sqlsrv_query($connection, "SELECT *, NewStatus=(CASE Status 
	   WHEN 0 THEN 'On hold' 
           WHEN 1 THEN 'Requested (Standard)'
           WHEN 2 THEN 'Requested (Urgent)'
           WHEN 3 THEN 'Pulling Boxes'
           WHEN 4 THEN 'En-Route / Delivering'
           WHEN 7 THEN 'Delivered'
           WHEN 8 THEN 'Return Requested'
           WHEN 9 THEN 'Complete'
           ELSE 'Canceled' 
           END) FROM requests WHERE ID=$requestid"));
        if(!$row) {
            header('Location: index.php?pid=100'); exit;
        }
    }
    
    include('includes/ah_form.php');
    
    $form = new ah_form('UpdateRequest');
    $form->addTitle('Update Request');
    function statuscheck($a,$b) {if($a == $b) {return 1; } else {return 0; } }
    $form->addOption(399,'Canceled',statuscheck(399,$row['Status']));
    $form->addOption(400,'On-Hold',statuscheck(400,$row['Status']));
    $form->addOption(1,'Requested (Standard)',statuscheck(1,$row['Status']));
    $form->addOption(2,'Requested (Urgent)',statuscheck(2,$row['Status']));
    $form->addOption(3,'Pulling Boxes',statuscheck(3,$row['Status']));
    $form->addOption(4,'En-Route / Delivering',statuscheck(4,$row['Status']));
    $form->addOption(7,'Delivered',statuscheck(7,$row['Status']));
    $form->addOption(8,'Return Requested',statuscheck(8,$row['Status']));
    $form->addOption(9,'Complete',statuscheck(9,$row['Status']));
    $form->selectInput('status','Status');
    $form->textInput('deliverto','Deliver To',$row['DeliverTo']);
    $form->textInput('location','Location',$row['Location']);
    $form->setAllowBlank(1);
    $form->textAreaInput('comments','Comments',str_replace('<br />',PHP_EOL,str_replace('<br/>',PHP_EOL,$row['Comments'])));
    
    if($form->formValidated == 1) {
        $newstatus = $form->validated['status']['clean'];
        if($newstatus == 399) {$newstatus = -1; }
        if($newstatus == 400) {$newstatus = 0; }
        $newdeliverto = $form->validated['deliverto']['clean'];
        $newlocation = $form->validated['location']['clean'];
        $newcomments = $form->validated['comments']['clean'];
        $currenttime = time();
        sqlsrv_query($connection, "UPDATE requests SET DeliverTo='$newdeliverto', Location='$newlocation', Comments='$newcomments', Status=$newstatus, LastActivity=$currenttime WHERE ID=$requestid");
        header('Location: index.php?pid=100'); exit;
    }

?>

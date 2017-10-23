<?php
    
  //  include('includes/connection.php');
    include('includes/ah_form.php');
    
    if(isset($_GET['cancelrequest'])) {unset($_SESSION['RCWarehouse_RequestedBoxes']); header('Location: index.php?pid=111'); exit; } 
    
    $form = new ah_form('finalizerequest');
    $form->addTitle('Finalize Request');
    
    $form->textInput('deliverto','Deliver To',$_SESSION['rcw_userFullName']);
    $form->textInput('address','Address / Location');
    $form->addOption(1,'Standard');
    $form->addOption(2,'Urgent');
    $form->selectInput('urgency','Urgency');
    $form->setAllowBlank(1);
    $form->textAreaInput('comments','Comments (Optional)');
    if($form->formValidated == 1) {
        $deliverto = $form->validated['deliverto']['clean'];
        $address = $form->validated['address']['clean'];
        $comments = $form->validated['comments']['clean'];
        $urgency = $form->validated['urgency']['clean'];
        $currenttime = time();
        $currentuserid = $_SESSION['rcw_currentUserID'];
        $boxdata = '';
        $boxcount = 0;
        $_SESSION['RCWarehouse_RequestedBoxes'] = array_unique($_SESSION['RCWarehouse_RequestedBoxes']);
        foreach($_SESSION['RCWarehouse_RequestedBoxes'] as $boxtemp) {
            $boxcount++;
            if($boxcount > 1) {$boxdata .= ','; }
            $boxdata .= $boxtemp;
        }
        $query = "INSERT INTO requests (RequestedBy, RequestTime, DeliverTo, Location, Comments, Urgency, BoxData, BoxCount, Status, DeliveredBy, DeliverTime, LastActivity";
        $query .= ") VALUES ($currentuserid, $currenttime, '$deliverto', '$address', '$comments', $urgency, '$boxdata', $boxcount, $urgency, -1, -1, $currenttime)";

       
        $query .= "; SELECT SCOPE_IDENTITY() AS IDENTITY_COLUMN_NAME"; 

        $res = sqlsrv_query($connection, $query);

        $ID = lastId($res);

       
        foreach($_SESSION['RCWarehouse_RequestedBoxes'] as $boxtemp) {
            $sql = "INSERT INTO dbo.BoxRequests (RequestID, BoxID, Status) values ($ID, $boxtemp, $urgency)";
            $res = sqlsrv_query($connection, $sql);
        }

        unset($_SESSION['RCWarehouse_RequestedBoxes']);
        header('Location: index.php?pid=111&rs=1'); exit;
    }
    
?>

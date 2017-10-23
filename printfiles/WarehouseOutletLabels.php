<?php

  //session_start();

if(!isset($_SESSION['rcw_userLoggedIn'])) {
    //    die('Unable to generate label.  Please log in to the system.');
}

include('../includes/settings.php');
include('../includes/g1functions.php');
include('../includes/connection.php');


write_log(__FILE__,__LINE__);

include('../includes/barcode/php-barcode.php');
require('../includes/fpdf/fpdf.php');

  /* include('../includes/connection.php'); */

  /* require('../includes/fpdf/fpdf.php'); */

  
  // -------------------------------------------------- //
  //            ALLOCATE FPDF RESSOURCE
  // -------------------------------------------------- //
    
  $pdf = new FPDF('P', 'pt');
  $pdf->AddPage();
  $pdf->SetMargins(0,0);
  
  // -------------------------------------------------- //
  //                      BARCODE
  // -------------------------------------------------- //
  
  function locationLabel($pdf,$x,$y,$warehouse,$row,$bay,$shelf,$code) {
 write_log(__FILE__,__LINE__);
    $data = Barcode::fpdf($pdf, '000000', $x+130, $y+110, 0, 'code128', array('code'=>$code), 2, 50);
 write_log(__FILE__,__LINE__);
    $pdf->SetFont('Arial','',10);
    $pdf->SetXY($x+70,$y+50);
    $pdf->Cell(200,10,$warehouse,0,1,'L');
    $pdf->SetXY($x+70,$y+65);
    $pdf->Cell(100,10,'ROW ' . $row . '       BAY ' . $bay,0,1,'L');
    $pdf->SetXY($x+140,$y+65);




    $pdf->Cell(100,10,$code,0,1,'R');
    $pdf->SetFont('Arial','',18);
    $pdf->SetFont('Arial','B',20);
    $pdf->SetTextColor(255);
    $pdf->SetXY($x+20, $y+50);
    $pdf->Cell(40,25,$shelf,1,0,'C',true);
    $pdf->SetTextColor(0);
  }
  
  $lWidth = 310;
  $lHeight = 157;
  
  $pos[1][0] = 10;              $pos[1][1] = 2;
  $pos[2][0] = 10 + $lWidth;    $pos[2][1] = 2;
  $pos[3][0] = 10;              $pos[3][1] = 2 + ($lHeight);
  $pos[4][0] = 10 + $lWidth;    $pos[4][1] = 2 + ($lHeight);
  $pos[5][0] = 10;              $pos[5][1] = 2 + ($lHeight * 2);
  $pos[6][0] = 10 + $lWidth;    $pos[6][1] = 2 + ($lHeight * 2);
  $pos[7][0] = 10;              $pos[7][1] = 2 + ($lHeight * 3);
  $pos[8][0] = 10 + $lWidth;    $pos[8][1] = 2 + ($lHeight * 3);
  $pos[9][0] = 10;              $pos[9][1] = 2 + ($lHeight * 4);
  $pos[10][0] = 10 + $lWidth;   $pos[10][1] = 2 + ($lHeight * 4);
  
  $result = sqlsrv_query($connection, "SELECT * FROM warehouses");
  while($row = sqlsrv_fetch_array($result)) {$whname[$row['ID']] = $row['Name']; }
  

  //$start = 1071;
  //$end = 2270;
  
  $labelsThisPage = 0;
  //$result = sqlsrv_query("SELECT * FROM locations WHERE ID>=$start AND ID<=$end AND Active=1");
  if(isset($_GET['wh']) && is_numeric($_GET['wh']) && isset($_GET['row']) && is_numeric($_GET['row'])) {
    $whid = $_GET['wh'];
    $rowid = $_GET['row'];

    $sql = "SELECT * FROM locations WHERE LabelPrinted=0 AND Active=1 AND LocationType='W' AND Warehouse=$whid AND Row=$rowid ORDER BY LocationType ASC, Warehouse ASC, Row ASC, Bay ASC, Shelf ASC, Name ASC";
 write_log(__FILE__,__LINE__,$sql);
 $result = sqlsrv_query($connection, $sql);
  } else {
    $sql = "SELECT * FROM locations WHERE LabelPrinted=0 AND Active=1 AND LocationType='W' ORDER BY LocationType ASC, Warehouse ASC, Row ASC, Bay ASC, Shelf ASC, Name ASC";
    write_log(__FILE__,__LINE__,$sql);
    $result = sqlsrv_query($connection, $sql);
  }
 write_log(__FILE__,__LINE__);
  while($row = sqlsrv_fetch_array($result)) {
    $labelsThisPage++;
 write_log(__FILE__,__LINE__);  
 // write_log(__FILE__,__LINE__,print_r($pdf));
 write_log(__FILE__,__LINE__,$pos[$labelsThisPage][0]);
 write_log(__FILE__,__LINE__,$pos[$labelsThisPage][1]);
 write_log(__FILE__,__LINE__,$whname[$row['Warehouse']]);
 write_log(__FILE__,__LINE__,$row['Row']);
 write_log(__FILE__,__LINE__,$row['Bay']);
 write_log(__FILE__,__LINE__,$row['Shelf']);
 write_log(__FILE__,__LINE__,$row['Barcode']);




    locationLabel($pdf,$pos[$labelsThisPage][0],$pos[$labelsThisPage][1],$whname[$row['Warehouse']],$row['Row'],$row['Bay'],$row['Shelf'],$row['Barcode']);
 write_log(__FILE__,__LINE__);  


    if($labelsThisPage == 10) {
      $labelsThisPage = 0;
      $pdf->addPage();
    }
  }



  $pdf->Output();



?>

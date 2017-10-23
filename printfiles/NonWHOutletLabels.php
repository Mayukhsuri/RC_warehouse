<?php

session_start();

if(!isset($_SESSION['rcw_userLoggedIn'])) {
    die('Unable to generate label.  Please log in to the system.');
}

include('../includes/settings.php');
include('../includes/g1functions.php');
include('../includes/connection.php');



write_log(__FILE__,__LINE__,"");
include('../includes/barcode/php-barcode.php');
require('../includes/fpdf/fpdf.php');

  /* include('../includes/connection.php'); */
  /* include('../includes/barcode/php-barcode.php'); */
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
  
  function nonWHLabel($pdf,$x,$y,$type,$name,$code) {
    $data = Barcode::fpdf($pdf, '000000', $x+130, $y+110, 0, 'code128', array('code'=>$code), 2, 50);
    switch($type) {
      case 'W': $typetext = 'Warehouse'; break;
      case 'O': $typetext = 'Originator'; break;
      case 'C': $typetext = 'Courier'; break;
      case 'T': $typetext = 'Temporary'; break;
      case 'D': $typetext = 'Destruction'; break;
      case 'P': $typetext = 'Pending'; break;
      default: $typetext = 'Unknown';
    }
    $pdf->SetXY($x+20,$y+35);
    $pdf->SetFont('Arial','B',36);
    $pdf->Cell(40,40,$type,1,0,'C');
    $pdf->SetFont('Arial','',12);
    $pdf->SetXY($x+70,$y+50);
    $pdf->Cell(200,10,$name,0,1,'L');
    $pdf->SetXY($x+70,$y+65);
    $pdf->Cell(200,10,$typetext,0,1,'L');
    $pdf->SetFont('Arial','',18);
    $pdf->SetXY($x+20,$y+145);
    $pdf->Cell(200,10,$code,0,0,'L');
    $pdf->SetFont('Arial','B',20);
    $pdf->SetXY($x+120, $y+50);
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
  
  //$start = 1071;
  //$end = 2270;
  
  $labelsThisPage = 0;
  //$result = sqlsrv_query("SELECT * FROM locations WHERE ID>=$start AND ID<=$end AND Active=1");
  $result = sqlsrv_query($connection, "SELECT * FROM locations WHERE LabelPrinted=0 AND Active=1 AND LocationType!='W' ORDER BY LocationType ASC, Warehouse ASC, Row ASC, Bay ASC, Shelf ASC, Name ASC");
  while($row = sqlsrv_fetch_array($result)) {
    $labelsThisPage++;
    nonWHLabel($pdf,$pos[$labelsThisPage][0],$pos[$labelsThisPage][1],$row['LocationType'],$row['Name'],$row['Barcode']);
    if($labelsThisPage == 10) {
      $labelsThisPage = 0;
      $pdf->addPage();
    }
  }
  
  $pdf->Output();
?>

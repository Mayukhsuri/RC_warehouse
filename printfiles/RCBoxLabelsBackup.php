<?php

  session_start();
  if(!isset($_SESSION['rcw_userLoggedIn'])) {die('Unable to generate labels.  Please log in to the system.'); }

  include('../includes/connection.php');
  include('../includes/g1functions.php');
  if(!isset($_GET['token'])) {die('Unable to generate labels.  Please contact a system administrator.'); }
  $token = cleantext($_GET['token']);
  
  $row = sqlsrv_fetch_array(sqlsrv_query("SELECT * FROM boxes WHERE Token='$token'"));
  if(!$row) {die('Unable to generate labels.  Please contact an administrator.'); }
  
  include('../includes/barcode/php-barcode.php');
  require('../includes/fpdf/fpdf.php');

  // -------------------------------------------------- //
  //                  PROPERTIES
  // -------------------------------------------------- //
  
  //$x        = 200;  // barcode center (horizontal)
  //$y        = 200;  // barcode center (vertical)
  
  $height   = 50;   // barcode height in 1D ; module size in 2D
  $width    = 2;    // barcode height in 1D ; not use in 2D
  $angle    = 0;    // rotation in degrees
  $type     = 'code128';
  $black    = '000000'; // color in hexa
  
  $blocktype = 'B';
  
  
  // -------------------------------------------------- //
  //            ALLOCATE FPDF RESSOURCE
  // -------------------------------------------------- //
    
  $pdf = new FPDF('P', 'pt');
  
  // -------------------------------------------------- //
  //                      BOXES
  // -------------------------------------------------- //
  
  function newCode($pdf,$x,$y,$code,$origin,$recordtype) {
    $data = Barcode::fpdf($pdf, '000000', $x+120, $y+90, 0, 'code128', array('code'=>$code), 2, 50);
    $pdf->SetXY($x,$y-30);
    $pdf->SetFont('Arial','B',20);
    $pdf->Cell(20,20,'',1);
    $pdf->SetXy($x+30,$y-30);
    $pdf->SetFont('Arial','',14);
    $pdf->Cell(100,20,'BOX COMPLETE');
    $pdf->SetXY($x,$y);
    $pdf->Cell(240, 160,'',1);
    $pdf->SetXY($x,$y+170);
    $pdf->SetFont('Arial','B',96);
    $pdf->Cell(240,96,substr($code,5,4),0,0,'C');
    $pdf->SetXY($x+20,$y+15);
    $pdf->SetFont('Arial','B',36);
    $pdf->Cell(40,40,'B',1,0,'C');
    $pdf->SetFont('Arial','',8);
    $pdf->SetXY($x+70,$y+30);
    $pdf->Cell(200,10,"BOX ORIGIN",0,1,'L');
    $pdf->SetFont('Arial','',12);
    $pdf->SetXY($x+70,$y+45);
    $pdf->Cell(200,10,$origin,0,1,'L');
    $pdf->SetFont('Arial','B',18);
    $pdf->SetXY($x+20,$y+130);
    $pdf->Cell(200,10,$code,0,0,'L');
    $pdf->SetFont('Arial','B',12);
    $pdf->SetXY($x+270,$y+5);
    $pdf->Cell(200,18,strtoupper($recordtype),0,0,'L');
    $pdf->SetFont('Arial','',6);
    $pdf->SetXY($x+270,$y+30);
    $pdf->Cell(200,40,'',1,0,'L');
    $pdf->SetXY($x+270,$y+33);
    $pdf->Cell(200,5,'PROPERTY',0,0,'L');
    $pdf->SetFont('Arial','',6);
    $pdf->SetXY($x+270,$y+75);
    $pdf->Cell(200,40,'',1,0,'L');
    $pdf->SetXY($x+270,$y+78);
    $pdf->Cell(200,5,'START DATE',0,0,'L');
    $pdf->SetFont('Arial','',6);
    $pdf->SetXY($x+270,$y+120);
    $pdf->Cell(200,40,'',1,0,'L');
    $pdf->SetXY($x+270,$y+123);
    $pdf->Cell(200,5,'END DATE',0,0,'L');
  }
  
  function newPage($pdf) {
    $pdf->AddPage();
  }
  
  $row = sqlsrv_fetch_array(sqlsrv_query("SELECT ID, LocationID FROM boxes WHERE Token='$token' LIMIT 1"));
  $sampleboxid = $row['ID'];
  $locationid = $row['LocationID'];
  $row = sqlsrv_fetch_array(sqlsrv_query("SELECT RecordTypeID FROM records WHERE BoxID=$sampleboxid LIMIT 1"));
  $recordtypeid = $row['RecordTypeID'];
  $row = sqlsrv_fetch_array(sqlsrv_query("SELECT Description FROM recordtypes WHERE ID=$recordtypeid"));
  $recordname = $row['Description'];
  $row = sqlsrv_fetch_array(sqlsrv_query("SELECT Name FROM locations WHERE ID=$locationid"));
  $origin = $row['Name'];
  
  $recordcount = 0;
  $result = sqlsrv_query("SELECT * FROM boxes WHERE Token='$token'");
  while($row = sqlsrv_fetch_array($result)) {
    $recordcount++;
    if(($recordcount/2) == intval($recordcount/2)) {$y=500; } else {$pdf->AddPage(); $y=80; }
    newCode($pdf,50,$y,$row['Barcode'],$origin,$recordname);
  }
  
  $pdf->Output();
  
?>

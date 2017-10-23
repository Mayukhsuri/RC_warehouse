<?php
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
    
  $pdf = new FPDF('L', 'pt');
  $pdf->AddPage();
  
  // -------------------------------------------------- //
  //                      BOXES
  // -------------------------------------------------- //
  
  function newCode($pdf,$x,$y,$code,$recordtype) {
    $data = Barcode::fpdf($pdf, '000000', $x+120, $y+90, 0, 'code128', array('code'=>$code), 2, 50);
    $pdf->SetXY($x,$y-30);
    $pdf->Cell(20,20,'',1);
    $pdf->SetXy($x+30,$y-30);
    $pdf->SetFont('Arial','',14);
    $pdf->Cell(100,20,'BOX COMPLETE');
    $pdf->SetXY($x,$y);
    $pdf->Cell(240, 160,'',1);
    $pdf->SetXY($x+20,$y+15);
    $pdf->SetFont('Arial','B',36);
    $pdf->Cell(40,40,'B',1,0,'C');
    $pdf->SetFont('Arial','',12);
    $pdf->SetXY($x+70,$y+30);
    $pdf->Cell(200,10,"BOX ORIGIN:",0,1,'L');
    $pdf->SetXY($x+70,$y+45);
    $pdf->Cell(200,10,"ONE HARRAH'S COURT",0,1,'L');
    $pdf->SetFont('Arial','B',18);
    $pdf->SetXY($x+20,$y+130);
    $pdf->Cell(200,10,$code,0,0,'L');
    $pdf->SetFont('Arial','B',20);
    $pdf->SetXY($x,$y+190);
    $pdf->Cell(200,20,$recordtype,0,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->SetDrawColor(150);
    $pdf->SetFillColor(215);
    $pdf->SetXY($x,$y+220);
    $pdf->Cell(300,15,'PROPERTY','TRL',2,'L',true);
    $pdf->Cell(300,50,'','RBL',0,'L',true);
    $pdf->SetXY($x,$y+300);
    $pdf->Cell(300,15,'START DATE','TRL',2,'L',true);
    $pdf->Cell(300,50,'','RBL',0,'L',true);
    $pdf->SetXY($x,$y+380);
    $pdf->Cell(300,15,'END DATE','TRL',2,'L',true);
    $pdf->Cell(300,50,'','RBL',0,'L',true);
  }
  
  function newPage($pdf) {
    $pdf->AddPage();
  }
  
  newCode($pdf,50,80,'B00060001','SLOT AUDIT');
  newCode($pdf,490,80,'B00060002','SLOT AUDIT');
  newPage($pdf);
  newCode($pdf,50,80,'B00060003','SLOT AUDIT');
  newCode($pdf,490,80,'B00060004','SLOT AUDIT');
  newPage($pdf);
  newCode($pdf,50,80,'B00060005','SLOT AUDIT');
  newCode($pdf,490,80,'B00060006','SLOT AUDIT');
  newPage($pdf);
  newCode($pdf,50,80,'B00060007','SLOT AUDIT');
  newCode($pdf,490,80,'B00060008','SLOT AUDIT');
  
  
  
  $pdf->Output();
?>

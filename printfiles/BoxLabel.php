<?php

session_start();

if(!isset($_SESSION['rcw_userLoggedIn'])) {
    die('Unable to generate label.  Please log in to the system.');
}

include('../includes/settings.php');
include('../includes/g1functions.php');
include('../includes/connection.php');

write_log(__FILE__,__LINE__,"");

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Unable to generate label.  Please contact a system administrator.');
}

$boxid = $_GET['id'];

$sql = "SELECT count(*) as ROW_COUNT FROM boxes WHERE ID=$boxid";
$row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
$row_count = $row['ROW_COUNT'];

$sql = "SELECT * FROM boxes WHERE ID=$boxid";
$row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));

if($row_count > 1) {
    die('Unable to generate label.  Please contact an administrator.');
}
  
$barcode = $row['Barcode'];
$locationid = $row['LocationID'];
$sql = "SELECT Name FROM locations WHERE ID=$locationid";
$row = sqlsrv_fetch_array(sqlsrv_query($connection, $sql));
$origin = $row['Name'];
  
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
  $pdf->AddPage();
  
  // -------------------------------------------------- //
  //                      BOXES
  // -------------------------------------------------- //
  
  function newCode($pdf,$x,$y,$code,$origin,$records,$dates,$additional) {
    $data = Barcode::fpdf($pdf, '000000', $x+120, $y+90, 0, 'code128', array('code'=>$code), 2, 50);
    $pdf->SetXY($x,$y-30);
    $pdf->SetFont('Arial','B',20);
    $pdf->Cell(20,20,'x',1);
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
    $pdf->SetXY($x+280,$y+5);
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(200,10,'RECORD INVENTORY',0,0,'L');
    $pdf->SetFont('Arial','',9);
    $pdf->SetXY($x+280,$y+20);
    $pdf->Cell(200,10,$records[1],0,0,'L');
    $pdf->SetXY($x+280,$y+30);
    $pdf->Cell(200,10,$dates[1]);
    $pdf->SetXY($x+280,$y+45);
    $pdf->Cell(200,10,$records[2],0,0,'L');
    $pdf->SetXY($x+280,$y+55);
    $pdf->Cell(200,10,$dates[2]);
    $pdf->SetXY($x+280,$y+70);
    $pdf->Cell(200,10,$records[3],0,0,'L');
    $pdf->SetXY($x+280,$y+80);
    $pdf->Cell(200,10,$dates[3]);
    $pdf->SetXY($x+280,$y+95);
    $pdf->Cell(200,10,$records[4],0,0,'L');
    $pdf->SetXY($x+280,$y+105);
    $pdf->Cell(200,10,$dates[4]);
    $pdf->SetXY($x+280,$y+120);
    $pdf->Cell(200,10,$records[5],0,0,'L');
    $pdf->SetXY($x+280,$y+130);
    $pdf->Cell(200,10,$dates[5]);
    $pdf->SetXY($x+280,$y+145);
    $pdf->Cell(200,10,$records[6],0,0,'L');
    $pdf->SetXY($x+280,$y+155);
    $pdf->Cell(200,10,$dates[6]);
    $pdf->SetXY($x+280,$y+170);
    $pdf->Cell(200,10,$records[7],0,0,'L');
    $pdf->SetXY($x+280,$y+180);
    $pdf->Cell(200,10,$dates[7]);
    $pdf->SetXY($x+280,$y+195);
    $pdf->Cell(200,10,$records[8],0,0,'L');
    $pdf->SetXY($x+280,$y+205);
    $pdf->Cell(200,10,$dates[8]);
    $pdf->SetXY($x+280,$y+220);
    $pdf->Cell(200,10,$records[9],0,0,'L');
    $pdf->SetXY($x+280,$y+230);
    $pdf->Cell(200,10,$dates[9]);
    $pdf->SetXY($x+280,$y+245);
    $pdf->Cell(200,10,$additional);
  }
  
  function newPage($pdf) {
    $pdf->AddPage();
  }
  
  for($i=1; $i<=9; $i++) {
    $records[$i] = '';
    $dates[$i] = '';
  }
  $additional = '';
  
$sql = "SELECT ID, Description FROM recordtypes";
$result = sqlsrv_query($connection, $sql);
while($row = sqlsrv_fetch_array($result)) {
    $recordname[$row['ID']] = $row['Description'];
}
  
$sql = "SELECT ID, Code FROM properties";
$result = sqlsrv_query($connection, $sql);
while($row = sqlsrv_fetch_array($result)) {
    $propcode[$row['ID']] = $row['Code'];
}
  
$recordcount = 0;
$sql = "SELECT * FROM records WHERE BoxID=$boxid AND Active=1";
$result = sqlsrv_query($connection, $sql);
while($row = sqlsrv_fetch_array($result)) {
    $recordcount++;
    $records[$recordcount] = $propcode[$row['PropertyID']] . ' - ' . $recordname[$row['RecordTypeID']];
    $dates[$recordcount] = date('n/j/Y',$row['StartDate']) . ' - ' . date('n/j/Y',$row['EndDate']);
}
  
if($recordcount > 9) {
    $additional = '(Additional records are contained in this box)';
}
  
  newCode($pdf,50,80,$barcode,$origin,$records,$dates,$additional);
  newCode($pdf,50,500,$barcode,$origin,$records,$dates,$additional);
  
  $pdf->Output();
?>

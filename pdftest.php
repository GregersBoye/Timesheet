<?php
include("fpdf.php"); //Inkluder pdf værktøjet
$dage = array(1,0,0,0,9,8,0,6,0,4,5,4,0,5,0,0,6,0,0,4,9,0,3,0,0,4,7,4,4,0,0,0);
$navn = $_REQUEST["TEKST"];
$pdf = new FPDF('p', 'mm', 'A4'); //Konstruktør, opret et nyt PDF dokument
$pdf->AddPage(); //Tilføj en side til dokumentet
$pdf->SetFont("Arial", "", 8); //Bestem fonten: Arial, fed (bold) og en størrelse på 18

$pdf->SetFillColor(10,10,180);
$pdf->Cell(50,7,"Den udfyldte timeseddel skal",1,'C',2,1);
$pdf->Ln();
$pdf->Cell(50,7,"indsendes til ansættelsesstedet",1,'C',2,1);
$pdf->Ln();
$pdf->Cell(50,7,"(ikke til lønkontoret)",1,'C',2,1);
$pdf->Ln();


$pdf->SetFont("Arial", "B", 18); //Bestem fonten: Arial, fed (bold) og en størrelse på 18
$pdf->Cell(180,10,"Studentermedhjælp", 0,1, 'C');

$pdf->Ln();
$mdr = array("0", "januar","februar","marts","april","maj","juni","juli","august","oktober","november","december");

$pdf->SetFont("Arial", "B", 12); //Bestem fonten: Arial, fed (bold) og en størrelse på 18
$md = date("n",time());
$pdf->Cell(90,6,"Timeopgørelse for måneden $mdr[$md]",0);
$pdf->Cell(70,6,date("Y",time()));
$pdf->Ln();
$pdf->SetFillColor(10,10,180);

$pdf->SetFont("Arial", "", 8); //Bestem fonten: Arial, fed (bold) og en størrelse på 18
$pdf->Cell(90,6,"Fulde Navn:",1,'L',1,1);
$pdf->Cell(50,6,"CPR.-nummer:",1,'L',1,1);
$pdf->Cell(40,6,"L.-Nr.",1,'L',1,1);
$pdf->Ln();
$pdf->Cell(90,6,"Gregers Boye-Jacobsen",1);
$pdf->Cell(50,6,"12345678-9099",1);
$pdf->Cell(40,6," ",1);
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont("Arial", "B", 8); //Bestem fonten: Arial, fed (bold) og en størrelse på 18
$pdf->Cell(15,6,"Dato",1);
$pdf->Cell(29,6,"Tidsrum",1);
$pdf->Cell(15,6,"Timer",1);
$pdf->Cell(30,6,"Arbejdets Art",1);

$pdf->Cell(2,6," ",1);

$pdf->Cell(15,6,"Dato",1);
$pdf->Cell(29,6,"Tidsrum",1);
$pdf->Cell(15,6,"Timer",1);
$pdf->Cell(30,6,"Arbejdets Art",1);
$pdf->Ln();
for ($i=1;$i<=15;$i++){

if ($dage[$i]!=0){
	$hours =$dage[$i];
	$kind ="Foref. kontorarb.";
}else
{
	$hours ="";
	$kind="";
}

$pdf->Cell(15,6,$i.".",1);
$pdf->Cell(29,6," ",1);
$pdf->Cell(15,6,$hours,1);
$pdf->Cell(30,6,"$kind",1);

$pdf->Cell(2,6," ",1);
$j=$i+16;

if ($dage[$j]!=0){
	$hours =$dage[$j];
	$kind ="Foref. kontorarb.";
}else
{
	$hours ="";
	$kind="";
}

$pdf->Cell(15,6,$j.".",1, "C");
$pdf->Cell(29,6," ",1);
$pdf->Cell(15,6,"$hours",1);
$pdf->Cell(30,6,"$kind",1);
$pdf->Ln();



}
if ($dage[16]!=0){
	$hours =$dage[16];
	$kind ="Foref. kontorarb.";
}else
{
	$hours ="";
	$kind="";
}
$pdf->Cell(15,6,"16.",1);
$pdf->Cell(29,6," ",1);
$pdf->Cell(15,6,"$hours",1);
$pdf->Cell(30,6,"$kind",1);

$pdf->Cell(2,6," ",1);
$pdf->Cell(44,6,"Antal timer ialt",1,'C',1,1);
$pdf->Cell(15,6," ",1);


$pdf->Ln();





$pdf->Output(); //Generer pdf dokumentet
?>
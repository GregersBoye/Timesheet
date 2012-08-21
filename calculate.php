<?php

include("fpdf.php"); //Inkluder pdf værktøjet

if (isset($_REQUEST["aar"])) {$aar_test = $_REQUEST["aar"];}
if (isset($_REQUEST["maaned"])) $maaned_test = $_REQUEST["maaned"];
$maaned_kort = $maaned_test;
if ($maaned_test<10){$maaned_test = "0".$maaned_test;}
$dato_test = "$aar_test$maaned_test";
//Vi henter stien til filen fra google og sætter en variabel.
$path = $_REQUEST["path"];
$navn = $_REQUEST["namos"];


$contents = "";


// Jeg åbner filen og lægger den over i en variabel 
$file = fopen ("$path", "r");

	//Jeg sikrer mig lige at den overhohedet findes.
if (!$file) {
    echo "<p>Unable to open remote file.\n";
    exit;
}

	//Vi flytter filen over.
while (!feof ($file)) {
    $line = fgets ($file);
		$contents .= $line;
}

fclose($file); // og lukker efter os.


	// skal bruge længden af variablen og sætter nogen variable
$file_length = strlen($contents);
$start_work = array();
$end_work = array();

$start_unix = array();
$end_unix = array();
$ignore_list = array();

$start_stamp = 0;
$end_stamp = 0;

	// Skal vide hvor mange arbejdsdage det drejer sig om.
str_replace("DTSTART:", "DTSTART:", $contents, $counter);



	//Her henter jeg selve start- og sluttidspunktet over i to arrays, indtil videre holder jeg dem i google-time
for ($i=1; $i<=$counter; $i++){
	
	$start_stamp = strpos($contents, "DTSTART:", $start_stamp);
	$end_stamp = strpos($contents, "DTEND:", $end_stamp);
  $test_start = $start_stamp+8;
  $test_slut = $start_stamp+14;
  $maaned_aar = substr($contents,$test_start,6);
  
  $j =	$start_stamp+8;
  $k =	$end_stamp+6;
  
		$start_work[] = substr($contents, $j, 16);
		$end_work[] =   substr($contents, $k, 16);
		$time = end($start_work);
		
		$dag = substr($time, 6,2);
		$mdr = substr($time, 4,2);
		$aar = substr($time, 0,4);
		
		$tim =substr($time, 9,2)+1;
		$minut =substr($time,11,2);
		$start_unix[] = mktime($tim, $minut, 00, $mdr, $dag, $aar);

	
		$time = end($end_work);
		$dag = substr($time, 6,2);
		$mdr = substr($time, 4,2);
		$aar = substr($time, 0,4);
		$tim =substr($time, 9,2)+1;
		$minut =substr($time,11,2);
	
		$end_unix[] = mktime($tim, $minut, 00, $mdr, $dag, $aar);

	

		
		$ignore_list[$i] = ($maaned_aar!=$dato_test) ? "0" : "1";
		
		$start_stamp = $start_stamp+1;
		$end_stamp = $end_stamp+1;
		$total = 0;
	
}
$test = $ignore_list[2];
	$dage = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
	$meet = array("","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");
	$home = array("","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");
for ($i=0; $i<$counter; $i++){
	if($ignore_list[$i]=="1")
	{
		$j= date("d/m 'y", $end_unix[$i])." ";
		$tid = round(($end_unix[$i]-$start_unix[$i])/3600,2);
		$dato = date("j",$end_unix[$i]);
		$meet[$dato] = date("H:i",$start_unix[$i]);
		$home[$dato] = date("H:i",$end_unix[$i]);
		$dage[$dato] = $tid;
		$total += $tid;
	}


}



$pdf = new FPDF('p', 'mm', 'A4'); //Konstruktør, opret et nyt PDF dokument
$pdf->AddPage(); //Tilføj en side til dokumentet

$pdf->Image('aaulogo.jpg',10,8,33);
$pdf->SetFont("Arial", "", 8); //Bestem fonten: Arial, fed (bold) og en størrelse på 18
$pdf->Cell(50,5," ",0);

$pdf->Ln();
$pdf->SetFillColor(197,221,246);
$pdf->Cell(50,4,"$test",'LTR','c',2,1);
//$pdf->Cell(50,4,"$testDen udfyldte timeseddel skal",'LTR','c',2,1);
$pdf->Ln();
$pdf->Cell(50,4,"indsendes til ansættelsesstedet",'LR','c',2,1);
$pdf->Ln();
$pdf->Cell(50,4,"(ikke til lønkontoret)",'LBR','c',2,1);
$pdf->Ln();


$pdf->SetFont("Arial", "B", 18); //Bestem fonten: Arial, fed (bold) og en størrelse på 18
$pdf->Cell(180,10,"Studentermedhjælp", 0,1, 'C');

$pdf->Ln();
$mdr = array("0", "januar","februar","marts","april","maj","juni","juli","august","september", "oktober","november","december");

$pdf->SetFont("Arial", "B", 12); //Bestem fonten: Arial, fed (bold) og en størrelse på 18
$md = date("n",time());
$pdf->Cell(90,6,"Timeopgørelse for måneden $mdr[$maaned_kort]",0);
$pdf->Cell(70,6,$aar_test);
$pdf->Ln();
$pdf->SetFillColor(197,221,246);

$pdf->SetFont("Arial", "", 8); //Bestem fonten: Arial, fed (bold) og en størrelse på 18
$pdf->Cell(90,6,"Fulde Navn:",1,'L',1,1);
$pdf->Cell(50,6,"CPR.-nummer:",1,'L',1,1);
$pdf->Cell(40,6,"L.-Nr.",1,'L',1,1);
$pdf->Ln();
$pdf->Cell(90,6,"Gregers Boye-Jacobsen",1);
$pdf->Cell(50,6,"110383-1111",1);
$pdf->Cell(40,6," ",1);

$pdf->Ln();
$pdf->Ln();

$pdf->SetFont("Arial", "B", 8); //Bestem fonten: Arial, fed (bold) og en størrelse på 18
$pdf->Cell(15,3,"Dato",'TLR');
$pdf->Cell(29,3,"Tidsrum",'TLR');
$pdf->Cell(15,3,"Antal",'TLR');
$pdf->Cell(30,3,"Arbejdets Art",'TLR');

$pdf->Cell(2,3," ",'TLR');

$pdf->Cell(15,3,"Dato",'TLR');
$pdf->Cell(29,3,"Tidsrum",'TLR');
$pdf->Cell(15,3,"Antal",'TLR');
$pdf->Cell(30,3,"Arbejdets Art",'TLR');
$pdf->Ln();

$pdf->SetFont("Arial", "B", 8); //Bestem fonten: Arial, fed (bold) og en størrelse på 18
$pdf->Cell(15,3," ",'RLB');
$pdf->Cell(29,3," ",'RLB');
$pdf->Cell(15,3,"timer",'RLB');
$pdf->Cell(30,3," ",'RLB');

$pdf->Cell(2,3," ",'RLB');

$pdf->Cell(15,3," ",'RLB');
$pdf->Cell(29,3," ",'RLB');
$pdf->Cell(15,3,"Timer",'RLB');
$pdf->Cell(30,3," ",'RLB');
$pdf->Ln();




for ($i=1;$i<=15;$i++){

if ($dage[$i]!=0){
	$hourses = $meet[$i]."-".$home[$i];
	$hours =$dage[$i];
	$kind ="Foref. kontorarb.";
}else
{
	
	$hourses ="";
	$hours ="";
	$kind="";
}

$pdf->Cell(15,6,$i.".",1);
$pdf->Cell(29,6,"$hourses",1);
$pdf->Cell(15,6,$hours,1);
$pdf->Cell(30,6,"$kind",1);

$pdf->Cell(2,6," ",1);
$j=$i+16;

if ($dage[$j]!=0){
	$hourses = $meet[$j]."-".$home[$j];
	$hours =$dage[$j];
	$kind ="Foref. kontorarb.";
}else
{
	$hourses ="";
	$hours ="";
	$kind="";
}

$pdf->Cell(15,6,$j.".",1, "C");
$pdf->Cell(29,6,"$hourses",1);
$pdf->Cell(15,6,"$hours",1);
$pdf->Cell(30,6,"$kind",1);
$pdf->Ln();



}
if ($dage[16]!=0){
	$hourses = $meet[16]."-".$home[16];
	$hours =$dage[16];
	$kind ="Foref. kontorarb.";
}else
{
	$hourses="";
	$hours ="";
	$kind="";
}
$pdf->Cell(15,6,"16.",1);
$pdf->Cell(29,6,$meet[16],1);
$pdf->Cell(15,6,"$hours",1);
$pdf->Cell(30,6,"$kind",1);

$pdf->Cell(2,6," ",1);
$pdf->Cell(44,6,"Antal timer ialt",1,'C',1,1);
$pdf->Cell(15,6,$total,1);


$pdf->Ln();





$pdf->Output(); //Generer pdf dokumentet



?>
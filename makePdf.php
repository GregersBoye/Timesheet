<?php 
	session_start();
	require_once("Event.php");
	require_once 'Zend/Loader.php';
	require_once("fpdf.php"); //Inkluder pdf værktøjet

Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');

$calFeed ="";
$client = "";
if (isset($_SESSION['sessionToken'])){
	$client = Zend_Gdata_AuthSub::getHttpClient($_SESSION['sessionToken']);
	$gdataCal = new Zend_Gdata_Calendar($client);
 	$calFeed = $gdataCal->getCalendarListFeed();
}

function countDecimals($hayStack){
	$decimals = strrchr($hayStack, ".");
	
	$return = "";
	switch(strlen($decimals)){
		case 0:
			$return = ".00";
			break;
		case 1:
			$return = "00";
			break;
		case 2: 
			$return = "0";
			break;
		default:
			$return = "";
			break;
			
	}
	return $hayStack.$return;
	
}


function getAuthSubUrl() 
{
  $next = "http://klasseliste.dk/gcal/test.php";
  $scope = 'https://www.google.com/calendar/feeds/';
  $secure = false;
  $session = true;
  return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure, 
      $session);
}

if (isset($_POST["chosenCalendar"])){
	$myCalendar = $calFeed[$_POST["chosenCalendar"]];
	$calId=  end(explode("/", $myCalendar->id));
	
	
	$startDato = $_POST["first-date"];
	$slutDato = $_POST["second-date"];

	
	$startDate  = substr($startDato,6,4)."-".substr($startDato ,3,2)."-".substr($startDato,0,2);
	$endDate = substr($slutDato,6,4)."-".substr($slutDato ,3,2)."-".substr($slutDato,0,2);
	$startDate=$startDate; 
	$endDate=$endDate;
	$gdataCal = new Zend_Gdata_Calendar($client);
 	$query = $gdataCal->newEventQuery();
 	$query->setUser($calId);
 	$query->setVisibility('private');
  	$query->setProjection('full');
	$query->setOrderby('starttime');
 	$query->setStartMin($startDate);
 	$query->setStartMax($endDate);
	$query->sortorder = 'ascend';
 	$eventFeed = $gdataCal->getCalendarEventFeed($query);
 	$i= 0;
	$total = 0;
	$eventList = Array();
 	foreach ($eventFeed as $event) {
   	foreach ($event->when as $when) {
			$eventList[]= new Event($event->title, $when->startTime, $when->endTime); //laver lang liste med event
		}
 	}
	usort($eventList, Array("Event", "sortElementsByTime")); 
	$sortedList = Array();
		
		//Laver array med et array for hvert event-titel
	foreach($eventList as $thisEvent){
		$name = $thisEvent->name."";
		$sortedList[$name][] = $thisEvent;
	}
	$pdf = new FPDF('p', 'mm', 'A4'); //Konstruktør, opret et nyt PDF dokument
$pdf->AddPage(); //Tilføj en side til dokumentet


$pdf->SetFont("Arial", "U", 20); //Bestem fonten: Arial, fed (bold) og en størrelse på 18
$pdf->Ln(10);
$pdf->Cell(150, 23,"Timeseddel: ". $_POST["navn"]);
$pdf->Ln(6);
$pdf->SetFont("Arial", "", 10);
$pdf->Cell(150, 23, "Perioden ".substr($startDato,0,5)." - ".substr($slutDato,0,5));
$pdf->SetFont("Arial", "", 11); //Bestem fonten: Arial, fed (bold) og en størrelse på 18
$pdf->Ln(20);
$workTotal =0;
$totalList = Array();
	foreach($sortedList as $key=>$eventList) // for all eventtyper
	{
		$total =0;
		$cw = 35;
		$ch = 5;
		$pdf->SetFillColor(150,150,150);
		$pdf->Cell($cw-5, $ch, "Opgave:", 'LTR', 0 , 'L', true);
		$pdf->Cell(5,$ch,"","T");
		$pdf->Cell(80, $ch, utf8_decode($key), 'TR');
		$pdf->Ln($ch);

			$pdf->Cell($cw,$ch, "Dato", 'LTB',  0 , 'L',true);
			$pdf->Cell($cw,$ch, "Fra", 'BT', 0 , 'L', true);
			$pdf->Cell($cw-10,$ch, "Til", 'BT', 0 , 'L', true);
	
			$pdf->Cell(20,$ch, "Timer", 'BRT', 0 , 'L', true);

		$pdf->Ln($ch);
		$color = false;
		$pdf->SetFillColor(220,220,220);
		foreach ($eventList as $thisEvent)	 // for hver event i en eventtype
		{
			$workingTime = countDecimals($thisEvent->getWorkTime(true));
			$pdf->Cell($cw,$ch, $thisEvent->getWorkDate(),'L',0,'L', $color);
			$pdf->Cell($cw,$ch, $thisEvent->getStartTime(),0,0,'L', $color);
			$pdf->Cell($cw,$ch, $thisEvent->getEndTime(),0,0,'L', $color);
			$pdf->Cell($cw-25,$ch, $workingTime,'R',0,'R', $color);
			$color = !$color;

			$startTime = $thisEvent->start;
			$endTime = $thisEvent->ending;
			$total += $thisEvent->getWorktime(false);
			$pdf->Ln($ch);
		}
			$pdf->SetFillColor(150,150,150);
			
			$pdf->Cell($cw-5, $ch, "Total: ",'TLB',0,'L',true);
			$pdf->Cell(5,$ch,"","LBT");
			$pdf->Cell(80, $ch, countDecimals(round($total,2)), 'TBR', 0, 'R');
			$workTotal += $total;
			$totalList[$key] = $total;
			$pdf->Ln(15);
	}
	$pdf->SetFillColor(150,150,150);
	$pdf->Cell($cw-5, $ch, "Timer totalt:", 'LBRT', 0,'L', true);
	$pdf->Cell(85, $ch, countDecimals(round($workTotal,2)), 'LBRT', 0, 'R'); 
	$pdf->Ln(10);
	$pdf->Cell($cw, $ch, "Udskrevet den ".date("j/n Y", time()));
$pdf->Output("timeseddel.pdf", "I"); //Generer pdf dokumentet
 ?>
 
 
 
 
 <?php
}
	?>
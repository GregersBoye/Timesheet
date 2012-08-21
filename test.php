<?php 
	session_start();
	require_once("Event.php");
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
table{ border-spacing:0px 5px;} 
.timeTable { font-family:Arial, Helvetica, sans-serif; width:300px;}
.timeTable tr:nth-child(even){background: #CCC;}
.timeTable tr:nth-child(odd) {background: #AAA;}
.timeTable tr:first-child{background:#888;
padding: 3px;}

.timeTable tr:first-child > td {color: #FFF; padding:3px; font-weight:bold;}

.timeTable tr:last-child{background-color:#eee;}
//#timeTable tr:last-child {background:#fff;}
.timeTable td{ vertical-align:bottom;
		padding:3px 5px 0px 3px;}
.timeLine td:last-child{text-align: right;padding-right:5px;}
.timeTable tr:last-child td{border-top: 1px solid #000; border-bottom: 3px double #000; padding-top:3px;}
</style>
<!-- jQuery -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="datePicker.css" />
<!-- required plugins -->
<script type="text/javascript" src="scripts/date.js"></script>
<!--[if IE]><script type="text/javascript" src="scripts/jquery.bgiframe.js"></script><![endif]-->
<script type="text/javascript">



$(function()
{
	$('.date-pick').datePicker({startDate:'01/01/1996'})
	$('#start-date').bind(
		'dpClosed',
		function(e, selectedDates)
		{
			var d = selectedDates[0];
			if (d) {
				d = new Date(d);
				$('#end-date').dpSetStartDate(d.addDays(1).asString());
			}
		}
	);
	$('#end-date').bind(
		'dpClosed',
		function(e, selectedDates)
		{
			var d = selectedDates[0];
			if (d) {
				d = new Date(d);
				$('#start-date').dpSetEndDate(d.addDays(-1).asString());
			}
		}
	);
});


</script>
<!-- jquery.datePicker.js -->
<script type="text/javascript" src="scripts/jquery.datePicker.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');


function outputCalendarList($client) 
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  $calFeed = $gdataCal->getCalendarListFeed();

  echo "<select name=\"chosenCalendar\">\n";
  foreach ($calFeed as $key => $calendar ) {
    echo "<option value=\"" .$key."\">". $calendar->title->text . "</option>\n";
	

  }
  echo '</select>';
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



function retrieveCalendarId($client, $calToFind){
	
 $gdataCal = new Zend_Gdata_Calendar($client);
 $calFeed = $gdataCal->getCalendarListFeed();
 $found = false;
 foreach ($calFeed as $calendar ) {
	if($calendar->title->text == $calToFind){
		$found = true;	
		$returnVal = explode("/", $calendar->id);
		return end($returnVal);
	}
  }
  if (!$found){
	  return null;
  }
	
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

if(!isset($_SESSION['sessionToken']) && !isset($_GET['token'])) {
	
	$authSubUrl = getAuthSubUrl();
	echo "<a href=\"$authSubUrl\">login to your Google account</a>"; 
}
if(!isset($_SESSION['sessionToken']) && isset($_GET['token'])) {
  $_SESSION['sessionToken'] = Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
	  echo "token er sat";
}
$calFeed ="";
$client = "";
if (isset($_SESSION['sessionToken'])){
	$client = Zend_Gdata_AuthSub::getHttpClient($_SESSION['sessionToken']);
	$gdataCal = new Zend_Gdata_Calendar($client);
 	$calFeed = $gdataCal->getCalendarListFeed();
	echo "<form action=\"test.php\" method=\"post\">\n";
	echo "<table>\n";
	echo "<tr><td colspan=\"2\"> Vælg kalender og interval</td></tr>";
	echo "<tr><td>Kalender: </td><td>";
	outputCalendarList($client);
	echo"</td></tr>\n";
	echo "<tr><td>Start: </td><td><input id=\"start-date\" name=\"first-date\" class=\"date-pick\" type=\"text\" /></td></tr>\n";
	echo "<tr><td>Slut: </td><td><input id=\"end-date\" name=\"second-date\" class=\"date-pick\" type=\"text\" /></td></tr>\n";
	echo "<tr><td>Navn: </td><td><input name=\"navn\"></td> </tr>";
	echo "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Hent kalender\" /></td></tr>\n";
	echo "</table>\n";
	echo "</form>";
	  
}

function makeDate($givenDate){
	
	$dateArray = explode("-",$givenDate);
	$dato = $dateArray[2]."-".$dateArray[1]."-".$dateArray[0];
	
	return $dato;
	
}

if (isset($_POST["chosenCalendar"])){
	$myCalendar = $calFeed[$_POST["chosenCalendar"]];
	$calId=  end(explode("/", $myCalendar->id));
	
	$startDato = makeDate($_POST["first-date"]);
	$slutDato = makeDate($_POST["second-date"]);


	$startDate=$startDato; 
	$endDate=$slutDato;
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
	
			$eventList[]= new Event($event->title, $when->startTime, $when->endTime);
			
		}
 	}
	usort($eventList, Array("Event", "sortElementsByTime"));
	$sortedList = Array();
		
	foreach($eventList as $thisEvent){
		$name = $thisEvent->name."";
		$sortedList[$name][] = $thisEvent;
	}


	foreach($sortedList as $key=>$eventList)
	{
		$total =0;
			echo "<table class=\"timeTable\">";
	echo "<tr><td>Opgave: </td><td colspan=\"3\">".$key."</td></tr>";
	echo "<tr><td>Dato:</td><td>Start</td><td>Slut: </td><td>Ialt</td></tr>\n";

		foreach ($eventList as $thisEvent)
		{
	
			$startTime = $thisEvent->start;
			$endTime = $thisEvent->ending;
			
			$thisEvent->printEvent();
			
			$total += $thisEvent->getWorktime(false);
		}
			echo "<tr ><td colspan=\"3\">Total arbejdstid: </td><td  align=\"right\" >".countDecimals(round($total, 2))."</td></tr>";
	echo "</table>";
	}

 ?>
 
 
<form action="makePdf.php" method="post">
	<input name="chosenCalendar" type="hidden" value="<?php echo $_POST["chosenCalendar"]?>" />
	<input name="first-date" type="hidden"  value="<?php echo $_POST["first-date"]?>"/>
	<input name="second-date" type="hidden" value="<?php echo $_POST["second-date"]?>"/>
	<input name="navn" type="hidden" value="<?php echo $_POST["navn"] ?> " />
	<input name="" type="submit" value="Lav pdf" />
</form>
 
 
 
 
 
 <?php
}


function showArray($myArray, $indent = ""){
	
	if(is_array($myArray)){
			$indent .= "&nbsp;&nbsp;&nbsp;&nbsp;";
			foreach($myArray as $key =>$val){
				echo "<b>$indent&lt;".$key."&gt;</b> <br />";
				showArray($val, $indent);	
				echo "<b>$indent&lt;/".$key."&gt;</b> <br />";
			}
	}
	else{
		echo "$indent&nbsp;&nbsp;&nbsp;&nbsp;".$myArray->name."<br />";
	}
}

?>




</body>
</html>

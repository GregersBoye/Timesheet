<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
.error{
	background:#F63; }

</style>
	<!-- jQuery -->
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<script src="scripts/development-bundle/ui/jquery.ui.core.js"></script>
	<script src="scripts/development-bundle/ui/jquery.ui.widget.js"></script>
	<script src="scripts/jquery.timePicker.js"></script>
	<script src="scripts/development-bundle/ui/jquery.ui.position.js"></script>
	<script src="scripts/development-bundle/ui/jquery.ui.autocomplete.js"></script>
	<script type="text/javascript" src="scripts/date.js"></script>
	<script type="text/javascript" src="scripts/jquery.datePicker.js"></script><!--[if IE]><script type="text/javascript" src="scripts/jquery.bgiframe.js"></script><![endif]-->
	
	<link rel="stylesheet" href="scripts/development-bundle/themes/base/jquery.ui.all.css">
	<link rel="stylesheet" href="demos.css" />
	<link rel="stylesheet" href="timePicker.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="datePicker.css" />
	
	<style>
	.ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
	</style>
	<!-- required plugins -->
	<script type="text/javascript">
	var numberOfActivities = 0;
	var projects = new Array();
 	
	$(document).ready(function(){
		$.ajax({
      	type: "GET",
			url: "projects.xml",
			dataType: "xml",
			success: function(xml) {
				$(xml).find('project').each(function(){
					projects[numberOfActivities] =	 $(this).text();
					numberOfActivities++;
				});
			}
		});
	});
$(function()
{
	$('.date-pick').datePicker({startDate:'01/01/1996'});
});




$(function()
{
	// Use default settings
$("#time_end, #time_start").timePicker({
  startTime: "9:00", // Using string. Can take string or Date object.
  endTime: new Date(0, 0, 0, 21, 00, 0), // Using Date object here.
  show24Hours: true,
  separator: ':',
  step: 15});
// Store time used by duration.
var oldTime = $.timePicker("#time_start").getTime();

// Keep the duration between the two inputs.
/*$("#time_end").change(function() {
  if ($("#time_end").val()) { // Only update when second input has a value.
    // Calculate duration.
    var duration = ($.timePicker("#time_end").getTime() - oldTime);
    var time = $.timePicker("#time_start").getTime();
    // Calculate and update the time in the second input.
    $.timePicker("#time_end").setTime(new Date(new Date(time.getTime() + duration)));
    oldTime = time;
  }
});*/
// Validate.
$("#time_end").change(function() {
  if($.timePicker("#time_start").getTime() >= $.timePicker(this).getTime()) {
    $(this).addClass("error");
  }
  else {
    $(this).removeClass("error");
  }
}); 

$('#opretForm').submit(function() {
	


	activity = $('#activity').val();
	best = -1;
	word = "";
	for(s=0; s<numberOfActivities; s++){
		
		actArray = activity.split(" ");
		actProjects = projects[s].split(" ");
		lev = findDist(projects[s],activity);
		alert(project[s]+": "+lev);
			if(lev==0){
			word = activity;
			best = 0;
		}
		if(lev<=best || best <0){
			best = lev;
			word = projects[s];	
			
			
		}
	}
	alert(word+": "+best);
	
  return false;
});

$("#time_start").change(function() {
	
  if($("#time_end").val() && $.timePicker("#time_start").getTime() >= $.timePicker("#time_end").getTime()) {
    $(this).addClass("error");
  }
  else {
    $(this).removeClass("error");
  }
});

	
	function log( message ) {
		$("input#activity").val( this.value);
	}
$.ajax({
		url: "projects.xml",
		dataType: "xml",
		success: function( xmlResponse ) {
			var data = $( "project"	, xmlResponse ).map(function() {
				return {
					value: $(this).text(), id: $(this).text()};
			}).get();
			$( "#activity" ).autocomplete({
				source: data,
				minLength: 0,
				select: function( event, ui ) {
					log( ui.item ?	ui.item.value : this.value );
				}
			});
		}
	});
});

function FindMin(a, b, c)
{
	minimum = a;
	if (b < minimum)
	{
		minimum = b;
	}
	if (c < minimum)
	{
		minimum = c;
	}
	return minimum;
}

function findDist(first, second){
            rowSize = first.length;
            colSize = second.length;
           	matrice = new Array();
				for(i=0; i<= rowSize; i++){
					matrice[i] = new Array();
				}
					
					
            for (i = 0; i <= rowSize; i++){
                matrice[i][0] = i;
            }

            for (i = 0; i <= colSize; i++)
            {
                matrice[0][i] = i;
            }

            for (iCol = 1; iCol < colSize; iCol++)
            {
                for (iRow = 1; iRow < rowSize; iRow++)
                {
                    firstTestString = first.substring(iRow - 1, 1);
                    secondTestString = second.substring(iCol - 1, 1);
                    if (firstTestString.toLowerCase(), secondTestString.toLowerCase())
                    {
                        matrice[iRow][ iCol] = matrice[iRow - 1][iCol - 1];
                    }else
                    {
                        a = matrice[iRow-1][ iCol]+1;
                        b = matrice[iRow ][ iCol-1]+1;
                        c = matrice[iRow-1][iCol-1]+1;
                        matrice[iRow][iCol] = FindMin(a, b, c);
                    }
                }
            }

            return matrice[rowSize-1][ colSize-1];
        }


	</script>
<?php
require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');


function getAuthSubUrl() 
{
  $next = "http://klasseliste.dk/gcal/opret.php";
  $scope = 'https://www.google.com/calendar/feeds/';
  $secure = false;
  $session = true;
  return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure, 
      $session);
}

if(!isset($_SESSION['sessionToken']) && !isset($_GET['token'])) {
	
	$authSubUrl = getAuthSubUrl();
	echo "<a href=\"$authSubUrl\">login to your Google account</a>"; 
}
if(!isset($_SESSION['sessionToken']) && isset($_GET['token'])) {
  $_SESSION['sessionToken'] = Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
	  echo "token er sat";
}

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
	$client = "";
	$calFeed ="";
if (isset($_SESSION['sessionToken'])){
	$client = Zend_Gdata_AuthSub::getHttpClient($_SESSION['sessionToken']);
	$gdataCal = new Zend_Gdata_Calendar($client);
 	$calFeed = $gdataCal->getCalendarListFeed();
	
	//createEvent($client, 'testet','','','2011-05-21', '11:00', '2011-05-21', '13:00');
	
}


function retrieveCalendarUri($client, $calToFind){
	
 $gdataCal = new Zend_Gdata_Calendar($client);
 $calFeed = $gdataCal->getCalendarListFeed();
 $found = false;
 foreach ($calFeed as $calendar ) {
	if($calendar->title->text == $calToFind){
		$found = true;	
		$returnVal = $calendar->id;
		return end($returnVal);
	}
  }
  if (!$found){
	  return null;
  }
	
}

function createEvent ($client, $title = 'Tennis with Beth',
    $desc=' ', $where = ' ',
    $startDate = '2008-01-20', $startTime = '10:00',
    $endDate = '2008-01-20', $endTime = '11:00', $tzOffset = '+02', $uri = 'default')
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  $newEvent = $gdataCal->newEventEntry();
  
  $newEvent->title = $gdataCal->newTitle($title);
  $newEvent->where = array($gdataCal->newWhere($where));
  $newEvent->content = $gdataCal->newContent("$desc");
  
  $when = $gdataCal->newWhen();
  $when->startTime = "{$startDate}T{$startTime}:00.000{$tzOffset}:00";
  $when->endTime = "{$endDate}T{$endTime}:00.000{$tzOffset}:00";
  $newEvent->when = array($when);



  // Upload the event to the calendar server
  // A copy of the event as it is recorded on the server is returned
  $createdEvent = $gdataCal->insertEvent($newEvent, $uri);
  return $createdEvent->id->text;
}


if (isset($_POST["saved"])){
	
	$activity = $_POST["aktivitet"];
	$start_time = $_POST["startTime"];
	$end_time = $_POST["endTime"];
	$dateArray = explode("-", $_POST["dato"]);
	


	$dato = $dateArray[2]."-".$dateArray[1]."-".$dateArray[0];
	$calendar = $_POST["chosenCalendar"];
	$myCalendar = $calFeed[$_POST["chosenCalendar"]];
	$uri =  $myCalendar->id."";
	$uri = str_replace("/default", "", $uri)."/private/full";
	$uri = str_replace("http", "https", $uri);
	echo $uri;
	createEvent($client, $activity, '', '', $dato, $start_time, $dato, $end_time, '+02', $uri);
	
}


?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
	<style type="text/css">
	body,td,th {
	font-family: "Trebuchet MS", Helvetica, Arial, Verdana, sans-serif;
}
	</style>
</head>
	<body>
<?php 
if (isset($_SESSION['sessionToken'])){
	?>
<form action="opret.php" method="post" id="opretForm">
		<table>
		<tr>
			<td colspan="2"> Vælg kalender og tidspunkt</td>
		</tr>
		<tr>
			<td>Kalender: </td>
			<td><?php outPutCalendarList($client) ?></td>
		</tr>
		<tr>
			<td>Dag: </td>
			<td><input class="date-pick" name="dato" type="text" /></td>
		</tr>
		<tr>
			<td>Start: </td>
			<td><input name="startTime" id="time_start" type="text"  /></td>
		</tr>
		<tr>
			<td>Slut: </td>
			<td><input name="endTime" id="time_end" type="text" /></td>
		</tr>
		<tr>
				<td>Aktivitet: </td>
				<td><input name="aktivitet" id="activity" type="text" /></td>
			</tr>
				<tr>
				<td colspan="2"><input name="saved" type="submit" value="Gem" /></td>
			</tr>
	</table>
	</form>
<?php } ?>
</body>
</html>
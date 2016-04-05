<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>




<title>Reservation Listing</title>
<meta http-equiv="refresh" content="300">




<link href="../css/reslist.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Stoke' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Hammersmith+One' rel='stylesheet' type='text/css'>

</head>



<body>
<div id="container">


<?php

echo "";
echo "<div id='header'><div id='headertext'> Today's Reservation Schedule</div></div>";

//Include the parser
require '../class.icsparser.php';

//Use the Parser to put each event into the array Events.
$ical = new ical('http://25livepub.collegenet.com/calendars/etu-room-reservations.ics');
$events = ($ical->events());



//This loop filters out all of the non matching classes in the array
foreach ($events as $key => &$event) {
	if (substr($event['DTSTART'],-1) == "Z") {
		date_default_timezone_set ("UTC");
		$event['DTSTART'] = date_create_from_format('Ymd\THis\Z', $event['DTSTART']);
		$event['DTEND'] = date_create_from_format('Ymd\THis\Z', $event['DTEND']);
		
	}else {
		date_default_timezone_set ("America/New_York");
		$event['DTSTART'] = date_create_from_format('Ymd\THis', $event['DTSTART']);
		$event['DTEND'] = date_create_from_format('Ymd\THis', $event['DTEND']);
		
	}
	
	$tz = $event['DTSTART']->getTimeZone();
		if ($tz->getName() != 'America/New_York') {
			$event['DTSTART']->setTimeZone(new DateTimeZone('America/New_York'));
			$event['DTEND']->setTimeZone(new DateTimeZone('America/New_York'));
			date_default_timezone_set ("America/New_York");	
		}
	
	//Date is after End Date
	$enddate = date_format($event['DTEND'],"Ymd");
		if (date("Ymd") > $enddate) {
			unset($events[$key]);
		}
	
	//Date is before Start Date 
	$startdate = date_format($event['DTSTART'],"Ymd");
		if (date("Ymd") < $startdate) {
			unset($events[$key]);
		}
	$laptopcartpos = strpos($event['X-TRUMBA-CUSTOMFIELD;NAME="Event Name";ID=9266;TYPE=SingleLine'],'Laptop Cart');
		if ($laptopcartpos === false) {
			unset($events[$key]);
		}
		
	/* Common filter set, but these two disabled to list entire day
	//Start Time is after Current Time
	$starttime = date_format($event['DTSTART'],"Gis");
		if (date("Gis") < $starttime ) {
			unset($events[$key]);
		}
	
	//End Time is before Current Time
	$endtime = date_format($event['DTEND'],"Gis");
		if (date("Gis") > $endtime ) {
			unset($events[$key]);
		}
*/
	
	
} //End of Foreach loop

		



if (empty($events) != true) {
	
	foreach ($events as $key => &$event) {
		
		//Start the Rest of the HTML for the Page
		//Start Line Div
		echo '<div class="entry">';
		
		echo '<div id="reservation">'.$event['LOCATION'].': '.date_format($event['DTSTART'],"g:i A")." - ".date_format($event['DTEND'],"g:i A").' '.$event['X-TRUMBA-CUSTOMFIELD;NAME="Event Name";ID=9266;TYPE=SingleLine']." for ".$event['X-TRUMBA-CUSTOMFIELD;NAME="Organization";ID=9209;TYPE=SingleLine'].'</div>';	
		
		
		
		
		
		
		
		
		//end Center Div		
		echo '</div>';
		
		
		
				
	}//End of For Each

}//End of If Empty
if (empty($events) == true) {
	//echo '<div id="header"> No Reservations Today! </div>';
	echo '<script type="text/javascript">';
	echo 'location.replace("http://athena.east.sccc.i/25livecal/dashboard/currentnext.php");';
	echo '</script>';
}



?>

</div>


</body>

</html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>


<title>Next Class</title>
<meta http-equiv="refresh" content="300">

<link href='../css/sign.css' rel='stylesheet' type='text/css' />
<link href='http://fonts.googleapis.com/css?family=Stoke' rel='stylesheet' type='text/css'>


<?php
// Get the Variables to determine room and CSS version
$room = $_GET['room'];	

// Allows date shown to be modified by adding days. date="6" moves date forward 6 days
$modifydate = "0";
$modfile = fopen("../common/modify.ini", "r") or die("Unable to open file!");
$modifydate = fread($modfile,filesize("../common/modify.ini"));
fclose($myfile);
$modify  = mktime(0, 0, 0, date("m")  , date("d")+$modifydate, date("Y"));


//Based on the Room Number, customize the background color and font. Otherwise it's black and white.	
echo '<style type="text/css">';
echo "body {";
	
//Special coloring for certain rooms
if ($room == "orient-200") {	
	echo "background-color: #138800;";
	echo "color: #FFF;";
}
if ($room == "orient-201") {	
	echo "background-color: #CC0000;";
	echo "color: #FFF;";
}
if ($room == "orient-204") {	
	echo "background-color: #ff9900;";
}
if ($room == "orient-205") {	
	echo "background-color: #efdb00;";
}
if ($room == "orient-233") {
	echo "background-color: #5205B3;";
	echo "color: #FFF;";
}
if ($room == "orient-237") {	
	echo "background-color: #6e6e6e;";
	echo "color: #FFF;";
}
//Finish the HTML Style & Body Section section

echo '</style>';
echo '</head>';

//Start the HTML Bodt Tag
echo '<body>';
echo '<div id="container">';

//Include the parser
require '../class.icsparser.php';

//Use the Parser to put each event into the array Events.
$ical = new ical('http://25livepub.collegenet.com/calendars/etu-'.$room.'.ics');
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
		if (date("Ymd",$modify) > $enddate) {
			unset($events[$key]);
		}
	
	//Date is before Start Date 
	$startdate = date_format($event['DTSTART'],"Ymd");
		if (date("Ymd",$modify) < $startdate) {
			unset($events[$key]);
		}
	
	//Start Time is after Current Time
	//Common filtering with other pages, but this section is commented to include next classes
	//$starttime = date_format($event['DTSTART'],"Gis");
//		if (date("Gis") < $starttime ) {
//			unset($events[$key]);
//		}
	
	//End Time is before Current Time
	$endtime = date_format($event['DTEND'],"Gis");
		if (date("Gis") > $endtime ) {
			unset($events[$key]);
		}

	//Remove Current Class
	
	$starttime = date_format($event['DTSTART'],"Gis");
	
	if (($starttime <= date("Gis")) && (date("Gis") <= $endtime)) {
		unset($events[$key]);
	}
	
	//Run the seperate PHP page to make string changes
	include ("../common/replace.php");

} //End of Foreach loop

//Reset Array Key numbers
	$events = array_values($events);

if (empty($events) != true) {
	//Current Class Header 
	echo '<div id="header">Next Class</div>';
	
	//Start Left Content Box
		echo '<div class="leftcontent">';
	
	//Create Class Number Box	
		$classnumber = substr($events[0]['X-TRUMBA-CUSTOMFIELD;NAME="Event Name";ID=9266;TYPE=SingleLine'],0,7);	
		echo '<div class="classnum">'.$classnumber.'</div>';
	
	//Create Description Box
		echo '<div id="description">'.$events[0]['SUMMARY'].'</div>';
	
	// Create Faculty Name Box
		$profpos = strrpos($events[0]['DESCRIPTION'],",");
		$profname = substr($events[0]['DESCRIPTION'],20,$profpos - 20);
		$profname = stripslashes($profname);
		$profFname = substr($events[0]["DESCRIPTION"],$profpos + 2,1);
		echo '<div id="profname" class="data"> '.$profname.', '.$profFname.'</div>';
	
	//Close LeftContent div Tag
		echo '</div>';	
	
	//Start Right Content Div	
		echo '<div class="rightcontent">';	
	
	//If the Timezone is anything except New York, change it to New York
	$tz = $event['DTSTART']->getTimeZone();
		
		if ($tz->getName() != 'America/New_York') {
			$event['DTSTART']->setTimeZone(new DateTimeZone('America/New_York'));
			$event['DTEND']->setTimeZone(new DateTimeZone('America/New_York'));	
		}
			
	//Start Time Box
		echo '<div>';
		echo '<div id="starttimelabel" class="label">Start</div>';
		echo '<div id="starttime" class="label">'.date_format($events[0]['DTSTART'],"g:i A").'</div>';
		echo '</div>';
		
	//End Time Box
		echo '<div>';
		echo '<div id="endtimelabel"class="label">End</div>';	
		echo '<div id="endtime" class="label">'.date_format($events[0]['DTEND'],"g:i A").'</div>';
		echo '</div>';
		
	//CRN Box
		echo '<div>';
		echo '<div id="crnlabel" class="label"> CRN# </div>';
		echo '<div id="crn" class="label">'.substr($events[0]['X-TRUMBA-CUSTOMFIELD;NAME="Reservation Name";ID=9269;TYPE=SingleLine'],0,5).'</div>';
		echo '</div>';
		
		//end container
		echo '</div>';
		echo '</div>';
		
	
}//End of If Empty

else { //If the Array is empty
	date_default_timezone_set ("America/New_York");	
	echo '<div id="noclass"> '.date("g:i A").'</div>';
	
	//end Container Div
	echo '</div>';
			
}; // End of Else


?>
</body>
</html>
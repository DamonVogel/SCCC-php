<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php
// Get the Variables to determine room and CSS version
$room = $_GET['room'];	
$version = $_GET['ver'];

// Allows date shown to be modified by adding days. date="6" moves date forward 6 days
$modifydate = "0";
$modfile = fopen("../common/modify.ini", "r") or die("Unable to open file!");
$modifydate = fread($modfile,filesize("../common/modify.ini"));
fclose($myfile);
$modify  = mktime(0, 0, 0, date("m")  , date("d")+$modifydate, date("Y"));


//Customize the Title with the room number
echo '<title>Room Listing</title>';
echo '<meta http-equiv="refresh" content="300">';



//Use the style.css for everything else

echo "<link href='stylelistv3.css' rel='stylesheet' type='text/css' />";
echo "<link href='http://fonts.googleapis.com/css?family=Stoke' rel='stylesheet' type='text/css'>";


//Based on the Room Number, customize the background color and font. Otherwise it's black and white.	
echo '<style type="text/css">';
echo "body {";
	
//Special coloring for certain rooms
if ($room == "orient-200") {	
	echo "background-color: #FFF;";
	echo "color: #138800;";
	echo "border-color: #138800;";
}
if ($room == "orient-201") {	
	echo "background-color: #FFF;";
	echo "color: #CC0000;";
	echo "border-color: #CC0000;";
}
if ($room == "orient-204") {	
	echo "background-color: #FFF;";
	echo "color: #ff9900;";
	echo "border-color: #ff9900;";
}
if ($room == "orient-205") {	
	echo "background-color: #efdb00;";
	echo "border-color: #000;";
}
if ($room == "orient-233") {
	echo "background-color: #FFF;";
	echo "color: #9400f7;";
	echo "border-color: #9400f7;";
}
if ($room == "orient-237") {	
	echo "background-color: #FFF;";
	echo "color: #6e6e6e;";
	echo "border-color: #6e6e6e;";
}
//Finish the HTML Style & Body Section section

echo '}';
echo '</style>';
echo '</head>';


//Start the HTML Bodt Tag
echo '<body>';
echo '<div id="container">';
echo '<div id="header">';

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
			
		}
	date_default_timezone_set ("America/New_York");	
	
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
//Reset Array Key numbers
		$events = array_values($events);
	
		echo date('l',$modify)."'s Schedule";

//end Header Div
		echo '</div>';
//Start Line Div
		echo '<div id="center">';	
			
if (empty($events) != true) {
	foreach ($events as $event) {
		
		//Run the seperate PHP page to make string changes
		include ("../common/replace.php");	

		// Div for Chevron of Current Class
		echo '<div id="now">';
		
		$starttime = date_format($event['DTSTART'],"Gis");
		$endtime = date_format($event['DTEND'],"Gis");
		
	if (($starttime <= date("Gis")) && (date("Gis") <= $endtime)) {
		echo '>';
	}
		echo '</div>';
		
		//Print out the times
		echo '<div id="timeblock">'.date_format($event['DTSTART'],"g:i A")." - ".date_format($event['DTEND'],"g:i A").'</div>';
		
		//Create Prof Name
		$profpos = strrpos($event['DESCRIPTION'],",");
		$profname = substr($event['DESCRIPTION'],20,$profpos - 20);
		$profname = stripslashes($profname);
		$profFname = substr($event["DESCRIPTION"],$profpos + 2,1);
		
		//Description Div
		echo '<div id="description">'.$profname.', '.$profFname.' - '.$event['SUMMARY'].'</div>';
				
	}//End of For Each
	//end Center Div		
		echo '</div>';


}//End of If Empty
if (empty($events) == true) {
	echo '<div id="header"> No Classes Today! </div>';
}

?>
</body>
</html>
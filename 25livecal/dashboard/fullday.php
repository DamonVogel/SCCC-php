<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Dashboard</title>
<link href='http://fonts.googleapis.com/css?family=News+Cycle' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Hammersmith+One' rel='stylesheet' type='text/css'>
<link href="../css/fullday.css" rel="stylesheet" type="text/css" />
<meta http-equiv="refresh" content="60">
</head>
<body>
<div id="container">
  <?php

// Allows date shown to be modified by adding days. date="6" moves date forward 6 days
$modifydate = "0";
$modfile = fopen("../common/modify.ini", "r") or die("Unable to open file!");
$modifydate = fread($modfile,filesize("../common/modify.ini"));
fclose($myfile);
$modify  = mktime(0, 0, 0, date("m")  , date("d")+$modifydate, date("Y"));

echo '<div id="leftcontent">';
echo "<div class='header'>Today's Full Day Schedule</div>";

//Include the parser
require '../class.icsparser.php';

$rooms = array("orient-200","orient-201","orient-204","orient-205","orient-233","orient-237");

foreach ($rooms as $room) {

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
		if (date("Ymd", $modify) > $enddate) {
			unset($events[$key]);
		}
	
	//Date is before Start Date 
	$startdate = date_format($event['DTSTART'],"Ymd");
		if (date("Ymd", $modify) < $startdate) {
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

//Run the seperate PHP page to make string changes
include ("../common/replace.php");
	
	
} //End of Foreach loop

//Reset Array Key numbers
$events = array_values($events);

if ($room == "orient-205") {
	
	echo '<div class="clearme"></div>';
	
}

echo '<div class="room" id="'.$room.'">';
$displayroom = substr_replace($room, " ", "6", 1);
echo '<div class="roomheader">'.$displayroom.'</div>';

if (empty($events) != true) {	
	if (count($events) < 5) {
		foreach ($events as $key => &$event) {
			
			echo '<div class="class">';
			if (strpos($event['SUMMARY']," - ")=== false) {
			//Print out the times and Prof Name
			$event['DESCRIPTION'] = stripslashes($event['DESCRIPTION']);
			//Figure out where the times and the names are by looking for : and ,
			$summpos = stripos($event['DESCRIPTION'],":");
			$summpos2 = stripos($event['DESCRIPTION'],',');
			echo '<div>'.date_format($event['DTSTART'],"g:ia")."-".date_format($event['DTEND'],"g:ia")." - Prof. ".substr($event['DESCRIPTION'],$summpos+2,$summpos2-$summpos-2)."</div>";
			echo '<div class="descriptionline">'.substr($event['X-TRUMBA-CUSTOMFIELD;NAME="Event Name";ID=9266;TYPE=SingleLine'],0,3).substr($event['X-TRUMBA-CUSTOMFIELD;NAME="Event Name";ID=9266;TYPE=SingleLine'],4,3).": ".$event['SUMMARY'].'</div>';
			} else {
				$namepos = strpos($event["SUMMARY"]," - ");
				echo '<div>'.date_format($event['DTSTART'],"g:ia")."-".date_format($event['DTEND'],"g:ia")." - Prof. ".substr($event['SUMMARY'],$namepos +2)."</div>";
				echo '<div class="descriptionline">'.substr($event['SUMMARY'],0,$namepos).':<span style="color:red">Room Reservation</span></div>';
			}
			//end Center Div		
			echo '</div>';
		}//End of For Each
	}else { //end of if count < 5
		$now = new DateTime("now");
			foreach ($events as $key => &$event) {
				if (($now < $event['DTEND']) && ($now > $event['DTSTART'])) {
				$nowclass = $key;
				}
			}
			if ($nowclass > 0) {
					echo '<div style="text-align:center;"> -more-</div>';	
			}
			foreach ($events as $key => &$event) {
				if ($nowclass + 3 < count($events)) {
					if (($key <= $nowclass + 3)&&($key >= $nowclass)) {
						echo '<div class="class">';
						if (strpos($event['SUMMARY']," - ")=== false) {
							//Print out the times and Prof Name
							$event['DESCRIPTION'] = stripslashes($event['DESCRIPTION']);
							//Figure out where the times and the names are by looking for : and ,
							$summpos = stripos($event['DESCRIPTION'],":");
							$summpos2 = stripos($event['DESCRIPTION'],',');
							echo '<div>'.date_format($event['DTSTART'],"g:ia")."-".date_format($event['DTEND'],"g:ia")." - Prof. ".substr($event['DESCRIPTION'],$summpos+2,$summpos2-$summpos-2)."</div>";
							echo '<div class="descriptionline">'.substr($event['X-TRUMBA-CUSTOMFIELD;NAME="Event Name";ID=9266;TYPE=SingleLine'],0,3).substr($event['X-TRUMBA-CUSTOMFIELD;NAME="Event Name";ID=9266;TYPE=SingleLine'],4,3).": ".$event['SUMMARY'].'</div>';
						} else {
							$namepos = strpos($event["SUMMARY"]," - ");
							echo '<div>'.date_format($event['DTSTART'],"g:ia")."-".date_format($event['DTEND'],"g:ia")." - Prof. ".substr($event['SUMMARY'],$namepos +2)."</div>";
							echo '<div class="descriptionline">'.substr($event['SUMMARY'],0,$namepos).':<span style="color:red">Room Reservation</span></div>';
						}		
				echo '</div>';
					}
				}else {
					if (($key <= count($events))&&($key >= count($events)-4)) {
						echo '<div class="class">';
						if (strpos($event['SUMMARY']," - ")=== false) {
							//Print out the times and Prof Name
							$event['DESCRIPTION'] = stripslashes($event['DESCRIPTION']);
							//Figure out where the times and the names are by looking for : and ,
							$summpos = stripos($event['DESCRIPTION'],":");
							$summpos2 = stripos($event['DESCRIPTION'],',');
							echo '<div>'.date_format($event['DTSTART'],"g:ia")."-".date_format($event['DTEND'],"g:ia")." - Prof. ".substr($event['DESCRIPTION'],$summpos+2,$summpos2-$summpos-2)."</div>";
							echo '<div class="descriptionline">'.substr($event['X-TRUMBA-CUSTOMFIELD;NAME="Event Name";ID=9266;TYPE=SingleLine'],0,3).substr($event['X-TRUMBA-CUSTOMFIELD;NAME="Event Name";ID=9266;TYPE=SingleLine'],4,3).": ".$event['SUMMARY'].'</div>';
						} else {
							$namepos = strpos($event["SUMMARY"]," - ");
							echo '<div>'.date_format($event['DTSTART'],"g:ia")."-".date_format($event['DTEND'],"g:ia")." - Prof. ".substr($event['SUMMARY'],$namepos +2)."</div>";
							echo '<div class="descriptionline">'.substr($event['SUMMARY'],0,$namepos).':<span style="color:red">Room Reservation</span></div>';
						}		
				echo '</div>';
					}
					
					
					
					
					
					
				}
			}
			if (count($events) > $nowclass + 4) {
					echo '<div style="text-align:center;"> -more-</div>';	
			}
	}
	echo '</div>'; //Close div of div class room
}else {
	echo "No Classes Today";
	echo "</div>";
}
}
echo '</div>';
echo '</div>';
?>
</div>
<!--End Container-->
</body>
</html>
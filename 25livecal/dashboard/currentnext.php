<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script>
$(function() {
    $('#dow').submit(function() {
        this.form.submit();
    });
});
</script>


<!--Title and Load fonts-->
<title>Dashboard</title>
<link href='http://fonts.googleapis.com/css?family=News+Cycle' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Hammersmith+One' rel='stylesheet' type='text/css'>
<link href="../css/currentnext.css" rel="stylesheet" type="text/css" />
<!--<meta http-equiv="refresh" content="60">-->


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

//Include the parser
require '../class.icsparser.php';


$rooms = array("orient-200","orient-201","orient-204","orient-205","orient-233","orient-237");

foreach ($rooms as $room) {


//Use the Parser to put each event into the array Events.
$ical = new ical('http://25livepub.collegenet.com/calendars/etu-'.$room.'.ics');
$events = ($ical->events());


//This loop filters out all of the non matching classes in the array
foreach ($events as $key => &$event) {
	
	//If the date and time is in Zulu Time, then change it to UTC
	if (substr($event['DTSTART'],-1) == "Z") {
		date_default_timezone_set ("UTC");
		$event['DTSTART'] = date_create_from_format('Ymd\THis\Z', $event['DTSTART']);
		$event['DTEND'] = date_create_from_format('Ymd\THis\Z', $event['DTEND']);
		
	}else { //Otherwise change it to the New York Time Zone
		date_default_timezone_set ("America/New_York");
		$event['DTSTART'] = date_create_from_format('Ymd\THis', $event['DTSTART']);
		$event['DTEND'] = date_create_from_format('Ymd\THis', $event['DTEND']);
	}
	//If the start time zone is New York, change the End time zone to match.
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

//Run the seperate PHP page to make string changes
include ("../common/replace.php");	
	
	
} //End of Foreach loop

//Reset Array Key numbers
	$events = array_values($events);

//Push the the Orient 205 box to a new line.
if ($room == "orient-205") {
	
	echo '<div class="clearme"></div>';
	
}
//Room box title
echo '<div class="perroom" id = '.$room.'>';
$displayroom = substr_replace($room, " ", "6", 1);
echo '<div class="roomheader" id = rh'.$room.'>'.$displayroom.'</div>';

//If there are any events...
if (empty($events) != true) {
		//Format the start and end times
		$starttime = date_format($events['0']['DTSTART'],"Gis");
		$endtime = date_format($events['0']['DTEND'],"Gis");
		
		//If it is during a class
	if (($starttime <= date("Gis")) && (date("Gis") <= $endtime)) {
		echo '<div class="currentclass">';
		//If the Summary String includes a " - ", then it's a reservation.
		if (strpos($events['0']['SUMMARY']," - ")=== false) {
			echo '<div class="currently">'.substr($events['0']['X-TRUMBA-CUSTOMFIELD;NAME="Event Name";ID=9266;TYPE=SingleLine'],0,7).' @ '.date_format($events['0']['DTSTART'],"g:i A").'</div>';
			echo $events['0']['SUMMARY'];
			echo "</br>";
			$profpos = strrpos($events['0']['DESCRIPTION'],",");
			$profname = substr($events['0']['DESCRIPTION'],20,$profpos - 20);
			$profname = stripslashes($profname);
			echo 'Prof. '.$profname;
			echo "</br>";
			$now = new DateTime("now");
			$timeleft = $now->diff($events['0']['DTEND']);
			echo '<div>Ends in: '.$timeleft->h.'h '.$timeleft->i.'m</div>';
			echo '</div>';	
	}else {
		$classpos = strpos($events['0']['SUMMARY'],"-");
		$classname = substr($events['0']['SUMMARY'],0,$classpos);
		if (ctype_digit(substr($events['0']['SUMMARY'],3,3)) === true) {
			$classname = rtrim(chunk_split($classname,3," ")," ");
		}
		echo '<div class="currently">'.$classname.' @ '.date_format($events['0']['DTSTART'],"g:i A").'</div>';
		
		$profname = substr($events['0']['SUMMARY'],$classpos+2);
		$profname = stripslashes($profname);
		echo 'Prof. '.$profname;
		echo "</br>";
		$now = new DateTime("now");
		$timeleft = $now->diff($events['0']['DTEND']);
		echo '<div>Ends in: '.$timeleft->h.'h '.$timeleft->i.'m</div>';
		echo '</div>';	
		
		
	}
	
	
		
		if (empty($events['1']) == 1) {
			echo '<div class="then" id = rh'.$room.'> -then- </div>';
		} else {
			echo '<div class="then" id = rh'.$room.'>';
			echo '-then- </br>';
			echo "</div>";
			echo '<div class="openfor" id = rh'.$room.'>';
			$timeleft = $events['0']['DTEND']->diff($events['1']['DTSTART']);
			echo '<div>Open For: '.$timeleft->h.'h '.$timeleft->i.'m</div>';
			echo "</div>";
			echo '<div class="then" id = rh'.$room.'>';
			echo '-then- </br>';
			echo "</div>";
		}
		
		
		
		//Next Class
		if (empty($events['1']) != 1) {
			echo '<div class="nextclass">';
			if (strpos($events['1']['SUMMARY']," - ")=== false) {
			echo '<div class="next">'.substr($events['1']['X-TRUMBA-CUSTOMFIELD;NAME="Event Name";ID=9266;TYPE=SingleLine'],0,7).' @ '.date_format($events['1']['DTSTART'],"g:i A").'</div>';
			echo $events['1']['SUMMARY'];
			echo "</br>";
			$profpos = strrpos($events['1']['DESCRIPTION'],",");
			$profname = substr($events['1']['DESCRIPTION'],20,$profpos - 20);
			$profname = stripslashes($profname);
			echo 'Prof. '.$profname;
			echo "</br>";
			//echo date_format($events['1']['DTSTART'],"g:i A").' - ' .date_format($events['1']['DTEND'],"g:i A");  
			echo '</div>';
			}else {
				$classpos = strpos($events['1']['SUMMARY'],"-");
				$classname = substr($events['1']['SUMMARY'],0,$classpos);
				if (ctype_digit(substr($events['1']['SUMMARY'],3,3)) === true) {
					$classname = rtrim(chunk_split($classname,3," ")," ");
				}
					echo '<div class="currently">'.$classname.' @ '.date_format($events['1']['DTSTART'],"g:i A").'</div>';
					$profname = substr($events['1']['SUMMARY'],$classpos+2);
					$profname = stripslashes($profname);
					echo '<span style="color:red">Room Reservation</span>';
					echo '</br>Prof. '.$profname;
					echo '</div>';	
				
				
			}
		} else {
		echo '<div class="nextclass">';
			echo '<div class="next">';
				echo "Open Rest of Day";
			echo '</div>';
		echo '</div>';
		
		}
		
	} else { //If there is no current Class
		echo '<div class="currentclass">';
			echo '<div class="currently" id = rh'.$room.'>';
				
				$date2 = new DateTime("now");
				$timeleft = $date2->diff($events['0']['DTSTART']);
				
				echo '<div>Open For: '.$timeleft->h.'h '.$timeleft->i.'m</div>'; 
				
			echo '</div>';
		echo '</div>';
		echo '<div class="then"> -then- </div>';
		//Second Class
		
		if (empty($events['0']) != 1) {
			echo '<div class="nextclass">';
			if (strpos($events['0']['SUMMARY']," - ")=== false) {
				
			
			echo '<div class="next">'.substr($events['0']['X-TRUMBA-CUSTOMFIELD;NAME="Event Name";ID=9266;TYPE=SingleLine'],0,7).' @ '.date_format($events['0']['DTSTART'],"g:i A").'</div>';
			echo $events['0']['SUMMARY'];
			echo "</br>";
			$profpos = strrpos($events['0']['DESCRIPTION'],",");
			$profname = substr($events['0']['DESCRIPTION'],20,$profpos - 20);
			$profname = stripslashes($profname);
			echo 'Prof. '.$profname;
			echo "</br>";
			//echo date_format($events['0']['DTSTART'],"g:i A").' - ' .date_format($events['0']['DTEND'],"g:i A");  
			
			}else {
				$classpos = strpos($events['0']['SUMMARY'],"-");
				$classname = substr($events['0']['SUMMARY'],0,$classpos);
				if (ctype_digit(substr($events['0']['SUMMARY'],3,3)) === true) {
					$classname = rtrim(chunk_split($classname,3," ")," ");
				}
				echo '<div class="next">'.$classname.' @ '.date_format($events['0']['DTSTART'],"g:i A").'</div>';
				echo '<span style="color:red">Room Reservation</span>';
				$profname = substr($events['0']['SUMMARY'],$classpos+2);
				$profname = stripslashes($profname);
				echo '</br>Prof. '.$profname;
				echo '</div>';
			}
			echo '</div>';
		}		
	}
					
} 	else { //If there are NO classes left for the day
		echo '<div class="currentclass">';
			echo '<div class="currently">';
				echo "No More</br>";
				echo "Classes Today";
			echo '</div>';
		echo '</div>';		
	}

echo '</div>'; 		

};
echo '</div>';
echo '</div>';
?>
</div> <!--End Container-->
</body>
</html>
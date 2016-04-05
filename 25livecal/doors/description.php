<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>


<title>Room Title</title>
<meta http-equiv="refresh" content="300">

<link href='http://fonts.googleapis.com/css?family=Stoke' rel='stylesheet' type='text/css'>


<?php
// Get the Variables to determine room and CSS version
$room = $_GET['room'];


//Based on the Room Number, customize the background color and font. Otherwise it's black and white.	
echo '<style type="text/css">';
echo "body {";
	echo "font-family: 'Stoke', cursive;";
	echo "font-size: 80px;";
	echo 'text-align: center;';
	
//Special coloring for certain rooms
if ($room == "orient-200") {	
	echo "background-color: #138800;";
	echo "color: #FFF;";
	$roomname = "Orient 200";
	$description = "Graphic Design/Photography & Computer Art";
}
if ($room == "orient-201") {	
	echo "background-color: #CC0000;";
	echo "color: #FFF;";
	$roomname = "Orient 201";
	$description = "English/Accounting & Computer Science";
}
if ($room == "orient-204") {	
	echo "background-color: #ff9900;";
	$roomname = "Orient 204";
	$description = "English/Accounting & Computer Science";
}
if ($room == "orient-205") {	
	echo "background-color: #efdb00;";
	$roomname = "Orient 205";
	$description = "Graphic Design/Photography & Computer Art";
}
if ($room == "orient-233") {
	echo "background-color: #5205B3;";
	echo "color: #FFF;";
	$roomname = "Orient 233";
	$description = "Graphic Design/Computer Art & Interior Design";
}
if ($room == "orient-237") {	
	echo "background-color: #6e6e6e;";
	echo "color: #FFF;";
	$roomname = "Orient 237";
	$description = "English/Math & Computer Science";
}
if ($room == "") {
	$roomname = "Class Cancelled";
	echo "background-color: #ff7170;";	
}
echo '}';

echo "#roomname {";
echo "font-size: 210%;";


echo "}";	
//Finish the HTML Style & Body Section section
echo '</style>';
echo '</head>';

//Start the HTML Bodt Tag
echo '<body>';
echo '<div id="container">';
	
	echo '<div id="roomname">'.$roomname.'</div>';
	echo '<div id="description">'.$description.'</div>';
	
	//end Container Div
	echo '</div>';
			


?>
</body>
</html>
<?php

if ($event['SUMMARY'] == "INTER 3D") {
	$event['SUMMARY'] = "Intermediate 3D";
}	

if ($event['SUMMARY'] == "GR PORT PREP") {
	$event['SUMMARY'] = " GR Portfolio Prep";
}

if ($event['SUMMARY'] == "CA PORT ASSESS") {
	$event['SUMMARY'] = "CA Portfolio Prep";
}
if ($event['SUMMARY'] == "Photo Port Dev & Assesment") {
	$event['SUMMARY'] = "Photo Portfolio Prep";
}
	
	if ($event['SUMMARY'] == "ColorTheoryElectronicAppl") {
	$event['SUMMARY'] = "Color Theory";
}	
	
	
if (strpos($event['SUMMARY'],"Mathematics") !== FALSE) {
	$event['SUMMARY'] = str_replace("Mathematics","Math",$event['SUMMARY']);
}	

if (strpos($event['SUMMARY'],"Laboratory") !== FALSE) {
	$event['SUMMARY'] = str_replace("Laboratory","Lab",$event['SUMMARY']);
}

if (strpos($event['SUMMARY'],"Introduction") !== FALSE) {
	$event['SUMMARY'] = str_replace("Introduction","Intro",$event['SUMMARY']);
}	
	
if (strpos($event['SUMMARY'],"Accounting") !== FALSE) {
	$event['SUMMARY'] = str_replace("Accounting","Acct.",$event['SUMMARY']);
}

if (strpos($event['SUMMARY'],"Development") !== FALSE) {
	$event['SUMMARY'] = str_replace("Development","Devel.",$event['SUMMARY']);
}

if (strpos($event['SUMMARY'],"On-Location") !== FALSE) {
	$event['SUMMARY'] = str_replace("People Illustrat","",$event['SUMMARY']);
}

if (strpos($event['SUMMARY'],"Sc. and Tech") !== FALSE) {
	$event['SUMMARY'] = str_replace("Sc. and Tech","",$event['SUMMARY']);
}
if (strpos($event['SUMMARY'],"Photography") !== FALSE) {
	$event['SUMMARY'] = str_replace("Photography","Photo.",$event['SUMMARY']);
}
if (strpos($event['SUMMARY'],'Photo Port Dev & Assessment') !== FALSE) {
	$event['SUMMARY'] = str_replace('Photo Port Dev & Assessment','Photo Portfolio',$event['SUMMARY']);
}

if (strpos($event['SUMMARY'],'Kitchen and Bath Design') !== FALSE) {
	$event['SUMMARY'] = str_replace('Design','',$event['SUMMARY']);
}
if (strpos($event['DESCRIPTION'],'Odell-Hamilton') !== FALSE) {
	$event['DESCRIPTION'] = str_replace('Odell-Hamilton','Odell',$event['DESCRIPTION']);
}






?>

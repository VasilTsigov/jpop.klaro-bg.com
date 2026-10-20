<?
date_default_timezone_set("Europe/Sofia");
$tag = date("d"); 
$monatZahl = date("m");
$month = date("F");
$jahr = date("Y");
if ($month == "January") {
	$monat = "Januar";
} else if ($month == "February") {
	$monat = "Februar";
} else if ($month == "March") {
	$monat = "März";
} else if ($month == "April") {
	$monat = "April";
} else if ($month == "May") {
	$monat = "Mai";
} else if ($month == "June") {
	$monat = "Juni";
} else if ($month == "July") {
	$monat = "Juli";
} else if ($month == "August") {
	$monat = "August";
} else if ($month == "September") {
	$monat = "September";
} else if ($month == "October") {
	$monat = "Oktober";
} else if ($month == "November") {
	$monat = "November";
} else if ($month == "December") {
	$monat = "Dezember";
} 
$datumT = "$tag. $monat $jahr";
$datumZ = "$tag.$monatZahl.$jahr";
// NULL
$null = " ";
?>
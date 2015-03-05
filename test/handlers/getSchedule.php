<?php
require_once( "../models/DB.php" );
require_once( "../models/courses.php" );

$courses = $_POST['courses'];
$startTime = (int)$_POST['startTime'];
$endTime = (int)$_POST['endTime'];
$daysOff = $_POST['daysOff'];

$schedule = course::generateSchedule($courses,$dbh);
//$sections = course::chooseASection($schedule);
//$sections = course::returnOnlineClasses($schedule);
//$sections = course::removeFridayCourses($schedule);



$sections = $schedule;
//Lame way of doing it. But idc
if($daysOff["Monday"] === "true")
	$sections = course::removeCoursesByDay($sections,"M");
if($daysOff["Tuesday"] === "true")
	$sections = course::removeCoursesByDay($sections,"T");
if($daysOff["Wednesday"] === "true")
	$sections = course::removeCoursesByDay($sections,"W");
if($daysOff["Thursday"] === "true")
	$sections = course::removeCoursesByDay($sections,"R");
if($daysOff["Friday"] === "true")
	$sections = course::removeCoursesByDay($sections,"F");


	
//if($startTime != null && $endTime != null)
$sections = course::removeCoursesByTime($sections, $startTime, $endTime);
$array = course::makeArray($sections);
//$sections = course::createValidSchedule($sections);

$schedule = course::array_cartesian_product($array);
$schedule = course::removeOverlappingCourses($schedule);

//Later change it to:
//echo json_encode($sections);
//echo json_encode($array);
echo json_encode($schedule);
?>
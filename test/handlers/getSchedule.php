<?php
require_once( "../models/DB.php" );
require_once( "../models/courses.php" );

$courses = $_POST['courses'];
$timeInfo = $_POST['daysInfo'];
$startTime = (int)$_POST['startTime'];
$endTime = (int)$_POST['endTime'];
$daysOff = $_POST['daysOff'];

$schedule = course::generateSchedule($courses,$dbh);
//$sections = course::chooseASection($schedule);
//$sections = course::returnOnlineClasses($schedule);
//$sections = course::removeFridayCourses($schedule);



$sections = $schedule;

$i = 0;
//foreach ($timeInfo[0]["day"]["dayOff"] as $d
while($i < 5)
{
	if($timeInfo[$i]["day"]["dayOff"] == "true")
	{
		$sections = course::removeCoursesByDay($sections,$i);
	}
	else
	{
		$sections = course::removeCoursesByDayAndTime($sections, $i, $sections,$d["day"]["startTime"], $sections,$d["day"]["endTime"]);
	}
	$i += 1;
}

/* foreach ($timeInfo as $d)
{
	if($d["day"]["dayOff"] == "true")
	{
		$sections = course::removeCoursesByDay($sections,$d["day"]["dayOff"]);
	}
	else
	{
		$sections = course::removeCoursesByDayAndTime($sections, $d["day"]["dayOff"], $sections,$d["day"]["startTime"], $sections,$d["day"]["endTime"]);
	}
} */
//Lame way of doing it. But idc
/* if($timeInfo[0]["day"]["dayOff"] === "true")
	$sections = course::removeCoursesByDay($sections,"M");
else
	$sections = course::removeCoursesByDayAndTime($sections, "M", $startTime[0], $endTime[0]);
if($daysOff["Tuesday"] === "true")
	$sections = course::removeCoursesByDay($sections,"T");
if($daysOff["Wednesday"] === "true")
	$sections = course::removeCoursesByDay($sections,"W");
if($daysOff["Thursday"] === "true")
	$sections = course::removeCoursesByDay($sections,"R");
if($daysOff["Friday"] === "true")
	$sections = course::removeCoursesByDay($sections,"F"); */



	
//if($startTime != null && $endTime != null)
//$sections = course::removeCoursesByTime($sections, $startTime, $endTime);
$array = course::makeArray($sections);
//$sections = course::createValidSchedule($sections);

$schedule = course::createAllPossibleSchedules($array);
$schedule = course::removeOverlappingCourses($schedule);

//Later change it to:
//echo json_encode($sections);
//echo json_encode($array);
echo json_encode($schedule);
?>
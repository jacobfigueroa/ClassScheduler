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

//foreach ($timeInfo[0]["day"]["dayOff"] as $d
for($i = 0; $i < 5; $i++)
{
	if($timeInfo[$i]["day"]["dayOff"] == "true")
	{
		$sections = course::removeCoursesByDay($sections,$i);
	}
	else
	{
		//echo "<br>" . $i . "Start: " . $timeInfo[$i]["day"]["startTime"] . " End: " . $timeInfo[$i]["day"]["endTime"] . "<br><br>";
		$sections = course::removeCoursesByDayAndTime($sections, $i, $timeInfo[$i]["day"]["startTime"], $timeInfo[$i]["day"]["endTime"]);
	}
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
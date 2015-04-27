<?php
require_once( "../models/DB.php" );
require_once( "../models/courses.php" );

$courses = $_POST['courses'];
$timeInfo = $_POST['daysInfo'];
$daysOff = $_POST['daysOff'];
$blockSchedule = $_POST['blockSchedule'];
$errors = [];

$sections = course::getAllSections($courses,$dbh);

$days = ["M","T","W","R","F"];
for($i = 0; $i < count($days); $i++) {
	if($timeInfo[$i]["day"]["dayOff"] == "true") {
		$sections = course::removeCoursesByDay($sections,$days[$i]);
	} else if ( (int)$timeInfo[$i]["day"]["startTime"] !== 0 || (int)$timeInfo[$i]["day"]["endTime"] !== 2359 ) {
		$sections = course::removeCoursesByDayAndTime($sections, $days[$i], $timeInfo[$i]["day"]["startTime"], $timeInfo[$i]["day"]["endTime"]);
	}
}

if(sizeof($sections) > 0) {
	$array = course::makeArray($sections);
	$schedule = course::createAllPossibleSchedules($array);
	$schedule = course::removeOverlappingCourses($schedule);

	if ($blockSchedule == "true") {
		$schedule = course::removeNonBlockSchedules($schedule);
	}

} else {
	$errors[] = "No courses meet your preferences";
}

echo json_encode(array('schedules' => $schedule, 'errors' => $errors ));
?>

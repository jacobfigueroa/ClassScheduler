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

	if(sizeof($schedule) == 0) {
		$errors[] = "No courses meet your preferences.";
	}

	
	if ($blockSchedule == "yes") {
		$schedule = course::removeNonBlockSchedules($schedule);
		if(sizeof($schedule) == 0) {
			$errors[] = "The courses that you've entered do not allow for a block schedule to be generated.";
		}
	}
} else {
	$errors[] = "No courses meet your preferences.";
}

//Check to see if a course is missing
if (sizeof($schedule) > 0) {
	foreach ($courses as $c) {
		$courseFound = FALSE;
		foreach ($schedule[0] as $courseInSchedule) {
			if( $c["Subject"] === $courseInSchedule->Subject && $c["CourseNumber"] === $courseInSchedule->CourseNumber ) {
				$courseFound = TRUE;
			}
		}
		if (!$courseFound) {
			$errors[] = "A schedule with " . $c["Subject"] . " " . $c["CourseNumber"] . " was not able to be generated. Please adjust your preferences.";
		}
	}
}

$errors = array_merge($errors, course::getErrors());

echo json_encode(array('schedules' => $schedule, 'errors' => $errors ));
?>

<?php
require_once( "../models/DB.php" );
require_once( "../models/courses.php" );

$courses = $_POST['courses'];
$startTime = $_POST['startTime'];
$endTime = $_POST['endTime'];
$daysOff = $_POST['daysOff'];

//Do php stuff.
//What the code might look like?
$schedule = course::generateSchedule($courses,$dbh);
$sections = course::chooseASection($schedule);

//For now:
//echo json_encode($courses);
//Later change it to:
echo json_encode($sections);
//echo json_encode($daysOff);
?>
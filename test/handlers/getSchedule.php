<?php
require_once( "../models/DB.php" );
require_once( "../models/courses.php" );

//Will probably be an array
$courses = $_POST['courses'];
//Do php stuff.
//What the code might look like?
//$schedule = course::generateSchedule($courses,$dbh);

//echo var_dump($courses);
//echo json_encode($schedule);

//echo "SCHEDULE";
echo json_encode($courses);
?>
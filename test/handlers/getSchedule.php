<?php
require_once( "../models/DB.php" );
require_once( "../models/courses.php" );

$courses = $_POST['courses'];

//Do php stuff.
//What the code might look like?
$schedule = course::generateSchedule($courses,$dbh);


//For now:
//echo json_encode($courses);
//Later change it to:
echo json_encode($schedule);
?>
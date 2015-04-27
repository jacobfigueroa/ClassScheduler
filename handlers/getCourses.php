<?php
require_once( "../models/DB.php" );
require_once( "../models/courses.php" );

$subject = $_POST['subject'];
#get list of courses from database
$courses = course::findDistinctCoursesBySubject($subject, $dbh);
echo json_encode($courses);
?>
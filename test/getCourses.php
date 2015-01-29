<?php
require_once( "DB.php" );
require_once( "courses.php" );

$subject = $_POST['subject'];
#get list of courses from database
$courses = course::findCoursesBySubject($subject, $dbh)

/*
$array = array(
    "courses" => $courses,
);
//$array['courses']=$courses;

header( "Content-type: text/json" );
echo json_encode( $array );

//include_once( "addCourse.html" );*/
?>
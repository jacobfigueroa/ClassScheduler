<?php
require_once( "./models/DB.php" );
#require_once( "courses.php" );
require_once( "./models/subject.php" );
#get list of courses from database
#$course = course::findAll( $dbh );
$subject = subject::getSubjects( $dbh );

include_once( "./views/addCourse.html" );
?>
<?php
require_once( "DB.php" );
#require_once( "courses.php" );
require_once( "subject.php" );
#get list of courses from database
#$course = course::findAll( $dbh );
$subject = subject::getSubjects( $dbh );

include_once( "addCourse.html" );
?>
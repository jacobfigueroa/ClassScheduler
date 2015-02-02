<?php
class course
{
	# class reflects a db table
	public static $tableName = "TABLE1";

	# data columns
	public $CourseName;
	public $Subject;
	public $CourseNumber;
	public $Section;
	public $Title;
	public $CRN;
	public $ScheduleType;
	public $Instructor;
	public $Days;
	public $Start;
	public $End;
	public $Bldg;
	public $Room;
	

	function copyFromRow( $row ) {
		$this->CourseName = $row['Course'];
		$this->Subject = $row['Subject'];
		$this->CourseNumber = $row['CourseNumber'];
		$this->Section = $row['Section'];
		$this->Title = $row['Title'];
		$this->CRN = $row['CRN'];
		$this->scheduletype = $row['ScheduleType'];
		$this->Instructor = $row['Instructor'];
		$this->Days = $row['Days'];
		$this->Start = $row['Start'];
		$this->End = $row['End'];
		$this->Bldg = $row['Bldg'];
		$this->Room = $row['Room'];
	}

	/* function __toString() {
		return $this->name." ".$this->pretime." ".$this->totaltime." ".$this->rating." ".$this->id;
	} */

	# inflation - making this object match a db row
	function findByCourseName( $CourseName, $dbh ) {
		$stmt = $dbh->prepare( "select * from ".course::$tableName." where CourseName = :CourseName" );
		$stmt->bindParam( ':CourseName', $CourseName );
		$stmt->execute();

		$course = new course();
		$row = $stmt->fetch();
		$course->copyFromRow( $row );
		return $course;
	}

	static function findAll( $dbh ) {
		$stmt = $dbh->prepare( "select * from ".course::$tableName );
		$stmt->execute();

		$result = array();
		while( $row = $stmt->fetch() ) {
			$course = new course();
			$course->copyFromRow( $row );
			$result[] = $course;
		}
		return $result;
	}

	static function findCoursesBySubject($subject, $dbh)
	{
		$stmt = $dbh->prepare( "select * from ".course::$tableName." where Subject = :Subject" );
		$stmt->bindParam( ':Subject', $subject );
		$stmt->execute();

		while( $row = $stmt->fetch() ) {
			$course = new course();
			$course->copyFromRow( $row );
			$result[] = $course;
		}
		return $result;
	}

	static function findDistinctCoursesBySubject($subject, $dbh)
	{
		//SELECT DISTINCT CourseNumber,Title FROM `TABLE1` WHERE Subject = "CSCI"
		$stmt = $dbh->prepare( "SELECT DISTINCT CourseNumber,Title FROM ".course::$tableName." WHERE Subject = :Subject" );
		$stmt->bindParam( ':Subject', $subject );
		$stmt->execute();

		while( $row = $stmt->fetch() ) {
			$course = new course();
			$course->CourseNumber = $row['CourseNumber'];
			$course->Title = $row['Title'];
			$result[] = $course;
		}
		return $result;
	}
	
	/* static function getSubjects( $dbh ) {
		$stmt = $dbh->prepare( "select distinct subject from TABLE1" );
		$stmt->execute();
		
		$result = array();
		while( $row = $stmt->fetch() ) {
			$course = new course();
			$course->copyFromRow( $row );
			$result[] = $course;
		}
		return $result;
	} */


}
?>
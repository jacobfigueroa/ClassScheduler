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
		$this->ScheduleType = $row['ScheduleType'];
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
	
	
	static function generateSchedule($courses, $dbh)
	{
		//grabs all sections of the chosen classes
		foreach ($courses as $c)
		{
			$stmt = $dbh->prepare( "SELECT * FROM ".course::$tableName." WHERE Subject = :Subject AND CourseNumber = :CourseNumber" );
			$stmt->bindParam( ':Subject', $c[0] );
			$stmt->bindParam( ':CourseNumber', $c[1] );
			$stmt->execute();
			
			while( $row = $stmt->fetch() ) 
			{
				$course = new course();
				$course->copyFromRow($row);
				$result[] = $course;
			}
		}
		return $result;
	}
	
	
	static function chooseASection($schedule)
	{
		//chooses the first section in the list of all classes
		$course = new course();
		$counter = 1;
		foreach($schedule as $s)
		{
			if(counter == 1)
			{
				$course = $s;
				$result[] = $course;
			}
			else
			{
				if($course->Title != $s->Title)
				{
					$course = $s;
					$result[] = $course;
				}
			}
			$counter = $counter + 1;
			
		}
		echo $result;
		return $result;
	}
	
	static function returnOnlineClasses($schedule)
	{
		//searches through all classes in schedule and removes all classes that arent online
		$course = new course();
		$searchPAram = 'L';
		foreach($schedule as $s)
		{
			if (strpos($s->Section, $searchParam) !== FALSE)
			{
				$course = $s;
				$result[] = $course;
			}
		}
		
		return $result;
	}
	
	static function removeFridayCourses($schedule)
	{
		//searches through all classes in schedule to remove specific days, can be easily modded to remove any day
		$course = new course();
		$dayOff = 'F';
		foreach($schedule as $s)
		{
			if (strpos($s->Days, $dayOff) === FALSE)
			{
				$course = $s;
				$result[] = $course;
			}
		}
		return $result;
	}
	
	static function createValidSchedule($schedule)
	{
	//makes sure classes start and end times dont overlap
	//works with only two classes currently
		$course = new course();
		$counter = 0;
		foreach($schedule as $s)
		{
			if($counter == 0)
			{
				$course = $s;
				$result[] = $course;
				$counter = $counter + 1;
			}
			else
			{
				if($course->Title != $s->Title)
				{
					for($i = 0; $i < $counter; $i++)
					{
						//$counter = 0
						//foreach($result[] as $r)
						//{
						//if($counter == 0)
						//{
						if(!($s->Start >= $result[$i]->Start && $s->Start <= $result[$i]->End) && !($s->End >= $result[$i]->Start && $s->End <= $result[$i]->End))
						{
							$course = $s;
							$result[] = $course;
							$counter++;
						}
						else if(!($s->Start >= $result[$i]->Start && $s->Start <= $result[$i]->End) && !($s->End >= $result[$i]->Start && $s->End <= $result[$i]->End) && $s->Days !=$result[$i]->Days)
						{
							$course = $s;
							$result[] = $course;
							$counter++;
						}
						//}
						//}
					}
				}
			}
		}
		return $result;
	}
	
}
?>
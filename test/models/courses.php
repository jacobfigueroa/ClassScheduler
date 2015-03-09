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

	# Return all courses
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

	# Return all courses of a certain subject
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

	# Return all unique courses of a certain subject. e.g. Only return CSCI 1301.01 and not CSCI 1301.01 and CSCI 1301.02 
	static function findDistinctCoursesBySubject($subject, $dbh)
	{
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
	
	static function removeOnlineClasses($schedule)
	{
		//searches through all classes in schedule and removes all classes that are online
		$course = new course();
		$searchPAram = 'L';
		foreach($schedule as $s)
		{
			if (strpos($s->Section, $searchParam) === FALSE)
			{
				$course = $s;
				$result[] = $course;
			}
		}
		
		return $result;
	}
	
	static function removeCoursesByDay($schedule, $day)
	{
		//searches through all classes in schedule to remove specific days, can be easily modded to remove any day
		$course = new course();
		$dayOff = $day;
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
	
	static function removeCoursesByTime($schedule, $start, $end)
	{
		//searches through all classes in schedule to remove specific classes by start and end time
		//if classes fall outside boundaries of preference, they are removed
		$course = new course();
		
		foreach($schedule as $s)
		{
			if ((int)$s->Start >= $start && (int)$s->End <= $end)
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
	//works with any amount of classes
		$course = new course();
		$counter = 0;
		foreach($schedule as $s)
		{
			if($counter == 0)
			{
			//adds first classes first section no matter what
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
					//calls the timeoverlap function to check for overlapping
						if($s->timeOverlap($s, $result[$i]))
						{
							$course = $s;
							$result[] = $course;
							$counter++;
						}

					}
				}
			}
		}
		return $result;
	}
	
	function timeOverlap($first, $second)
	{
	//checks to see if the times for two classes overlap
		if(!($first->Start >= $second->Start && $first->Start <= $second->End) && !($first->End >= $second->Start && $first->End <= $second->End))
		{
		//checks the times and returns true if they dont
			return true;
		}
		else if(!($first->Start >= $second->Start && $first->Start <= $second->End) && !($first->End >= $second->Start && $first->End <= $second->End) && $first->Days !=$second->Days)
		{
		//checks the days and returns true if they dont
			return true;
		}
		else
		//else they do
			return false;
	}
	
	static function makeArray($schedule)
	{
	//makes a 2 dimensional array with all the classes that are passed to it
		$course = new course();
		$titleCounter = 0;
		$sectionCounter = 0;
		$counter = 0;
		foreach($schedule as $s)
		{
			if($counter == 0)
			{
				$course = $s;
				$counter++;
			}
			if($course->Title != $s->Title)
			{
				$titleCourse = $s;
				$result[$titleCounter] = $array;
				$sectionCounter = 0;
				unset($array);
				$array = array();
				$array[$sectionCounter] = $course;
				$titleCounter++;
			}
			
			$course = $s;
			$array[$sectionCounter] = $course;
			$sectionCounter++;
			
			
		}
		$result[$titleCounter] = $array;
		return $result;
		
	}

/*
	static function createAllPossibleSchedules($courses)
	{
		for($i = 0; $i < $courses[0].length(); $i++)
		{
			$schedule[] = $course[0][$i] + returnPossibleSchedules($course[$i])
		}
	}

	static function returnPossibleSchedules($courses)
	{

	}
*/
	#$arrays contains all possible courses a person can take.
	#Each array in $arrays is organized by course
	#For example $arrays[0] contains an array of all possible ENG 1301 courses
	#For example $arrays[1] contains an array of all possible CSCI 1370 courses
	#And so on...
	#This function will return all possible combinations of all those courses
	static function createAllPossibleSchedules($arrays)
	{
	    $result = array();
	    $arrays = array_values($arrays);

	    $size = sizeof($arrays) > 0 ? 1 : 0;

	    #Calculate the number of combinations
	    foreach ($arrays as $array)
	    {
	        $size *= sizeof($array);
	    }

	    #Make each schedule
	    for ($i = 0; $i < $size; $i++)
	    {
	        $result[$i] = array();

	        #The size of $arrays is equal to the number of courses that a person is taking
	        for ($j = 0; $j < sizeof($arrays); $j++)
	        {
	        	#Put next course in the array
	            $result[$i][] = current($arrays[$j]);
	        }

	        for ($j = ($sizeIn -1); $j >= 0; $j--)
	        {
	            if (next($arrays[$j]))
	                break;
	            elseif (isset ($arrays[$j]))
	                reset($arrays[$j]);
	        }
	    }
	    return $result;
	}

	static function removeOverlappingCourses($schedules)
	{
		foreach($schedules as $s)
		{
			$courseOverlap = false;
			foreach ($s as $course1)
			{
				foreach ($s as $course2)
				{
					if ($course1 !== $course2)
					{
						if($course1->Days === $course2->Days)
						{
							if(!$course1->timeOverlap($course1,$course2))
							{
								$courseOverlap = true;
							}
						}
					}
				}
			}

			if($courseOverlap === false)
				$result[] = $s;

		}
		return $result;
	}
}
?>
<?php
class subject
{
	# class reflects a db table
	public static $tableName = "TABLE1";

	# data columns
	public $SubjectName;

	function copyFromRow( $row ) {
		$this->SubjectName = $row['subject'];
		
	}


	# inflation - making this object match a db row
	
	static function getSubjects( $dbh ) {
		$stmt = $dbh->prepare( "select distinct subject from ".subject::$tableName );
		
		$stmt->execute();
		
		$result = array();
		while( $row = $stmt->fetch() ) {
			$subject = new subject();
			$subject->copyFromRow( $row );
			$result[] = $subject;
		}
		return $result;
	}


}
?>
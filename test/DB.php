<?php
try {
	$dbh = new PDO( 'mysql:host=localhost;dbname=jacoqatp_classlistings',
				'jacoqatp_jjacob', 'blarg1234',
				array( PDO::ATTR_PERSISTENT => true ) );
} catch( PDOException $e ) {
	print "ERROR: ".$e->getMessage();
}
?>
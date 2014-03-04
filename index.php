<?php header('Content-type: text/html; charset=UTF-8');?>

<?php

require_once 'db.php';

try{
	setup();
	header('Location: ./theater.php');
}
catch(Exception $e){
	if($e->getMessage() == 'Cannot connect'){
		die('Not connected : ' . mysql_error());
	}
	elseif($e->getMessage() == 'No database'){
		InitialSetup();
		header('Location: ./theater.php');
	}
}


?>
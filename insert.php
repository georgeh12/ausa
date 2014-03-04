<?php header('Content-type: text/html; charset=UTF-8');?>

<?php

require_once 'db.php';
try{
	setup();
}
catch(Exception $e){
	echo '<span style="font-weight:bold"><a href="index.php">' . $e->getMessage() . '</a></span><br/><br/>';
}

insert($_POST);

?>
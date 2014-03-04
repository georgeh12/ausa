<?php header('Content-type: text/html; charset=UTF-8');?>

<?php
require_once 'functions.php';

echo '<html><head>';
echo '<title>Theater Listing</title>';
echo '<link rel="stylesheet" type="text/css" href="./css/my.css" />';
echo '<script type="text/javascript" src="./scripts/jquery-1.4.3.min.js"></script>';
echo StoreGetVars();
echo '<script type="text/javascript" src="./scripts/table_script.js"></script>';
echo '<script type="text/javascript">$(function(){script("schedule.php", "theater_id");});</script>';
echo '</head>';

require_once 'db.php';
try{
	setup();
}
catch(Exception $e){
	echo '<span style="font-weight:bold"><a href="index.php">' . $e->getMessage() . '</a></span><br/><br/>';
}
	
echo '<body>';
echo Home();
echo '<br/><br/>';

echo BuildTable($theater_table);
echo '<br/><br/><br/>';
echo GoButton('schedule.php', 'theater_id');
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo DeleteButton('theater_id');
echo '<br/>';
echo AddDiv('Add Theater', $theater_table);
echo updateID($theater_table, 'theater_id', true);

echo '</body></html>';
?>
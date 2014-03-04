<?php header('Content-type: text/html; charset=UTF-8');?>

<?php
require_once 'functions.php';

echo '<html><head>';
echo '<title>Movie Listing</title>';
echo '<link rel="stylesheet" type="text/css" href="./css/my.css" />';
echo '<script type="text/javascript" src="./scripts/jquery-1.4.3.min.js"></script>';
echo StoreGetVars();
echo '<script type="text/javascript" src="./scripts/table_script.js"></script>';
echo '<script type="text/javascript">$(function(){script("movie.php", "playlist_id");});</script>';
echo '</head>';

require_once 'db.php';
try{
	setup();
}
catch(Exception $e){
	echo '<span style="color:#FF0000;font-weight:bold">' . $e->getMessage() . '</span><br/><br/>';
}

echo '<body>';
echo Home();
echo '<br/><br/>';

echo BuildTable($movie_table);
echo '<br/><br/><br/>';
echo GoButton('playlist.php', 'movie_id');
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo DeleteButton('movie_id');
echo '<br/>';
echo AddDiv('Add Movie Item', $movie_table);
echo updateID($movie_table, 'movie_id', true);

echo '</body></html>';
?>
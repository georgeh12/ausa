<?php header('Content-type: text/html; charset=UTF-8');?>

<?php
require_once 'functions.php';

echo '<html><head>';
echo '<title>Playlist Listing</title>';
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
	echo '<span style="font-weight:bold"><a href="index.php">' . $e->getMessage() . '</a></span><br/><br/>';
}

echo '<body>';
echo Home();
echo '<br/><br/>';

echo BuildTable($playlist_table, '', 'ORDER BY `playlist_track`');
echo '<br/><br/><br/>';
echo GoButton('schedule.php', 'playlist_id');
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo DeleteButton('playlist_id');
echo '<br/>';
echo AddDiv('Add Playlist Item', $playlist_table);
echo FindButton('Find Movie', 'movie.php', 'playlist_id', 'movie_id');
echo updateID($playlist_table, 'playlist_id');

echo '</body></html>';
?>
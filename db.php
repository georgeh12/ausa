<?php
error_reporting(0);
/*
Easy setup and use of database. Functions:

InitialSetup() - Run only once on the server. Creates the database and tables.
setup() - Connect to server and selects database.
insert($array) - Takes one array argument containing values for any combination of theater, movie, or schedule values.
DeleteTheater($id) - Takes the theater_id as an argument.
DeleteMovie($id) - Takes the movie_id as an argument.
DeleteSchedule($id) - Takes the schedule_id as an argument.
*/

$server = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'ausa';
$theater_table = 'theater_table';
$theater_table_values = array('theater_id'=>'SERIAL', 'theater_name'=>'VARCHAR(50)', 'theater_location'=>'VARCHAR(50)');
$playlist_table = 'playlist_table';
$playlist_table_values = array('playlist_id'=>'BIGINT(20) UNSIGNED', 'playlist_name'=>'VARCHAR(50)', 'movie_id'=>'BIGINT(20) UNSIGNED', 'playlist_track'=>'BIGINT(20) UNSIGNED');
$movie_table = 'movie_table';
$movie_table_values = array('movie_id'=>'SERIAL', 'movie_filename'=>'VARCHAR(255)', 'movie_duration'=>'INT', 'movie_encoding'=>'VARCHAR(255)', 'movie_md5'=>'VARCHAR(32)');
$schedule_table = 'schedule_table';
$schedule_table_values = array('schedule_id'=>'SERIAL', 'theater_id'=>'BIGINT(20) UNSIGNED', 'playlist_id'=>'BIGINT(20) UNSIGNED', 'schedule_start'=>'DATETIME');
$table_strings = array('schedule_id'=>'Schedule #', 'theater_id'=>'Theater #', 'playlist_id'=>'Playlist #', 'movie_id'=>'Movie #', 'schedule_start'=>'Start Time', 'theater_name'=>'Name', 'theater_location'=>'Location', 'playlist_name'=>'Playlist Name', 'playlist_track'=>'Track Number', 'movie_duration'=>'Duration in ms', 'movie_encoding'=>'Encoding', 'movie_filename'=>'Filename', 'movie_md5'=>'MD5 Hash');

function InitialSetup(){
	connect();
	create_database();
	select_database();
	create_tables();
}

function setup(){
	connect();
	select_database();
}

function connect(){
	global $server, $user, $pass;
	
	if (!mysql_connect($server, $user, $pass)) {
		throw new Exception('Cannot connect');
	}
}

function select_database(){
	global $db_name;
	if(!mysql_select_db($db_name)){
		throw new Exception('No database');
	}
}

function create_database(){
	global $db_name;
	
	$createDB = "CREATE DATABASE IF NOT EXISTS `$db_name`;";
	mysql_query($createDB);
}

function create_tables(){
	global $theater_table, $theater_table_values, $playlist_table, $playlist_table_values, $movie_table, $movie_table_values, $schedule_table, $schedule_table_values;
	
	tableX($theater_table, $theater_table_values);
	tableX($playlist_table, $playlist_table_values);
	tableX($movie_table, $movie_table_values);
	tableX($schedule_table, $schedule_table_values);
}

function tableX($table, $table_values){
	$values = "";
	foreach ($table_values as $field=>$type) {
		$values .= "`$field` $type,";
	}
	$values = substr($values,0,-1);
	$query = "CREATE TABLE IF NOT EXISTS `$table`(" . $values . ");";
	mysql_query($query);
}

function insert($array){
	global $theater_table, $theater_table_values, $playlist_table, $playlist_table_values, $movie_table, $movie_table_values, $schedule_table, $schedule_table_values;
	
	if(isset($array['schedule_id'])){
		insertX($array, $schedule_table, $schedule_table_values);
	}
	elseif(isset($array['theater_id'])){
		insertX($array, $theater_table, $theater_table_values);
	}
	elseif(isset($array['playlist_id'])){
		$query = "SELECT `playlist_name` FROM `$playlist_table` WHERE `playlist_id`=${array['playlist_id']}";
		$playlist_name = mysql_fetch_row(mysql_query($query));
		if(isset($playlist_name[0])){
			$array['playlist_name'] = $playlist_name[0];
		}
		insertX($array, $playlist_table, $playlist_table_values);
	}
	elseif(isset($array['movie_id'])){
		insertX($array, $movie_table, $movie_table_values);
	}
}

function insertX($array, $table, $table_values){
	$values = "";
	foreach ($table_values as $field=>$type) {
		$values .= "'" . mysql_real_escape_string($array["$field"]) . "',";
	}
	$values = substr($values,0,-1);
	$query = "INSERT INTO `$table` VALUES(" . $values . ");";
	mysql_query($query);
}

function delete($array){
	global $theater_table, $playlist_table, $movie_table, $schedule_table;
	
	if(isset($array['schedule_id'])){
		deleteX($array['schedule_id'], $schedule_table, 'schedule_id');
	}
	elseif(isset($array['theater_id'])){
		deleteX($array['theater_id'], $theater_table, 'theater_id');
		$query = "DELETE FROM `$schedule_table` WHERE `theater_id` = " . mysql_real_escape_string($array['theater_id']);
		mysql_query($query);
	}
	elseif(isset($array['playlist_id'])){
		$query = "DELETE FROM `$playlist_table` WHERE `playlist_id` = " . mysql_real_escape_string($array['playlist_id']) . " AND `playlist_track` = ". mysql_real_escape_string($array['playlist_track']);
		mysql_query($query);
	}
	elseif(isset($array['movie_id'])){
		deleteX($array['movie_id'], $movie_table, 'movie_id');
	}
}
	
function deleteX($id, $table, $table_id){
	$query = "DELETE FROM `$table` WHERE `$table_id` = " . mysql_real_escape_string($id);
	mysql_query($query);
}

?>
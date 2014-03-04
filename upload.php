<?php //header('Content-type: text/html; charset=UTF-8');?>

<?php
require_once 'db.php';
try{
	setup();
}
catch(Exception $e){
	echo '<span style="font-weight:bold"><a href="index.php">' . $e->getMessage() . '</a></span><br/><br/>';
}

if(isset($_POST['submit'])){
	$file_types = array('mpg','avi','flv');
	$mydir = $_POST['submit'];

	parseDirectory($mydir);
}
else{
	echo '<html><head>';
	echo '<title>Upload Movie Information</title>';
	echo '<link rel="stylesheet" type="text/css" href="./css/my.css" />';
	echo '<script type="text/javascript" src="./scripts/jquery-1.4.3.min.js"></script>';

	echo <<<SCRIPT
		<script type="text/javascript">
			$(function(){
				$('#submit').click(function(){
					$('#data').prepend('Loading...<br/><br/>');
					post_data = {};
					post_data['submit'] = $('#upload').val();
					$.ajax({
						type: 'POST',
						url: 'upload.php',
						data: post_data,
						success: function(data){
							$('#data').html(data);
						}
					});
				});
			});
		</script>
SCRIPT;

	echo '</head>';
	echo '<body>';

	echo 'Enter Directory:&nbsp;<input id="upload" type="text"></input>&nbsp;&nbsp;&nbsp;';
	echo '<button id="submit" type="button">Submit</button><br/><br/><div id="data"></div>';

	echo '</body></html>';
}

function parseDirectory($base){
	global $file_types;
	foreach(scandir($base) as $file){
		$full_filename = $base . '/' . $file;
		if(!is_dir($file)){
			$pathinfo = pathinfo($file);
			if(isset($pathinfo['extension'])){
				if(in_array($pathinfo['extension'], $file_types)){
					parseFile($full_filename);
				}
			}
		}
		elseif($file != '.' && $file != '..'){
			parseDirectory($full_filename);
		}
	}
}

function parseFile($filename){
	global $movie_table;
	ob_start();
	passthru('.\ffmpeg\ffmpeg.exe -i "'.$filename.'" 2>&1');
	$file_info = ob_get_contents();
	ob_end_clean();

	$file_not_found = preg_match('/: no such file or directory\s$/', $file_info, $matches);
	$unknown_format = preg_match('/: Unknown format\s$/', $file_info, $matches);
	if($file_not_found === 0 && $unknown_format === 0){
		preg_match('/Duration: (\d{2}):(\d{2}):(\d{2})\.(\d{2}\d?),/', $file_info, $matches);
		$duration = ((intval($matches[1])*60 + intval($matches[2]))*60 + intval($matches[3]))*1000 + intval($matches[4]);
		preg_match('/Video: (\w+),/', $file_info, $matches);
		$encoding = $matches[1];
		$md5 = md5_file($filename);
		
		$print = 'filename: ' . $filename . '<br/>duration(ms): ' . $duration . '<br/>encoding: ' . $encoding . '<br/>md5: ' . $md5 . '<br/><br/>';
		
		$query = "SELECT COUNT(*) FROM $movie_table WHERE `movie_md5`='$md5'";
		$result = mysql_fetch_row(mysql_query($query));
		if(!$result[0]) {
			$query = "INSERT INTO `$movie_table` VALUES (NULL, '$filename', '$duration', '$encoding', '$md5')";
			mysql_query($query);
			echo $print;
		}
		else{
			echo '<span style="color:#FF0000">';
			echo $print;
			echo '</span>';
		}
		
		
		if(!$result[0]){
		}
	}
	elseif($file_not_found == 1){
		echo 'no such file or directory';
	}
	elseif($unknown_format == 1){
		echo 'unknown format';
	}
	else{
		echo 'unexpected error';
	}
}

?>
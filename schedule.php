<?php header('Content-type: text/html; charset=UTF-8');?>

<?php
require_once 'functions.php';

echo '<html><head>';
echo '<title>Schedule Listing</title>';
echo '<link rel="stylesheet" type="text/css" href="./css/my.css" />';
echo '<link rel="stylesheet" type="text/css" href="jquery-ui-1.8.custom.css" />';
echo '<link rel="stylesheet" type="text/css" href="./css/timePicker.css" />';
echo '<link rel="stylesheet" type="text/css" href="./css/jquery-ui-1.8.6.custom.css" />';
echo '<script type="text/javascript" src="./scripts/jquery-1.4.3.min.js"></script>';
echo '<script type="text/javascript" src="./scripts/jquery-ui-1.8.custom.min.js"></script>';
echo StoreGetVars();
echo '<script type="text/javascript" src="./scripts/table_script.js"></script>';
echo '<script type="text/javascript">$(function(){script("playlist.php", "schedule_id");});</script>';
echo '<script type="text/javascript">$(function(){$("#date").datepicker({ dateFormat: "yy-mm-dd"  });});</script>';
echo '<script type="text/javascript" src="./scripts/jquery.timePicker.js"></script>';
echo '<script type="text/javascript" src="./scripts/jquery.cookie.js"></script>';

echo <<<ADDBUTTON
	<script type="text/javascript">
		$(function(){
			$('#add_button').click(function(){
				var date = $('#date').val();
				var time = $('#time').val();
				$.cookie("date", date);
				$.cookie("time", time);
			});
		});
	</script>
ADDBUTTON;

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

echo BuildTable($schedule_table, "`theater_id`=${_GET['theater_id']}", 'ORDER BY `schedule_start`');
echo '<br/><br/><br/>';
echo GoButton('playlist.php', 'schedule_id');
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo DeleteButton('schedule_id');
echo '<br/>';
echo AddDiv('Add Schedule Item', $schedule_table);
echo FindButton('Find Playlist', 'playlist.php', 'schedule_id', 'playlist_id');
echo updateID($schedule_table, 'schedule_id', true);

echo <<<SCRIPT
	<script type="text/javascript">
		var default_date = '2010-11-12';
		var default_time = '12:00';
		if($.cookie("date") != null){
			default_date = $.cookie("date");
			default_time = $.cookie("time");
		}
		$('input[name=schedule_start]').hide().parent().html('<div id="hidden_input"><input type="text" value="" name="schedule_start" style="display: none;"></div><input name="form_date" type="text" id="date" value="' + default_date + '" /><input name="form_time" type="text" id="time" size="10" value="' + default_time + '" />');
		$('#time').click(function(){
			$('#time').timePicker();
		});
		
		function update(){
			var input_value = $('#date').val() + ' ' + $('#time').val() + ':00';
			$('#hidden_input').html('<input type="text" value="' + input_value + '" name="schedule_start" style="display: none;">');
		};
		update();
		$('#date').blur(update);
		$('#time').blur(update);
		$('#date').change(update);
		$('#time').change(update);
	</script>
SCRIPT;

echo '</body></html>';
?>
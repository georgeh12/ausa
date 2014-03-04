<?php

$debug_mode=false;
if(isset($_GET['debug'])){
	$debug_mode=true;
}

function Home(){
	if(isset($_GET['debug'])){
		$debug = preg_replace('/[?&]debug=true/', '', $_SERVER['REQUEST_URI']);
	}
	else{
		$debug = $_SERVER['REQUEST_URI'];
		if($_SERVER['REQUEST_URI'] == $_SERVER['PHP_SELF']){
			$debug .= '?debug=true';
		}
		else{
			$debug .= '&debug=true';
		}
	}
	return <<<HOME
		<a href="./">Take me HOME!</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="$debug">Brace for Debugging</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="./upload.php">Up-to-the-load</a>
HOME;
}

function GoButton($target, $id){
	return <<<BUTTON
		<button id="go_button" type="button">gogogog</button>
		<script type="text/javascript">
			$("#go_button").click(function(){
				if($('tr.highlight td').html()){
					redirect("$target", "$id");
				}
			});
		</script>
BUTTON;
}

function DeleteButton($id){
	return <<<BUTTON
		<button id="delete_button" type="button">Delete</button>
		<script type="text/javascript">
			$("#delete_button").click(function(){
				var post_data = {};
				post_data['$id'] = $('tr.highlight td').html();
				if('$id' == 'playlist_id'){
					post_data['playlist_track'] = $('tr.highlight td')[3].innerHTML;
				}
				if($('tr.highlight td').html() != null && confirm("There's no second chances. Are you sure?")){
					$.ajax({
						type: 'POST',
						url: 'delete.php',
						data: post_data,
						success: function(data){
							window.location.href= "${_SERVER['REQUEST_URI']}";
						}
					});
				}
			});
		</script>
BUTTON;
}

function FindButton($text, $target, $id, $target_id){
	return <<<BUTTON
		<script type="text/javascript">
		$(function(){
			var id_value = $('input[name=$id]').val();
			var button = '<button id="find_button" type="button">$text</button>';
			button += '<script type="text/javascript">';
			button += '$("#find_button").click(function(){';
			button += 'window.location.href = "./' + '$target' + '?' + HTML_STRING + '$id' + '=' + id_value + '";';
			button += '});';
			button += '\</script\>';
			$('input[name=$target_id]').parent().append('&nbsp;&nbsp;&nbsp;' + button);
		});
		</script>
BUTTON;
}

function StoreGetVars($exclude=array()){
	$html = '';
	foreach($_GET as $key=>$value){
		if(!in_array($key, $exclude)){
			$html .= htmlentities($key . '=' . $value) . '&';
		}
	}
	return <<<SCRIPT
		<script type="text/javascript">
			var HTML_STRING = '$html';
		</script>
SCRIPT;
}

function BuildTable($table, $params='', $sortby=''){
	global $debug_mode, $table_strings;
	$return = '';
	
	$prefix = 'WHERE';
	$search_prefix = '';
	$search = $_POST['search'];
	if($search != ''){
		if($params != ''){
			$search_prefix = 'AND';
		}
	}
	else{
		if($params == ''){
			$prefix = '';
		}
	}
	
	if($debug_mode){
		$return .= '<form method="POST" action="' . $_SERVER['REQUEST_URI'] . '"><table>';
		$return .= '<tr><td>Input search query (eg. `theater_id` = 1337 AND `theater_name` = "Broadway")</td>';
		$return .= '<td><input type="text" name="search"></td></tr>';
		$return .= '</table><input type="submit" value="Submit" /></form>';
	}
	
	$return .= '<table>';
	$query = "DESC `$table`";
	$result = mysql_query($query);
	$return .= '<tr>';
	while($row = mysql_fetch_assoc($result)) {
		$return .= "<td>".$table_strings[$row['Field']]." (${row['Field']})</td>";
	}
	$return .= '</tr>';

	$query = "SELECT * FROM `$table` " . $prefix . " " . $params . " " . $search_prefix . " " . $search . " " . $sortby;
	
	if($debug_mode){
		echo "MySQL: $query";
	}
	
	$result = mysql_query($query);
	while($row = mysql_fetch_assoc($result)) {
		$return .= '<tr class="rows">';
		foreach ($row as $field=>$value){
			$return .= "<td>$value</td>";
		}
		$return .= '</tr>';
	}
	$return .= '</table>';
	
	return $return;
}

function AddDiv($button_text, $table){
	global $table_strings;
	$return = '<div id="the_div">';
	$return .= '<table>';
	
	$query = "DESC `$table`";
	$result = mysql_query($query);
	while($row = mysql_fetch_assoc($result)) {
		$value = '';
		$other = '';
		if(isset($_GET[$row['Field']])){
			$value = $_GET[$row['Field']];
			$other = "readonly=true";
		}
		$return .= <<<HTML
			<tr><td>${table_strings[$row['Field']]}</td><td><input type="text" name="${row['Field']}" value="$value" $other/></td></tr>
HTML;
	}
	$return .= '</table><button id="add_button" type="button"/>'.$button_text.'</button></div>';

	$return .= <<<JSCRIPT
	<script type="text/javascript">
	$(function(){
		$('#add_button').click(function(){
			var post_data = {};
			$('input').each(function(index, item){
				post_data[item['name']] = $('input[name=' + item['name'] + ']').val();
			});
			$.ajax({
				type: 'POST',
				url: 'insert.php',
				data: post_data,
				success: function(data){
					window.location.href= "${_SERVER['REQUEST_URI']}";
				}
			});
		});
	});
	</script>
JSCRIPT;

	return $return;
}

function updateID($table, $id, $readonly='false'){
	$query = "SELECT MAX($id) FROM $table";
	$max_id = mysql_fetch_row(mysql_query($query));
	$max_id = $max_id[0];
	if($readonly === true){
		$max_id += 1;
	}
	return <<<SCRIPT
		<script type="text/javascript">
			$('input[name=$id]').val('$max_id').attr('readonly',$readonly);
		</script>
SCRIPT;
}

?>
<?php
include 'config.php';
include 'opendb.php';
include 'functions.php';

$table = $_POST['table'];
unset($_POST['table']);

if($_POST['action'] == 'update'){
	$column = $_POST['column'];
	$value = $_POST['value'];
	$ID = $_POST['ID'];

	if($column =='station_id'){
		$current_id = doquery("SELECT station_id FROM gift_relationships WHERE ID=$ID;");
		$current_id = $station_id[0]->station_id;
	}
	$value = preg_match("/(_name|greeting)$/", $column) ? '"' . $value. '"' : $value;
	
	$query = "UPDATE $table SET $column = $value WHERE ID = $ID;";
	if(mysql_query($query)!==false){
		if($column =='station_id'){
			update_station_variables($ID, $current_id, $value);
		}
		echo "success";
	}else{echo "error: $query (" . implode(',',$_POST).")";}
	return;
}else if($_POST['action'] == 'insert'){
	unset($_POST['action']);

	$safevals = array();
	$newvals = array();
	foreach($_POST as $key => $val){
		$newvals[] = $val;
		$type = substr($key, strrpos($key, '_')+1);
		$key = substr($key, 0, strrpos($key, '_'));
		if($type =='char'){
			$val = ($val);
			$safevals[] = "\"$val\"";
		}else{
			$safevals[] = $val;
		}
	}
        if($table =='programs')$safevals[] = "NULL";//empty description

	$query = "INSERT INTO $table VALUES (NULL, " . implode(', ', $safevals) . ");";

	if(mysql_query($query)!==false){
		$new_id = mysql_insert_id();
		echo "<option value='$new_id' >".stripslashes($newvals[0])."</option>";
	}else{echo "error: $query (" . implode(',',$_POST).")";}
	return;
}else if( preg_match("/delete/", $_POST['action']) ){
	$row_id = $_POST['gift_id'] ? $_POST['gift_id'] : $_POST['item_id'];

	if($table =='gifts'){
		$file = doquery("SELECT thumb FROM $table WHERE ID=$row_id;");
		$file = $file[0]->thumb;
		if(is_file("../$file")){
			unlink("../$file");
		}
	}

	if($row_id){
		$query = "DELETE FROM $table WHERE ID = $row_id;";

		if(mysql_query($query)!==false){
			echo "success";
		}else{echo "error: $query (" . implode(',',$_POST).")";}
	}
	return "error: No gift id passed.";
}
?>

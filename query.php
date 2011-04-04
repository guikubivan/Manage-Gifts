<?php
include 'config.php';
include 'opendb.php';
include 'functions.php';

$table = $_POST['table'];
unset($_POST['table']);

if($table=='gift_relationships'){
	$gift_id = $_POST['gift_id'];
	$column = $_POST['column'];
	$query="SELECT program_id FROM $table WHERE ID = $gift_id;";
	$results = doquery($query);
	$col_id = $results[0]->program_id;
	
	$col_table = substr($column, 0, strrpos($column, '_')) . 's';

	if($col_table == 'levels'){
		$query="SELECT level_id, level_amount FROM $col_table WHERE station_id = $col_id;";
	}else if($col_table == 'programs'){
		$query="SELECT ID, program_name FROM $col_table WHERE ID = $col_id;";
	}

	$results = doquery($query);
	echo json_encode($results);
}else{
	$queryvars = array();
	foreach($_POST as $key => $val){
		$type = substr($key, strrpos($key, '_')+1);
		$key = substr($key, 0, strrpos($key, '_'));
		if($type =='char'){
			$val = addslashes($val);
			$queryvars[] = "$key LIKE \"$val\"";
		}else{
			$queryvars[] = "$key = $val";
		}
	}

	if($table=='programs'){
		$query="SELECT * FROM $table";
	}else{
		$query="SELECT * FROM $table";
	}
	if(sizeof($queryvars)>0){
		$query .= " WHERE " . implode(' AND ', $queryvars);
	}

	if($table=='programs'){
		$query .= " ORDER BY parent_id";
	}
	$query .= ";";
	$results = doquery($query);

	if($table=='programs'){
		$parents = array();
		foreach($results as $program){
			if($program->parent_id == 0){
				$parents[$program->ID] = array();
			}
		}

		$pnames = array();
		foreach($results as $program){
			if($program->parent_id > 0){
				$parents[$program->parent_id][] = $program->ID;
			}
			$pnames[$program->ID] = array('program_name'=> $program->program_name, 'parent_id' => $program->parent_id);
		}
		$newresults = array();
		$i = 0;
		foreach($parents as $id => $parent){
			$results[$i]->ID = $id;
			$results[$i]->program_name = $pnames[$id]['program_name'];
			$results[$i]->parent_id = $pnames[$id]['parent_id'];
			++$i;
			foreach($parent as $child_id){
				$results[$i]->ID =  $child_id;
				$results[$i]->program_name = str_repeat("-", 3) . $pnames[$child_id]['program_name'];
				$results[$i]->parent_id = $pnames[$child_id]['parent_id'];
				++$i;
			}
		}

	}
	//echo json_encode($query);
	echo json_encode($results);
}
?>

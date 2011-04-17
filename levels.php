<?php
include 'header.php';
?>

<?php
$table = basename(__FILE__);
$table = substr($table, 0, strpos($table, '.php'));

if($_POST['updatebutton']){
//  print_r($_POST);
  if(($program_id = $_POST['ID']) && ($amounts = $_POST['level_amount'])){
	$level_ids = array();
	foreach($amounts as $amount){
		$level_id = doquery("SELECT ID FROM levels WHERE level_amount = $amount;");
		$level_id = $level_id[0]->ID;
	
		if(!$level_id){
			doquery("INSERT INTO levels VALUES(NULL, $amount, '');");
			$level_id = mysql_insert_id();
		}
		$level_ids[] = $level_id;
	}
	
	foreach($level_ids as $level_id){
		$query = "INSERT INTO level_relationships VALUES(NULL, $program_id, $level_id);";
		if(mysql_query($query)===false){
			//echo "<div>error: $query (" . implode(',',$_POST).")</div>";
			//	break;
		}
	}
	$query = "DELETE FROM level_relationships WHERE program_id = $program_id && level_id NOT IN (".implode(',', $level_ids).");";
	if(mysql_query($query)===false){
			echo "<div>error: $query</div>";
	}
	echo "<div class='message'>Levels updated.</div>";
  }else{
    echo "<div>Error: not all fields were filled out.</div>\n";
  }
}




if($ID = $_GET['ID']){
	$query="SELECT p.ID, p.program_name, l.level_amount FROM programs as p LEFT JOIN level_relationships as lr ON (p.ID = lr.program_id) LEFT JOIN levels AS l ON (lr.level_id = l.ID) WHERE p.ID = $ID ORDER BY p.ID, l.level_amount;";
}else{
	$query="SELECT p.ID, p.program_name, l.level_amount FROM programs as p LEFT JOIN level_relationships as lr ON (p.ID = lr.program_id) LEFT JOIN levels AS l ON (lr.level_id = l.ID) ORDER BY p.ID, l.level_amount;";
}
$results = doquery($query);
//print_r($results);
?>

<script type='text/javascript'>

function deleteSelected(){
	$("#level_amount option:selected").remove();
}

function addLevelAmount(amount){
	amount = parseInt(amount);
	if(!isNaN(amount)){
		var n = $("#level_amount option").length;
		if(n > 0){
			var curval = 0;
			for(var i = 0; i< n; ++i){
				curval = parseInt($("#level_amount option:eq("+i+")").val());
				if(curval == amount){
					return;
				}else if(curval > amount){					
					$("#level_amount option:eq("+i+")").before("<option value='" + amount + "' >"+amount+"</option>");
					break;
				}
			}
		}
		if(n == $("#level_amount option").length){
			$('#level_amount').append("<option value='" + amount + "' >"+amount+"</option>");
		}
	}


}
</script>

<h2>Manage Levels</h2>


<?php
if($ID){
?>
<form method='post' action='' onsubmit="$('#level_amount option').attr('selected', 'selected'); return true;" />
<?php } ?>

<table>
  <tr>
    <th></th>
    <th>Program name</th>
    <th>Levels </th>
  </tr>

<?php 

for($i = 0; $i < sizeof($results); ++$i){
	$item = $results[$i];
	echo "<tr>";
	echo "<td><input type='hidden' name='ID' value=\"$item->ID\"/>";
	if(!$ID){
		echo "<a href='?ID=$item->ID' style='font-size: 14px; background-color: #DDD;padding: 3px;' >Edit</a>";
	}
	echo " </td>";
	echo "<td>$item->program_name</td>";
	echo "<td style='border-top: 20px solid white'>";
	$curId = $item->ID;
	if($ID){
?>
		<select multiple='multiple' style='float:left;width: 200px;margin-right: 5px;' id='level_amount' name='level_amount[]' size='5'>
<?php
		if($item->level_amount){
			$j = $i;
			do {
				$item = $results[$j];
				echo "<option value='$item->level_amount'>$item->level_amount</option>\n";
				++$j;
			}while( ($j < sizeof($results)) && ($curId == $results[$j]->ID) );
			$i = $j -1;
		}
?>
		</select>
		<input type='text' style='width: 80px;' /> <input onclick="if($(this).prev().val()){addLevelAmount($(this).prev().val());};" type='button' value='Add amount'/>
		<br /> <br />
		<input onclick="deleteSelected();" type='button' value='Delete Selected'/>
	<? }else{
		echo "<ul>";
		if($item->level_amount){
			$j = $i;
			do {
				$item = $results[$j];
				echo "<li> $item->level_amount</li>\n";
				++$j;
			}while( ($j < sizeof($results)) && ($curId == $results[$j]->ID) );
			$i = $j -1;
		}
		echo "</ul>";
	}?>

	</td>
  </tr>
<?php } ?>
</table>

<?php
if($ID){
?>
<input type='submit' name='updatebutton' value='Save' />
</form>
<?php } ?>

<br /> <br />
<a href='index.php' >Go back to main </a>


<?php
include 'footer.php';

?>

<?php
include 'header.php';
?>
<script type='text/javascript'>

function deleteItem(itemtable, id){
	$.post("submit.php", {table: itemtable, action:'delete', item_id: id},
		  function(response){
			if(response.indexOf('error') > -1){
				alert(response);
			}else if(response.indexOf('success') > -1){
				itemsingular = itemtable.substr(0, itemtable.length - 1);
				$("#" + itemsingular + "_" + id).remove();			
			}
	}, "text");
}
</script>

<?php
$table = basename(__FILE__);
$table = substr($table, 0, strpos($table, '.php'));

if($_POST['updatebutton']){
  if($_POST['ID'] && ($level_amount = $_POST['level_amount'])){
	$min_level_id = doquery("SELECT ID FROM levels WHERE level_amount = $level_amount;");
	$min_level_id = $min_level_id[0]->ID;
	
	if(!$min_level_id){
		doquery("INSERT INTO levels VALUES(NULL, $level_amount, '');");
		$min_level_id = mysql_insert_id();
	}

	$query = "UPDATE $table SET gift_name = \"".$_POST['gift_name']."\", gift_description = \"".$_POST['gift_description']."\", thumb = \"".$_POST['thumb']."\", min_level_id = $min_level_id WHERE ID = ". $_POST['ID'];
	if(mysql_query($query)!==false){
		echo "<div>Gift updated</div>";
	}else{echo "<div>error: $query (" . implode(',',$_POST).")</div>";}

  }else{
    echo "<div>Error: not all fields were filled out.</div>\n";
  }
}




if($ID = $_GET['ID']){
	$query="SELECT gifts.ID, gifts.gift_name, gifts.gift_description, gifts.thumb, levels.level_amount FROM ".$table.", levels WHERE gifts.min_level_id = levels.ID AND gifts.ID = $ID ORDER BY gift_name;";
}else{
	$query="SELECT gifts.ID, gifts.gift_name, gifts.gift_description, gifts.thumb, levels.level_amount FROM ".$table.", levels WHERE gifts.min_level_id = levels.ID ORDER BY gift_name;";
}
$results = doquery($query);
?>

<h2>Edit gifts</h2>

<?php
if($ID){
?>
<form method='post' action='' />
<?php } ?>

<table>
  <tr>
<?php
foreach($results as $item){
	foreach($item as $key => $val){
			echo "<th>".cleanname($key)."</th>\n";
	}
	break;
}
echo "</tr>\n";
foreach($results as $item){
	echo "<tr id='gift_$item->ID'>";
	$item_name = htmlentities($item->gift_name, ENT_QUOTES);
	foreach($item as $key => $val){
		if($key =='ID'){
			echo "<td class='id'>$val <input type='hidden' name='$key' value=\"".htmlentities($val, ENT_QUOTES)."\"/>";
			if(!$ID){
				echo "<a href='?ID=$val' style='font-size: 14px; background-color: #DDD;padding: 3px;' >Edit</a>";
				echo "<input type='button' style='display:block;margin-top: 5px;font-size: 10px;border:none;' value='Delete' onclick=\"if(confirm('Are you sure you want to delete gift &quot;".$item_name."&quot;? This will delete the gift only, along with its picture.')){deleteItem('gifts', $val)};\"/>";
			}
			echo " </td>";
		}else if($ID){
			if($key == 'gift_description'){
				echo "<td class='description'><textarea style='width: 300px;height: 100px;' name='$key'>$val</textarea></td>";
			}else{
				echo "<td>";
				echo "<input type='text' name='$key' value=\"".htmlentities($val, ENT_QUOTES)."\"/>";
				echo "</td>";
			}
		}else if($key == 'thumb'){
			echo "<td><img src=\"../$val\" /></td>";
		}else if($key == 'gift_description'){
			echo "<td class='description'>$val</td>";
		}else{
			echo "<td>$val</td>";
		}
	}
	echo "</tr>";
}
?>
  </tr>
</table>

<?php
if($ID){
?>
<input type='submit' name='updatebutton' value='Update' />
</form>
<?php } ?>

<br /> <br />
<a href='index.php' >Go back to main </a>


<?php
include 'footer.php';

?>

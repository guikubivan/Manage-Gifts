<?php
include 'header.php';

?>
<script type='text/javascript'>

function sendtoDB(select_id, myargs){
//	alert(myargs.level_amount_int);
	$.post("submit.php", myargs,
		  function(response){
			if(response.indexOf('error') > -1){
				alert(response);
			}else{
				var newopt = $(response);
				$('#' + select_id + ' option:last').before(newopt);
				newopt.attr('selected', true);
			}
	}, "text");

}



function deleteGift(gid){
	$.post("submit.php", {table: 'gift_relationships', action:'delete_gift', gift_id: gid},
		  function(response){
			if(response.indexOf('error') > -1){
				alert(response);
			}else{
				$("#gift_" + gid).remove();			
			}
	}, "text");
}

function saveFeaturedProp(rowID, val){
	var sendval = val ? 1 : 0;
	var myargs = {action:'update','table':'gift_relationships', column: 'featured', ID: rowID, value: sendval};
	$.post("submit.php", myargs,
		  function(response){
			if(response.indexOf('error') > -1){
				alert(response);
			}else{
				var yesorno = val ? 'Yes' : 'No';
				$("#"+rowID + "_featured").find('span').text(yesorno);
				$("#"+rowID + "_featured").find('span').show();
				$("#"+rowID + "_featured").find('input').hide();
			}
	}, "text");
}

function makeEditable(elementID, col_name, data){
	var rowID = elementID.substring(0,elementID.indexOf('_'));
	$('#'+ elementID).removeClass('editable');
	$('#'+ elementID).addClass('editing');
	var select = $('<select class="edit_' + col_name + '"></select>');

	for(var i=0; i< data.length; ++i){
	    var newopt = $('<option></option>');
	    jQuery.each(data[i], function(key, val) {
		if( (key=='ID') | (key=='level_id')){
			newopt.val(val);
			if(val==$("input[name='"+elementID+"']").val()){
				newopt.attr('selected', 'selected');
			}
		}else if(key.indexOf('_name') > -1 ){
			newopt.text(val);

		}
	    });
	    select.append(newopt);
	}
	$('#'+ elementID).html(select);




	var saveButton = $("<input type='button' value='Save' style='font-size: 12px' />");
	var cancelButton = $("<input type='button' value='Cancel' style='font-size: 8px;' />");

	saveButton.bind('click', function(){
		if($(select).val()){
			var myargs = {action:'update','table':'gift_relationships', column: col_name, ID: rowID, value: $(select).val()};
			//alert(select.find("option[selected]").text());
			$.post("submit.php", myargs,
				  function(response){
					if(response.indexOf('error') > -1){
						alert(response);
					}else{
						var newval = select.find("option[selected]").text();
						newval = newval.replace(/\-/g, "");
//						alert(newval);
						$("input[name='"+rowID + "_program_name']").val(newval);
						$("input[name='"+elementID+"']").val($(select).val());
						$('#'+ elementID).text(newval);
						$('#'+ elementID).addClass('editable');
						$('#'+ elementID).removeClass('editing');
					}
			}, "text");
		}

	});

	cancelButton.bind('click', function(){
		$('#'+ elementID).text($("input[name='"+rowID + "_program_name']").val());
		$('#'+ elementID).addClass('editable');
		var t=setTimeout("$('#" + elementID + "').removeClass('editing');",500)
	});

	$('#'+ elementID).append(saveButton);
	$('#'+ elementID).append(cancelButton);


}

  $(document).ready(function(){
	$(".editable").bind('click', function (e) {
		if($(this).hasClass('editing'))return;
		var elementID = $(this).attr('id');
		var ID = elementID.substring(0,elementID.indexOf('_'));
		var col_name = elementID.substring(elementID.indexOf('_')+1);

		if(col_name =='program_id'){
			$.post("query.php", {table: 'programs'},
				function(data){
					if(data.length > 0){
						makeEditable(elementID, col_name, data);
					}
				}, "json");

		}else{
			$.post("query.php", {table: 'gift_relationships', gift_id : ID, column: col_name},
				  function(data){
					makeEditable(elementID, col_name, data);
			}, "json");
		}
	});
/*

	$.post("query.php", myargs,{ table: "levels", station_id_int: $(this).val() },
	  function(data){
		var item;
		for(var i=0; i< data.length; ++i){
		    //alert(data[i].level_amount);
		    var newopt = $('<option>'+data[i].level_amount+'</option>');
		    newopt.val(data[i].level_id);
		    //var newIndex = $('#station_id').children().length - 1;
		
		    $('#level_id option:last').before(newopt);
		}
	  }, "json");

*/
	$('#program_id').change(function () {
		if($(this).val() =='other'){
			$(this).next().show('slow');
			var parentSelect = $("<select style='display:block' id='parent_id' name='parent_id'></select>");
			parentSelect.append("<option value=''>Choose Parent (optional)</option>");			
			/*
			for(var i = 0; i< $('#program_id').children().length; ++i){
				if( ($('#program_id option:eq('+i+')').val() != '') && ($('#program_id option:eq('+i+')').val() != 'other')){
					parentSelect.append("<option></option>");
					parentSelect.find("option:last").val($('#program_id option:eq('+i+')').val());
					parentSelect.find("option:last").text($('#program_id option:eq('+i+')').text());
				}
				//$('#level_id option:eq(1)').remove();
			}
			*/
			$.post("query.php", { table: "programs", parent_id_int: 0 },
			  function(data){
				var item;
				for(var i=0; i< data.length; ++i){
				    //alert(data[i].level_amount);
				    var newopt = $('<option>'+data[i].program_name+'</option>');
				    newopt.val(data[i].ID);
				    //var newIndex = $('#station_id').children().length - 1;
				
				    $('#parent_id').append(newopt);
				}
			  }, "json");


			$(this).next().find("div:first").after(parentSelect);
		}else{
			$(this).next().find("input[name='program_name']").val('');
			$("#parent_id").remove();
			$(this).next().hide('slow');
		}
	});

  });


</script>

<?php

if($_POST['newgift']){
	//print_r($_POST);
	//die();
	if( ( ($gift_name = $_POST['gift_name']) || nonempty($_POST['gift_id']) ) && nonempty($_POST['program_id']) && nonempty($_POST['min_level_id']) ){

		$gift_description = $_POST['gift_description'];

		//$station_name = $_POST['station_name'];
		//$level_amount = $_POST['level_amount'];
		//$program_name = $_POST['program_name'];
		$gift_id = nonempty($_POST['gift_id']) ? $_POST['gift_id'] : '';

		$program_id = nonempty($_POST['program_id']) ? $_POST['program_id'] : '';
		$min_level_id = nonempty($_POST['min_level_id']) ? $_POST['min_level_id'] : '';

		$featured = $_POST['featured'] ? $_POST['featured'] : 0;

		if(!$gift_id){
			$upload_url = upload_back();
			if($upload_url === false){
				error('Error uploading file');
			}
			$upload_url = str_replace("../", '', $upload_url);
			$query = "INSERT INTO gifts (gift_name, gift_description, thumb, min_level_id) VALUES (\"" . ($gift_name) . "\", \"".$gift_description."\", \"$upload_url\", $min_level_id);";
			if(mysql_query($query)!==false){
				$gift_id = mysql_insert_id();
			}else{ error($query);die();}
		}



		$query = "INSERT INTO gift_relationships (gift_id, program_id, featured) VALUES ($gift_id, $program_id, $featured);";
		if(mysql_query($query)!==false){
			echo "<div class='message'>Gift added.</div>";
		}else error($query);

	}else{
		error("Please fill out all fields.");
		//print_r($_POST);
	}
}
?>




<h2>Manage Gifts</h2>

<form method='post' action='' enctype="multipart/form-data" onsubmit="if($('#level_id').attr('disabled') || $('#program_id').attr('disabled')){return false;}else{return true;}"/>
<table>
  <tr>
<th><a href='?order=ID'>ID</a></th>
<th><a href='?order=gift_name'> Gift Name </a></th>
<th>Gift Description</th>
<th>Thumb (120x120 jpg)</th>
<th><a href='?order=level_amount'> Give Level </a></th>
<th><a href='?order=program_name'> Station/Program </a></th>

<th>Featured</th>

</tr>

<tr style='background-color: #CCC'>
<?php 


$query="SELECT * FROM gifts ORDER BY gift_name;";
$gifts = doquery($query);


?>
 <td colspan='2'>
	<select id='gift_id' name='gift_id' style='display:block;width: 200px' onchange="if($(this).val()=='other'){$(this).next().show('slow').next().show();$('#thumb').show('slow');$('#gift_description').show('slow');}else{$(this).next().val('');$(this).next().hide('slow').next().hide();$('#thumb').hide('slow');$('#gift_description').hide('slow');}">
	<option style='font-style:italic' value=''>Choose a gift</option>
	<option value='other' style='font-weight: bold'>New</option>
  <?php 
	foreach($gifts as $gift){
		echo "<option value='$gift->ID'> $gift->gift_name </option>\n";
	}
  ?>
	</select>
	<input style='display:none' type='text' name='gift_name' />
<!--<input type='button' value='cancel' style='display: none' onclick="$(this).prev().val('');$(this).prev().hide('slow');$(this).hide();$('#thumb_box').hide('slow');"/>-->


</td>
<td> <textarea id='gift_description' style='display:none' type='text' name='gift_description' /></textarea>

</td>

</td>
<td> <input style='display:none' type='file' id='thumb' name='thumb' />
<input type="hidden" name="attachement_loos" name="attachement_loos" value="<?php echo $globals['attachement'];?>"></input>
</td>

  <td>
<?php
$query="SELECT * FROM levels ORDER BY level_amount ASC;";
$levels = doquery($query);
?>
	<select id='min_level_id' name='min_level_id' style='display:block' onchange="if($(this).val()=='other'){$(this).next().show().next().show();}else{$(this).next().val('');$(this).next().hide().next().hide();}">
	<option value=''>Choose amount</option>
<?php
	foreach($levels as $level){
		echo "<option value='$level->ID'> $level->level_amount</option>\n";
	}
?>
	<option value='other'>Other</option>
	</select>
	<input name='min_level_amount' type='text'  style='display: none'/><input type='button' value='Save' style='display: none' onclick="sendtoDB('min_level_id',{'action':'insert','table':'levels', 'level_amount_int': $(this).prev().val(), 'level_name_char' : ''});$(this).prev().val('');$(this).prev().hide('slow');$(this).hide();"/>

  </td>

<?php
$query="SELECT * FROM programs ORDER BY parent_id ASC, program_name;";
$programs = doquery($query);


$parents = array();
foreach($programs as $program){
	if($program->parent_id > 0)break;
	$parents[$program->ID] = array();
}

$pnames = array();
foreach($programs as $program){
	if($program->parent_id > 0){
		$parents[$program->parent_id][] = $program->ID;
	}
	$pnames[$program->ID] = $program->program_name;
}

?>
  <td style='background-color: white'>
	<select id='program_id'   name='program_id' style='display:block' >
	<option value=''>Choose station/program</option>

  <?php 
	foreach($parents as $id => $parent){
		echo "<option style='font-weight: bold' value='$id'>" . $pnames[$id] . "</option>\n";
		foreach($parent as $child_id){
			echo "<option value='$child_id'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $pnames[$child_id] . "</option>\n";
		}
	}
  ?>
	<option value='other' style='font-style:italic'>New</option>
	</select>
	<div style='display:none;border-top: 1px solid grey;margin-top: 5px;padding-top: 5px'>
	<div> Name: <input type='text' name='program_name' id='program_name' style='width: 100px;'/></div><input type='button' value='Save' style='' onclick="sendtoDB('program_id',{'action':'insert','table':'programs','program_name_char': $('#program_name').val(), 'parent_id_int': $('#parent_id').val() ? $('#parent_id').val() : 0});$('#program_name').val('');$('#parent_id').remove();$(this).parent().hide('slow');"/>
	</div>
<!--
<input type='button' value='cancel' style='display: none' onclick="$(this).prev().val('');$(this).prev().hide('slow');$(this).hide();"/>


<input type='button'  value='save' style='display: none' onclick="var opt = $('<option>'+$(this).prev().val()+'</option>'); opt.attr('selected', 'selected'); opt.val($(this).prev().val()); $('select#station_name').append(opt);$(this).prev().hide();$(this).hide();"/>-->
  </td>

  <td style='background-color: white'>
	<input type='checkbox' name='featured' id='featured' value='1' /> Yes <br />
  </td>

  </tr>
  <tr>
  <td colspan='7' style='text-align: right;height: 60px;vertical-align: top;'>
     <input type='submit' name='newgift' value='Add new' />
     </form>
  </td>
  </tr>

<?php 
	$order = $_GET['order'] ? $_GET['order'] : 'gift_name';
	$query="SELECT gr.ID, g.ID as gift_id, g.gift_name, g.gift_description, g.thumb, l.level_name, l.level_amount, p.program_name, gr.program_id, l.ID AS level_id, gr.program_id, gr.featured FROM gift_relationships as gr LEFT JOIN programs as p ON (gr.program_id = p.ID)
		LEFT JOIN gifts as g ON (gr.gift_id = g.ID)
		LEFT JOIN levels as l ON (g.min_level_id = l.ID)
		ORDER BY $order;";
	//$query = "SELECT * FROM programs;";
	$gifts = doquery($query);
	$count = 1;
	foreach($gifts as $gift){
		echo "<tr id='gift_$gift->ID' style='background-color: #CCC'>\n";
		echo "<td  class='id' >$gift->ID";
		echo "<input type='hidden' name='".$gift->ID."_program_id' value='$gift->program_id'/>";
		echo "<input type='hidden' name='".$gift->ID."_program_name' value='$gift->program_name'/>";

		echo "</td>\n";
		echo "<td class='name'><div style='font-weight:bold;'>$gift->gift_name</div>
			<input type='button' style='font-size: 12px;' value='Delete' onclick=\"if(confirm('Are you sure you want to delete gift &quot;".htmlentities($gift->gift_name, ENT_QUOTES)."&quot;?')){deleteGift($gift->ID)};\"/>
			<a href='gifts.php?ID=$gift->gift_id' style='font-size: 12px;' >Edit</a>
		 </td>\n";
		echo "<td class='description'>$gift->gift_description</td>\n";
		echo "<td><img src='../$gift->thumb' /></td>\n";

		echo "<td id='".$gift->ID."_level_id'>$gift->level_amount</td>\n";
		echo "<td class='editable' id='".$gift->ID."_program_id' >";
		echo $gift->program_name ? $gift->program_name : '&nbsp;';
		echo "</td>\n";

		echo "<td  class='underline' id='".$gift->ID."_featured' >\n";
		echo "<span onclick=\"$(this).parent().find('input').show();$(this).parent().find('span').hide();\" style='display: block;'>";
		echo $gift->featured ? 'Yes' : 'No';
		echo "</span>";
		$checked = $gift->featured ? 'checked' : '';
		echo "<input type='checkbox' name='edit_featured' $checked style='display:none' /> <input type='button' onclick=\"saveFeaturedProp($gift->ID, $(this).prev().is(':checked'))\" value='Save' style='font-size: 12px;display:none' /><input onclick=\"$(this).parent().find('input').hide();$(this).parent().find('span').show()\" type='button' value='Cancel' style='display:none;font-size:8px;' />";
		echo "</td>\n";

		echo "</tr>\n";
		++$count;
	}
?>


  </td>
</table>


<?php
/*
$query="SHOW TABLES;";

$results = doquery($query);
echo "<h2>Edit Tables:</h2>\n";
echo "<ul>\n";
foreach($results as $key => $row){
	$table = $row->Tables_in_supportform;
	$t = cleanname($table);
	echo "  <li><a href='$table.php'>$t</a></li>\n";
}
echo "</ul>\n";
*/
// Use result
// Attempting to print $result won't allow access to information in the resource
// One of the mysql result functions must be used
// See also mysql_result(), mysql_fetch_array(), mysql_fetch_row(), etc.


// Free the resources associated with the result set
// This is done automatically at the end of the script
//mysql_free_result($result);
?>


<?php
include 'footer.php';

?>

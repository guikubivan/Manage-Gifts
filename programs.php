<?php
include 'header.php';

?>
<script type='text/javascript'>

function deleteItem(itemtable, id){
	$.post("submit.php", {table: itemtable, action:'delete', item_id: id},
		  function(response){
			if(response.indexOf('error') > -1){
				alert(response);
			}else if(response == 'success'){
				itemsingular = itemtable.substr(0, itemtable.length - 1);
				$("#" + itemsingular + "_" + id).remove();			
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
			var myargs = {action:'update','table':'programs', column: col_name, ID: rowID, value: $(select).val()};
			$.post("submit.php", myargs,
				  function(response){
					if(response.indexOf('error') > -1){
						alert(response);
					}else{
						$("input[name='"+elementID+"']").val(select.find("option[selected]").text());
						$('#'+ elementID).text(select.find("option[selected]").text());
						$('#'+ elementID).addClass('editable');
						$('#'+ elementID).removeClass('editing');
					}
			}, "text");
		}

	});

	cancelButton.bind('click', function(){
		$('#'+ elementID).text($("input[name='"+elementID+"']").val());
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
		var rowID = elementID.substring(0,elementID.indexOf('_'));
		var col_name = elementID.substring(elementID.indexOf('_')+1);

                switch(col_name){
                  case "parent_id":
			$.post("query.php", {table: 'programs', parent_id_int: 0},
				function(data){
					if(data.length > 0){
						makeEditable(elementID, col_name, data);
					}
				}, "json");
                    break;
                  case "program_name":
			$('#'+ elementID).removeClass('editable');
			$('#'+ elementID).addClass('editing');
			$(this).text('');
			var nameField = $("<input type='text' style='font-size: 12px' />");
			nameField.val($("input[name='"+elementID+"']").val());
			var saveButton = $("<input type='button' value='Update' style='font-size: 12px' />");
			var cancelButton = $("<input type='button' value='Cancel' style='font-size: 8px;' />");

			saveButton.bind('click', function(){
				if($(nameField).val()){
					var myargs = {action:'update','table':'programs', column: col_name, ID: rowID, value: $(nameField).val()};
					$.post("submit.php", myargs,
						  function(response){
							if(response.indexOf('error') > -1){
								alert(response);
							}else{
								$("input[name='"+elementID+"']").val($(nameField).val());
								$('#'+ elementID).text($(nameField).val());
								$('#'+ elementID).addClass('editable');
								$('#'+ elementID).removeClass('editing');
							}
					}, "text");
				}

			});

			cancelButton.bind('click', function(){
				$('#'+ elementID).text($("input[name='"+elementID+"']").val());
				$('#'+ elementID).addClass('editable');
				var t=setTimeout("$('#" + elementID + "').removeClass('editing');",500)
			});

			$(this).append(nameField);
			$(this).append(saveButton);
			$(this).append(cancelButton);
			
                    break;
                  case "program_greeting":
			$('#'+ elementID).removeClass('editable');
			$('#'+ elementID).addClass('editing');
			$(this).text('');
			var nameField = $("<textarea style='font-size: 12px; height:50px; width:300px;' ></textarea");
			nameField.val($("input[name='"+elementID+"']").val());
			var saveButton = $("<input type='button' value='Update' style='font-size: 12px' />");
			var cancelButton = $("<input type='button' value='Cancel' style='font-size: 8px;' />");

			saveButton.bind('click', function(){
				if($(nameField).val()){
					var myargs = {action:'update','table':'programs', column: "greeting", ID: rowID, value: $(nameField).val()};
					$.post("submit.php", myargs,
						  function(response){
							if(response.indexOf('error') > -1){
								alert(response);
							}else{
								$("input[name='"+elementID+"']").val($(nameField).val());
								$('#'+ elementID).text($(nameField).val());
								$('#'+ elementID).addClass('editable');
								$('#'+ elementID).removeClass('editing');
							}
					}, "text");
				}

			});

			cancelButton.bind('click', function(){
				$('#'+ elementID).text($("input[name='"+elementID+"']").val());
				$('#'+ elementID).addClass('editable');
				var t=setTimeout("$('#" + elementID + "').removeClass('editing');",500)
			});

			$(this).append(nameField);
			$(this).append(saveButton);
			$(this).append(cancelButton);
			
                    break;
                }

	});


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

if($_POST['newprogram']){
	//print_r($_POST);
	//die();
	if( ($program_name = $_POST['program_name']) ){	

		$parent_id = $_POST['parent_id'] ? $_POST['parent_id'] : 0;
                $greeting = $_POST['program_greeting'] ? mysql_real_escape_string($_POST['program_greeting']) : "NULL";

		$query = "INSERT INTO programs VALUES (NULL, \"$program_name\", $parent_id, '$greeting');";
		if(mysql_query($query)!==false){
			echo "<div class='message'>Program added.</div>";
		}else error($query);

	}else{
		error("Please fill the name field.");
		//print_r($_POST);
	}
}
?>




<h2>Manage programs</h2>

<form method='post' action='' />
<table>
  <tr>
<th>ID</th>
<th><a href='?order=program_name'> Program Name </a></th>
<th>Program Greeting</th>
<th>Parent </th>
</tr>

<tr >
  <td>&nbsp;</td>
  <td style='text-align: center;'>
	<input type='text' name='program_name' id='program_name'/>
  </td>
  <td>
	<textarea style="height:50px;width:300px;" name='program_greeting' id='program_greeting'></textarea>
  </td>
  <td style='text-align: center;'>
	<select id='parent_id'   name='parent_id' style='display:block' >
	<option value=''>Choose Parent (optional)</option>

  <?php 

	$query="SELECT * FROM programs WHERE parent_id = 0 ORDER BY program_name;";
	$parents = doquery($query);

	foreach($parents as $parent){
		echo "<option style='font-weight: bold' value='$parent->ID'>" . $parent->program_name . "</option>\n";
	}
  ?>
	</select>
  </td>


  </tr>
  <tr>
  <td colspan='3' style='text-align: right'>
     <input type='submit' name='newprogram' value='Add new' />
     </form>
  </td>
  </tr>

<?php 
	$order = $_GET['order'] ? $_GET['order'] : 'program_name';
	$query="SELECT * from programs;";
	//$query = "SELECT * FROM programs;";
	$programs = doquery($query);


	$pnames = array();
	foreach($programs as $program){
		$pnames[$program->ID] = $program->program_name;
	}

	foreach($programs as $program){
		echo "<tr id='program_$program->ID' >\n";
		echo "<td>$program->ID";
		echo "<input type='hidden' name='".$program->ID."_parent_id' value='".$pnames[$program->parent_id]."'/>";
		echo "<input type='hidden' name='".$program->ID."_program_name' value=\"".htmlentities($program->program_name, ENT_QUOTES)."\"/>";
                echo "<input type='hidden' name='".$program->ID."_program_greeting' value=\"".htmlentities($program->greeting, ENT_QUOTES)."\"/>";

		echo "</td>\n";
		echo "<td>
			<div style='margin-bottom: 3px;' id='".$program->ID."_program_name' class='editable'> $program->program_name</div>
			
			<input type='button' style='font-size: 10px;border:none;' value='Delete' onclick=\"if(confirm('Are you sure you want to delete program &quot;".htmlentities($program->program_name, ENT_QUOTES)."&quot;?')){deleteItem('programs', $program->ID)};\"/>
		 </td>\n";
                echo "<td>\n";
                echo "<div class='editable' id='" . $program->ID . "_program_greeting'>$program->greeting </div>";
                echo "</td>\n";
		$class = $program->parent_id == 0 ? '' : 'class="editable"';
		echo "<td $class id='".$program->ID."_parent_id'>";
		echo $program->parent_id ? $pnames[$program->parent_id] : '&nbsp;';
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

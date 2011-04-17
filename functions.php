<?php
function doquery($query){
	// Perform Query
	$result = mysql_query($query);
	// Check result
	// This shows the actual query sent to MySQL, and the error. Useful for debugging.
	if (!$result) {
	    $message  = 'Invalid query: ' . mysql_error() . "\n";
	    $message .= 'Whole query: ' . $query;
	    die($message);
	}
	$ret = array();
	if(is_resource($result)){
		while ($row = mysql_fetch_object($result)) {

			$ret[] = $row;	
		}
	}
	return $ret;
}

function update_station_variables($ID, $old_id, $new_id){
	$level_id = doquery("SELECT level_id FROM gift_relationships WHERE ID = $ID;");
	$level_id = $level_id[0]->level_id;

	$level_amount = doquery("SELECT level_amount FROM levels WHERE level_id = $level_id;");
	$level_amount = $level_amount[0]->level_amount;
	
	$nlevel_id = doquery("SELECT level_id FROM levels where level_amount = $level_amount AND station_id = $new_id;");
	$nlevel_id = $nlevel_id[0]->level_id;
	if(!$nlevel_id){
		doquery("INSERT INTO levels VALUES(NULL, $level_amount, '', $new_id);");
		$nlevel_id = mysql_insert_id();
	}
	doquery("UPDATE gift_relationships SET level_id = $nlevel_id WHERE ID = $ID;");
	/*******************************************/
	$program_id = doquery("SELECT program_id FROM gift_relationships WHERE ID = $ID;");
	$program_id = $program_id[0]->program_id;
	if($program_id != 0 ){
		$program_name = doquery("SELECT program_name FROM programs WHERE ID = $program_id;");
		$program_name = addslashes($program_name[0]->program_name);
	
		$nprogram_id = doquery("SELECT ID FROM programs where program_name LIKE \"$program_name\" AND station_id = $new_id;");
		$nprogram_id = $nprogram_id[0]->ID;
		if(!$nprogram_id){
			doquery("INSERT INTO programs VALUES(NULL, \"$program_name\", $new_id);");
			$nprogram_id = mysql_insert_id();
		}
		doquery("UPDATE gift_relationships SET program_id = $nprogram_id WHERE ID = $ID");
	}
}

function cleanname($text){
//	return ucwords(str_replace('_', ' ', $text));
	return ucfirst(str_replace('_', ' ', $text));
}

function error($out){
	echo "<div class='error'>Error: $out</div>";
}


function nonempty($val) {

	if($val =='other')return false;

	return $val ? true : false;

}
/***
    if the user do not want this image and change
    his mind he can reupload a new image and we
    will delete the last

    i have added the debug if !move_uploaded_file
    so you can verify the result with your
    directory and you can use this function to
    destroy the last upload without uploading
    again if you want too, just add a value...
***/

function upload_back() {
 global $globals;
/***
    1rst set the images dir and declare a files
    array we will have to loop the images
    directory to write a new name for our picture
***/
  $uploaddir = '../thumbs/'; $dir = opendir($uploaddir);
  $files = array();

/***
    if we are on a form who allow to reedit the
    posted vars we can save the image previously
    uploaded if the previous upload was successfull.
    so declare that value into a global var, we
    will rewrite that value in a hidden input later
    to post it again if we do not need to rewrite
    the image after the new upload and just... save
    the value...
***/

  if(!empty($_POST['attachement_loos'])) { $globals['attachement'] = $_POST['attachement_loos']; }

/***
    now verify if the file exists, just verify
    if the 1rst array is not empty. else you
    can do what you want, that form allows to
    use a multipart form, for exemple for a
    topic on a forum, and then to post an
    attachement with all our other values
***/

  if(isset($_FILES['thumb']) && !empty($_FILES['thumb']['name'])) {

	/***
	    now verify the mime, i did not find
	    something more easy than verify the
	    'image/' ty^pe. if wrong tell it!
	***/

	    if(!eregi('image/', $_FILES['thumb']['type'])) {

	      echo 'The uploaded file is not an image please upload a valid file!';

	    } else {

	/***
	    else we must loop our upload folder to find
	    the last entry the count will tell us and will
	    be used to declare the new name of the new
	    image. we do not want to rewrite a previously
	    uploaded image
	***/
	while($file = readdir($dir)) {
		array_push($files,"$file");
		// echo $file;
	}
	closedir($dir);
	/***
	    now just rewrite the name of our uploaded file
	    with the count and the extension, strrchr will
	    return us the needle for the extension
	***/
	$num_of_files = count($files);
	do{
		++$num_of_files;
		$_FILES['thumb']['name'] = $num_of_files.strrchr($_FILES['thumb']['name'], '.');
		$uploadfile = $uploaddir . basename($_FILES['thumb']['name']);
	}while(file_exists($uploadfile));
	/***
	    do same for the last uploaded file, just build
	    it if we have a previously uploaded file
	***/

		$previousToDestroy = empty($globals['attachement']) && !empty($_FILES['thumb']['name']) ? '' : $uploaddir . $files[ceil(count($files)-'1')];

	// now verify if file was successfully uploaded

	      if(!move_uploaded_file($_FILES['thumb']['tmp_name'], $uploadfile)) {
			echo '<pre>
			Your file was not uploaded please try again
			here are your debug informations:
			'.print_r($_FILES) .'
			</pre>';
	      } else {
		chmod($uploadfile, 0755);
		return $uploadfile;
	      }

	/***
	    and reset the globals vars if we maybe want to
	    reedit the form: first the new image, second
	    delete the previous....
	***/

		$globals['attachement'] = $_FILES['thumb']['name'];
		if(!empty($previousToDestroy)) { unlink($previousToDestroy); }

    }

  }
  return false;
}
?>

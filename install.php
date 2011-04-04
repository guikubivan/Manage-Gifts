<?php
include 'config.php';
include 'opendb.php';
include 'functions.php';

?>
<form action='' method='post'>
Install?<br/>
<input type='submit' name='install' value='Yes'/>

<br /><br />
Delete?
<input type='submit' name='delete' value='Yes'/>
</form>


<?php

if(!$_POST['install'] && !$_POST['delete'])die();

if($_POST['delete']){
	$query="SHOW TABLES;";
	$results = doquery($query);
	echo "<h2>Deleting tables:</h2>\n";
	echo "<ul>\n";
	foreach($results as $key => $row){
		$table = $row->Tables_in_supportform;
		
		$t = cleanname($table);
		echo "  <li>$t</li>\n";
		$query = "DROP TABLE $table;";
		mysql_query($query);
	}
	echo "</ul>\n";
	die();

}

echo "<h2>Installing:</h2>\n";
/*
$query="CREATE TABLE stations (
	ID int(2) NOT NULL auto_increment,
	station_name varchar(255) NOT NULL default '',
	PRIMARY KEY  (ID),
	KEY station_name (station_name)
);";
if(!mysql_query($query)) echo "Error when creating table\n";
*/
$query="CREATE TABLE programs (
	ID int(5) NOT NULL auto_increment,
	program_name varchar(255) NOT NULL default '',
	parent_id int(5) NOT NULL default 0,
	PRIMARY KEY (ID),
	KEY program_name (program_name),
	KEY parent_id (parent_id)
);";
if(!mysql_query($query)) echo "Error when creating table\n";

$query="CREATE TABLE levels (
	ID int(5) NOT NULL auto_increment,
	level_amount int(10) NOT NULL default 0,
	level_name varchar(255) NOT NULL default '',
	PRIMARY KEY(ID),
	KEY level_amount (level_amount),
	KEY level_name (level_name)
);";
if(!mysql_query($query)) echo "Error when creating table\n";

$query="CREATE TABLE level_relationships (
	ID int(9) NOT NULL auto_increment,
	program_id int(5) NOT NULL default 0,
	level_id int(5) NOT NULL default 0,
	PRIMARY KEY (program_id, level_id),
	KEY ID(ID)
);";
if(!mysql_query($query)) echo "Error when creating table\n";

$query="CREATE TABLE gifts (
	ID int(9) NOT NULL auto_increment,
	gift_name VARCHAR(255) NOT NULL default '',
	gift_description TEXT,
	thumb varchar(255) NOT NULL default '',
	min_level_id int(5) NOT NULL default 0,
	PRIMARY KEY (ID),
	KEY gift_name (gift_name)
);";
if(!mysql_query($query)) echo "Error when creating table\n";


$query="CREATE TABLE gift_relationships (
	ID int(9) NOT NULL auto_increment,
	gift_id int(9) NOT NULL default 0,
	program_id int(5) NOT NULL default 0,
	featured INT(1) NOT NULL default 0,
	PRIMARY KEY (gift_id, program_id),
	KEY ID(ID)
);";
if(!mysql_query($query)) echo "Error when creating table\n";

include 'closedb.php';
?>


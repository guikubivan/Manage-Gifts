<?php
include 'config.php';
include 'opendb.php';
include 'functions.php';

?>
<form action='' method='post'>
  <table>
    <tr>
      <td>
        Install?
      </td>
      <td>
        <input type='submit' name='install' value='Yes'/>
      </td>
    </tr>
    <tr>
      <td>
        Upgrade?<br/>(add "greeting" column to "programs" table)
      </td>
      <td>
        <input type='submit' name='upgrade' value='Yes'/>
      </td>
    </tr>
    <tr>
      <td>
        Delete all tables?
      </td>
      <td>
        <input type='submit' name='delete' value='Yes'/>
      </td>
    </tr>
  </table>
</form>


<?php

function upgrade_database(){
  $query = "ALTER TABLE programs ADD COLUMN greeting TEXT AFTER parent_id;";
  echo (mysql_query($query)==false) ? "ERROR." : "done.";
}

if(empty($_POST['install']) && empty($_POST['delete']) && empty($_POST['upgrade']))die();
?>
<hr/>
<?
if(!empty($_POST['delete'])) {
  $query = "SHOW TABLES;";
  $results = doquery($query);
  if(sizeof($results)==0)die("<h2>No tables to delete.</h2>");
  echo "<h2>Deleting tables:</h2>\n";
  echo "<ul>\n";
  foreach ($results as $key => $row) {
    $table = current($row);

    $t = cleanname($table);
    echo "  <li>$t...\n";
    $query = "DROP TABLE $table;";
    echo (mysql_query($query)==false) ? "ERROR." : "done.";
    echo "</li>";
  }
  echo "</ul>\n";
  die();
}

if(!empty($_POST['upgrade'])){
  echo "<p>Upgrading \"programs\" table...";
  upgrade_database();
  echo "</p>";
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
*/?>

<ul>

<?
$query="CREATE TABLE programs (
	ID int(5) NOT NULL auto_increment,
	program_name varchar(255) NOT NULL default '',
	parent_id int(5) NOT NULL default 0,
	PRIMARY KEY (ID),
	KEY program_name (program_name),
	KEY parent_id (parent_id)
);";
?>

  <li>Creating "programs" table...
    <? echo (mysql_query($query)==false) ? "ERROR." : "done.";
       upgrade_database();
    ?>
  </li>

  
<?
$query="CREATE TABLE levels (
	ID int(5) NOT NULL auto_increment,
	level_amount int(10) NOT NULL default 0,
	level_name varchar(255) NOT NULL default '',
	PRIMARY KEY(ID),
	KEY level_amount (level_amount),
	KEY level_name (level_name)
);";
?>
  <li>Creating "levels" table...
    <? echo (mysql_query($query)==false) ? "ERROR." : "done.";?>
  </li>


<?
$query="CREATE TABLE level_relationships (
	ID int(9) NOT NULL auto_increment,
	program_id int(5) NOT NULL default 0,
	level_id int(5) NOT NULL default 0,
	PRIMARY KEY (program_id, level_id),
	KEY ID(ID)
);";
?>
  <li>Creating "level_relationships" table...
    <? echo (mysql_query($query)==false) ? "ERROR." : "done.";?>
  </li>


<?
$query="CREATE TABLE gifts (
	ID int(9) NOT NULL auto_increment,
	gift_name VARCHAR(255) NOT NULL default '',
	gift_description TEXT,
	thumb varchar(255) NOT NULL default '',
	min_level_id int(5) NOT NULL default 0,
	PRIMARY KEY (ID),
	KEY gift_name (gift_name)
);";
?>
  <li>Creating "gifts" table...
    <? echo (mysql_query($query)==false) ? "ERROR." : "done.";?>
  </li>


  
<?
$query="CREATE TABLE gift_relationships (
	ID int(9) NOT NULL auto_increment,
	gift_id int(9) NOT NULL default 0,
	program_id int(5) NOT NULL default 0,
	featured INT(1) NOT NULL default 0,
	PRIMARY KEY (gift_id, program_id),
	KEY ID(ID)
);";
?>
  <li>Creating "gift_relationships" table...
    <? echo (mysql_query($query)==false) ? "ERROR." : "done.";?>
  </li>
</ul>
<?
include 'closedb.php';
?>


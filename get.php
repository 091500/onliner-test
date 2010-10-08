<?php
session_start();
require_once('config.php'); 

$connection = mysql_connect($db_host, $db_user, $db_password) or die(mysql_error());
mysql_query('SET NAMES utf8');
mysql_select_db("test", $connection);


$result=mysql_query("SELECT * FROM files WHERE files_id = '". $_GET[id] ."'")
         or die("ERROR: bad query");
$output=mysql_fetch_array($result);

header("Content-type: ". $output[file_type] ."");
//header("Content-length: $size");
header("Content-Disposition: attachment; filename=". $output[file_name] ."");

echo $output[blob_file];

?>

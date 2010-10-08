<?php
// logout
session_start();

$_SESSION[user_id] = '';
require_once('config.php'); 

header("Location: http://".$location."/test/");
?>


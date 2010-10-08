<?php
// login
session_start();
//print_r($_SESSION);
require_once('config.php'); 


//if($_SESSION[logged] != 1 ){

//print_r($_POST);

if($_POST[user_email] == '' or  $_POST[user_pwd] == ''){
	
			echo "<center>please, fill required fields</center><br>";
}
			
else{
			$connection = @mysql_connect($db_host, $db_user, $db_password) or die(mysql_error());
			mysql_query('SET NAMES utf8');
			mysql_select_db("test", $connection);
	

			$user_email = $_POST[user_email];
			$user_pwd = md5($_POST[user_pwd]);
			
			$sql = "select * from users where user_email = '$user_email' and user_pwd = '$user_pwd'";
	
			$result=mysql_query($sql, $connection);
			$db_result = mysql_fetch_array($result);
			

		
//check pwd and email in DB			
			if($db_result[user_email] == $user_email && $db_result[user_pwd] == $user_pwd){
				$_SESSION[user_id] = $db_result[user_id];
				
				header("Location: http://".$location."/test/myfiles.php");
				
				//echo "OK";
			}
					
//if no match
			else {
				echo "<center>there is no such user</center><br>";

			
			}		
					
			
}
	


?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<title>login</title>

<link type="text/css" rel="stylesheet" href="style.css">

</head>

<body>
<form action="" method="post">
<center><a href="register.php">regeister</a></center><br>

<table align="center"  border="0" cellpadding="0" cellspacing="1">
<tr>
	<td>email&nbsp;</td>
	<td><input type="text" name="user_email" size="30"></td>
</tr>
<tr>
	<td>password&nbsp;</td>
	<td><input type="password" name="user_pwd" size="30"></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" value="login"></td>
</tr>

</table>

</form>

</body>
</html>

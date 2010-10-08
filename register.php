<?php
session_start();
//print_r($_SESSION);
require_once('config.php'); 

			$connection = @mysql_connect($db_host, $db_user, $db_password) or die(mysql_error());
			mysql_query('SET NAMES utf8');
			mysql_select_db("test", $connection);


//print_r($_POST);

if($_POST[user_email] == '' or  $_POST[user_pwd] == ''){
	$output_message =  "<center>please, fill required fields</center><br>";
}
elseif(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $_POST[user_email])) {  //email check
	$output_message = "invalid email address";
}	  

elseif(strlen(trim($_POST[user_pwd])) < 8) { //pwd check
	$output_message = "minimum 8 characters in password"; 

}
	
else{
	
	
	$user_email = $_POST[user_email];
	$user_pwd= md5($_POST[user_pwd]); //for security
	
	$sql = "select count(user_email) from users where user_email = '$user_email'"; //check for duplicates
	
	$unique = (mysql_result(mysql_query($sql, $connection),0) == 0) ? true : false;
	
	//echo $unique;
	
	if($unique){

		$sql = "insert into users (user_id, user_pwd, user_email) values (null, '$user_pwd' , '$user_email')";
	
		$result=mysql_query($sql, $connection);
		if($result){
			$output_message =  "success!";
			$flag = true;

			$sql = "select user_id from users where user_email = '$user_email'";
			$new_id = mysql_result(mysql_query($sql, $connection),0); //get the user's id

			
			$_SESSION[user_id] = $new_id;
		}
		else
			$output_message =  "error: " . mysql_error();
	
	}
	else
		$output_message =  "user with same email exists";
	
}



	


?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<title>register</title>

<link type="text/css" rel="stylesheet" href="style.css">

</head>

<body>
<?php if($flag) : ?>

<table width="900" align="center" border="0" cellpadding="0">

<tr class="top_table">
<td width="50%"><a href="index.php" class="tdlink"><div>all files</div></a></td>
<td><a href="logout.php" class="tdlink"><div>logout</div></a></td>
</tr>


<tr>
<td colspan="2">
<form action="myfiles.php" enctype="multipart/form-data" method="post">
<input type="file" name="filename" > <input type="submit" value="upload"><br><br>
<input type="hidden" name="hidden">
</form>
</td>
</tr>

</table>


<?php else : ?>
<form action="" method="post">
<table align="center"  border="0" cellpadding="0" cellspacing="1">
<tr>
	<td colspan="2" align="center"><a href='index.php'>main page</a></td>
</tr>
<tr>
	<td>email&nbsp;</td>
	<td><input type="text" name="user_email" size="30"></td>
</tr>
<tr>
	<td>password&nbsp;</td>
	<td><input type="password" name="user_pwd" size="30"> min 8 chars</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" value="register"></td>
</tr>
</table>
<br>
<center><?php echo $output_message; ?></center>
</form>
<?php endif; ?>

</body>
</html>

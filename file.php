<?php
// login
session_start();
//print_r($_SESSION);
require_once('config.php'); 

$connection = mysql_connect($db_host, $db_user, $db_password) or die(mysql_error());
mysql_query('SET NAMES utf8');
mysql_select_db("test", $connection);


//==============generate file list
$sql = "select * from files where files_id = '". $_GET[id] ."'";
$result=mysql_query($sql, $connection);
		
$output_files ="";
//list($res_id, $res_fname, $res_date, , , , ,) = mysql_fetch_row($result);
list($res_id, $res_fname, $res_date, , ,$file_size, , , ,$comment_flag) = mysql_fetch_row($result);
		
$file_size = round($file_size / 1024);

$output_file .= "
<tr>
	<td><a href='get.php?id=$res_id'>$res_fname</a> ($file_size kb)</td>
	<td>$res_date</td>
</tr>
		 ";
//end generate file list		



//echo "cflag: " . $comment_flag;
if($comment_flag == 1){
//=========generate comments
$numrows = mysql_result(mysql_query("select count(comment_id) from comments"),0);



//UPDATE my_tree SET right_key = right_key + 2 WHERE right_key >= $right_key AND left_key < $right_key 


//INSERT INTO my_tree SET left_key = $right_key, right_key = $right_key + 1, level = $level + 1 

//echo "rows:" . mysql_result(mysql_query("select count(comment_id) from comments where file_id = ". $_GET[id] .""),0);

if($_POST[author] != '' && $_POST[comment_text] != ''){
//$sql="UPDATE comments SET right_key = right_key + 2, left_key = IF(left_key > $right_key, left_key + 2, left_key) WHERE right_key >= $right_key";

//check if no comments


$rkey = $_GET[r];

if($numrows != 0){
	$sql="UPDATE comments SET right_key = right_key + 2, left_key = IF(left_key > $rkey, left_key + 2, left_key) WHERE right_key >= $rkey";
	$result = mysql_query($sql);
	
	if($result = mysql_query($sql)) ; //echo "ok $result";
	else echo "error $result". mysql_error() . " $sql";

	$new_level =  $_GET[l] + 1;
	$new_rightkey = $_GET[r] + 1;

}else{

	$new_level =  1 ;
	$new_rightkey = $_GET[r] + 1;

}



$sql="INSERT INTO comments(
comment_id,
left_key,
right_key,
LEVEL ,
comment_date,
author_name,
comment_text,
file_id
)
VALUES(null, '". $_GET[r] ."', '$new_rightkey' , $new_level, now(), '". $_POST[author] ."', '". $_POST[comment_text] ."', '". $_GET[id] ."' )";



if($result = mysql_query($sql)) ; //echo "ok $result";
else echo "error $result". mysql_error() . " $sql";

$flag = 1;
}



//ouput comments
$sql="SELECT * FROM comments WHERE left_key >= 0 AND right_key <= (select max(right_key) from comments)  and file_id = ". $_GET[id] ." ORDER BY left_key";
$result = mysql_query($sql);

$output_comments="";

while(list($comment_id,$left_key,$right_key,$level ,$comment_date,$author_name,$comment_text,$file_id)=mysql_fetch_array($result)){

$level_padding = $level * 20;
$output_comments.= "<div class='comment' style='padding-left:".$level_padding."px'>$author_name: $comment_text <a href='file.php?id=". $_GET[id] ."&l=$level&r=$right_key'>reply</a></div>\n";

	if($_GET[l] == $level && $_GET[r] == $right_key){
	
		$output_comments.= "
		<form action='' method='post'>
		name: <input type='text' name='author'><br>
		comment:<br>
		<textarea name='comment_text'></textarea>
		<input type='submit' value='post'>
		</form>
		";
	
	}

}


//


	
//if(mysql_result(mysql_query("select count(comment_id) from comments where file_id = ". $_GET[id] .""),0) == 0)
$max_right = mysql_result(mysql_query("select max(right_key) from comments"),0) + 1 ;

//echo "max right: $max_right";

$output_leavecomment_link = "";

if($numrows == 0)
	$output_leavecomment_link = "<a href='file.php?id=". $_GET[id] ."&l=0&r=1'>leave comment</a><br>";
else
	$output_leavecomment_link = "<a href='file.php?id=". $_GET[id] ."&l=0&r=$max_right'>leave comment</a><br>";


if($_GET[l] == '0' && $flag != 1){
	$output_first_time = "
		<form action='' method='post'>
		name: <input type='text' name='author'><br>
		comment:<br>
		<textarea name='comment_text'></textarea>
		<input type='submit' value='post'>
		</form>
		";
		
		$flag = 0;
}


//end generate comments
}
else
	$output_comments = "comments set off by owner";



?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<title>Untitled Document</title>

<link type="text/css" rel="stylesheet" href="style.css">

</head>

<body>
<table width="900" align="center" border="0" cellpadding="0">

<tr class="top_table">
<td width="50%"><a href="index.php" class="tdlink"><div>all files</div></a></td>
<?php if($_SESSION['user_id'] != '') : ?>
<td width="50%"><a href="myfiles.php" class="tdlink"><div>my files</div></a></td>
<?php else : ?>
<td width="50%"><a href="login.php" class="tdlink"><div>login</div></a></td>
<?php endif; ?>
</tr>


<tr>
<td colspan="3">

<table width="900" align="center" class="bottom_table" border="0" cellpadding="0" cellspacing="1">
<tr class="bottom_table_title">
	<td width="*">file name</td>
	<td width="200">upload date</td>
</tr>

<?php echo $output_file; ?>

<tr>
	<td colspan="2">
	<?php
	echo $output_comments; 
	echo $output_leavecomment_link;
	echo $output_first_time; //show link if no comments
	?>
	</td>
</tr>


</table>

</td>
</tr>

</table>
</body>
</html>

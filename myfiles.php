<?php
// login
session_start();
//print_r($_SESSION);
require_once('config.php'); 

if($_SESSION[user_id] == 0) exit; //for security


$connection = mysql_connect($db_host, $db_user, $db_password) or die(mysql_error());
mysql_query('SET NAMES utf8');
mysql_select_db("test", $connection);

$current_page = isset($_GET[page]) ?  $_GET[page] : 1 ;


//===========begin file uploading
if(sizeof($_FILES) != 0){


		if(copy($_FILES["filename"]["tmp_name"], $temp_dir.$_FILES["filename"]["name"])) {

				
/*
				 echo($_FILES["filename"]["name"]);
				 echo($_FILES["filename"]["size"]);
				 echo($_FILES["filename"]["tmp_name"]);
				 echo($_FILES["filename"]["type"]);
*/			   
				$f=fopen($temp_dir.$_FILES["filename"]["name"],"rb"); 
				
				$upload = fread($f,filesize($temp_dir.$_FILES["filename"]["name"])); 
				$upload = addslashes($upload);   
				$file_name = addslashes($_FILES["filename"]["name"]);
				$file_size = $_FILES["filename"]["size"];
				$file_type = $_FILES["filename"]["type"];
				
				fclose($f); 
				unlink($temp_dir.$_FILES["filename"]["name"]);   // remove file from hdd
			   
				
				if ($_SERVER['HTTP_X_FORWARD_FOR']) {
					$ip = $_SERVER['HTTP_X_FORWARD_FOR'];
				} else {
					$ip = $_SERVER['REMOTE_ADDR'];
				}		
				
				
				$sql = "INSERT INTO files (files_id, file_name, date_value, user_id, blob_file,  file_size, user_agent, file_type, user_ip, comment_flag) 
						VALUES (
							null, 
							'$file_name', 
							NOW(), 
							'".$_SESSION[user_id]."', 
							'$upload',
							'$file_size', 
							'". $_SERVER['HTTP_USER_AGENT'] ."', 
							'$file_type', 
							'$ip',
							0
						)";
			
				$result=mysql_query($sql, $connection); //upload to DB

				if($result){
					$uploaded_id = mysql_result(mysql_query("SELECT files_id FROM files ORDER BY files_id DESC LIMIT 1", $connection),0); //get id to show the link to user
				
					//echo("ok");		
					$output_status_string = "<b>file was uploaded to server. <a href='get.php?id=$uploaded_id'>link</a> to download</b>";
					
				}	
				else
				//mysql_query($sql);
			    	$output_status_string =  "DB ERROR:" . mysql_error();


		} else 
			$output_status_string = "<b>sorry, uploading error</b>";
		   
} 
// end file upload






//==============begin delete
//print_r($_POST['del']);

if($_POST['del']){
	foreach($_POST['del'] as $k=>$v){
	
		$result = mysql_query("delete from files where files_id = $v");
	}
	
	$output_status_string = "<b>files deleted</b>";
}
//end  delete




//print_r($_POST);

//==============begin update comment rules
if($_POST[hidden] == 1){
	switch($_GET[sort]){
		//different sorting:
		case 'filename': $sql = "select * from files where user_id = '". $_SESSION[user_id] ."' 
		order by file_name limit " . ($current_page - 1) * $page_limit . ", $page_limit"; break;
		case 'date': $sql =  "select * from files where user_id = '". $_SESSION[user_id] ."' 
		order by date_value limit " . ($current_page - 1) * $page_limit . ", $page_limit"; break;
		case 'filenamedown': $sql = "select * from files where user_id = '". $_SESSION[user_id] ."' 
		order by file_name desc limit " . ($current_page - 1) * $page_limit . ", $page_limit"; break;
		case 'datedown': $sql =  "select * from files where user_id = '". $_SESSION[user_id] ."' 
		order by date_value desc limit " . ($current_page - 1) * $page_limit . ", $page_limit"; break;
	default:	$sql = "select * from files where user_id = '". $_SESSION[user_id] ."' 
	order by files_id desc limit " . ($current_page - 1) * $page_limit . ", $page_limit"; break;
	}
	
	//echo "$sql";
	$result=mysql_query($sql, $connection);
	
	//print_r($_POST[comment]);
	
	while(list($res_id, , , , , , , , , $comment_flag) = mysql_fetch_row($result)){
			
			if(count($_POST[comment]) !=0 && array_key_exists($res_id, $_POST[comment])){
				mysql_query("update files set comment_flag = 1 where files_id = $res_id");
				//echo "update files set comment_flag = 1 where files_id = $res_id<br>";
			}
			else{
			
				mysql_query("update files set comment_flag = 0  where files_id = $res_id");
				//echo "update files set comment_flag = 0 where files_id = $res_id<br>";
			}	
			
	}
}
//end  update comment rules






//========== generate files list
switch($_GET[sort]){
	//different sorting:
	case 'filename': $sql = "select * from files where user_id = '". $_SESSION[user_id] ."' 
	order by file_name limit " . ($current_page - 1) * $page_limit . ", $page_limit"; break;
	case 'date': $sql =  "select * from files where user_id = '". $_SESSION[user_id] ."' 
	order by date_value limit " . ($current_page - 1) * $page_limit . ", $page_limit"; break;
	case 'filenamedown': $sql = "select * from files where user_id = '". $_SESSION[user_id] ."' 
	order by file_name desc limit " . ($current_page - 1) * $page_limit . ", $page_limit"; break;
	case 'datedown': $sql =  "select * from files where user_id = '". $_SESSION[user_id] ."' 
	order by date_value desc limit " . ($current_page - 1) * $page_limit . ", $page_limit"; break;
default:	$sql = "select * from files where user_id = '". $_SESSION[user_id] ."' 
order by files_id desc limit " . ($current_page - 1) * $page_limit . ", $page_limit"; break;
}

//echo "$sql";
$result=mysql_query($sql, $connection);




$output_files_list = "";

while(list($res_id, $res_fname, $res_date, , ,$file_size, , , , $comment_flag) = mysql_fetch_row($result)){

	
	$file_size = round($file_size / 1024);
	
	$comment_flag = ($comment_flag == 1) ? "checked" : "" ;
		
	$output_files_list.= "
	<tr>
		<td>$res_id</td>
		<td><input type='checkbox' name='del[]' value='$res_id'></td>
		<td><input type='checkbox' name='comment[$res_id]' $comment_flag></td>
		<td><a href='file.php?id=$res_id'>$res_fname</a></td>
		<td>$file_size</td>
		<td>$res_date</td>
		<td><a href='get.php?id=$res_id'>url</a></td>
	</tr>		
 ";
};
//end generate files list





//generate naviation footer:

$output_navigation = "pages: ";
$filecount = mysql_result(mysql_query("select count(files_id) from files where user_id = '". $_SESSION[user_id] ."'", $connection), 0);
$num_pages = ceil($filecount / $page_limit);

//echo $num_pages;

for($i=1;$i<=$num_pages;$i++){
	$output_navigation.= ($i != $current_page) ? 
"<a href='myfiles.php?sort=" . $_GET[sort] . "&page=$i'>$i</a>&nbsp;" : 
"<b>$i</b>&nbsp;";
	
}
//end generate navigation footer

//begin header
$output_header = "
<tr class='bottom_table_title'>
	<td width='60'>file_id</td>
	<td width='20'>del</td>
	<td width='60''>allow<br>comment</td>
	<td width='*'>filename 
		<a href='myfiles.php?sort=filename&page=$current_page'>a-z</a> 
		<a href='myfiles.php?sort=filenamedown&page=$current_page'>z-a</a></td>
	<td>size(kb)</td>
	<td width='200'>upload date 
		<a href='myfiles.php?sort=date&page=$current_page'>1-9</a> 
		<a href='myfiles.php?sort=datedown&page=$current_page'>9-1</a></td>
	<td width='120'>url</td>
</tr>

";
//end header




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
<td><a href="logout.php" class="tdlink"><div>logout</div></a></td>
</tr>


<tr>
<td colspan="2">

<form name="delform" action="" method="post">
<input type="hidden" name="hidden" value="1">
<table width="900" align="center" class="bottom_table" border="0" cellpadding="0" cellspacing="1">

<?php if($filecount > 0) : ?>

<?php echo $output_header; ?>
<?php echo $output_files_list; ?>

<tr><td colspan="7">
<?php echo $output_navigation; ?>
</td></tr>
<? endif; ?>


</table>
</form>

<form action="" enctype="multipart/form-data" method="post">
<input type="file" name="filename" > <input type="submit" value="upload"><br><br>
<?php if($filecount > 0) : ?>
<input type="button" value="delete selected" onClick="document.forms['delform'].submit()"><br><br>
<input type="button" value="update comment rules" onClick="document.forms['delform'].submit()">
<? endif; ?>
</form>

<center><?php echo $output_status_string;?></center>

</td>
</tr>

</table>




</body>
</html>

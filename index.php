<?php
session_start();
//print_r($_SESSION);
require_once('config.php'); 

$connection = mysql_connect($db_host, $db_user, $db_password) or die(mysql_error());
mysql_query('SET NAMES utf8');
mysql_select_db("test", $connection);


$current_page = isset($_GET[page]) ?  $_GET[page] : 1 ;

switch($_GET[sort]){
	//$sql = "select * from users order by INET_ATON(ip)";
	case 'filename': $sql = "select * from files order by file_name limit " . ($current_page - 1) * $page_limit . ", $page_limit"; break;
	case 'date': $sql =  "select * from files order by date_value limit " . ($current_page - 1) * $page_limit . ", $page_limit"; break;
	case 'filenamedown': $sql = "select * from files order by file_name desc limit " . ($current_page - 1) * $page_limit . ", $page_limit"; break;
	case 'datedown': $sql =  "select * from files  order by date_value desc limit " . ($current_page - 1) * $page_limit . ", $page_limit" ; break;
default:	$sql = "select * from files order by files_id desc limit " . ($current_page - 1) * $page_limit . ", $page_limit"; break;
}

//echo $sql;

$result=mysql_query($sql, $connection);

$ouput_list = "";
		
while(list($res_id, $res_fname, $res_date, , , , ,) = mysql_fetch_row($result)){
		

$output_list .= "
<tr>
	<td><a href='file.php?id=$res_id'>$res_fname</a></td>
	<td>$res_date</td>
</tr>
		 ";
		
}


//generate naviation footer:

$output_navigation = "pages: ";

$num_pages = ceil(mysql_result(mysql_query("select count(files_id) from files", $connection), 0) / $page_limit);

//echo "np $num_pages ";

for($i=1;$i<=$num_pages;$i++){
	$output_navigation.= ($i != $current_page) ? 
"<a href='index.php?sort=" . $_GET[sort] . "&page=$i'>$i</a>&nbsp;" : 
"<b>$i</b>&nbsp;";
	
}
//end generate navigation footer

//begin header
$output_header = "
<tr class='bottom_table_title'>
	<td width='*'>filename 
		<a href='index.php?sort=filename&page=$current_page'>a-z</a> 
		<a href='index.php?sort=filenamedown&page=$current_page'>z-a</a>
	</td>
	
	<td width='200'>upload date 
		<a href='index.php?sort=date&page=$current_page'>1-9</a> 
		<a href='index.php?sort=datedown&page=$current_page'>9-1</a></td>
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
<?php if($_SESSION['user_id'] != '') : ?>
<td width="50%"><a href="myfiles.php" class="tdlink"><div>my files</div></a></td>
<?php else : ?>
<td width="50%"><a href="login.php" class="tdlink"><div>login</div></a></td>
<?php endif; ?>
</tr>


<tr>
<td colspan="3">

<table width="900" align="center" class="bottom_table" border="0" cellpadding="0" cellspacing="1">
<?php echo $output_header; ?>
<?php echo $output_list; ?>

<tr><td colspan="2">
<?php echo $output_navigation; ?>
</td></tr>
 


</table>

</td>
</tr>

</table>
</body>
</html>

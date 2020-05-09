<?php
//ini_set("display_errors",1);
include_once("../../connection/con.php");
$user_name ="diptesh";// $_POST['user_name'];
$password ="1234";// $_POST['password'];
$sql = "SELECT * FROM admin_info WHERE user_name = '$user_name' AND password = '$password'";
$result = pg_query($con,$sql);
$numRows=pg_num_rows($result);
if($numRows>0){
	//echo $sql;
	header("Location: http://172.104.177.75/live_analyzer/analyser_dev_pg/analysing_files/portal/index.php"); 
}else{
	//echo $sql;
	header("Location: http://172.104.177.75/live_analyzer/analyser_dev_pg/analysing_files/portal/login.php"); 

}
?>
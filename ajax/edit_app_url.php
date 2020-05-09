<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$sl_no = $_GET['sl_no'];
$app_name = $_GET['app_name'];
$package_name = $_GET['package_name'];
$url = $_GET['url'];
$sql = "UPDATE public.app_url SET  app_name = '$app_name', package_name = '$package_name', url = '$url' WHERE sl_no = $sl_no";
//echo $sql;
$res = pg_query($con,$sql);
$response= array(); 
if($res){
	$response['success']=1; 
	$response['msg']="update successful"; 
}else{
	$response['success']= 0 ;
}
echo json_encode ($response);
?>
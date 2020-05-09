<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$app_name = $_GET['app_name'];
$package_name = $_GET['package_name'];
$url = $_GET['url'];
$sql = "INSERT INTO public.app_url(app_name, package_name, url)VALUES ( '$app_name','$package_name','$url');";
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


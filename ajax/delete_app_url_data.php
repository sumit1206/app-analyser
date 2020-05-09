<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$sl_no = $_GET['sl_no'];
$sql = "DELETE FROM public.app_url WHERE sl_no = $sl_no";
//echo $sql;
$res = pg_query($con,$sql);
$response= array(); 
if($res){
	$response['success']=1; 
	$response['msg']="Delete successful"; 
}else{
	$response['success']= 0 ;
}
echo json_encode ($response);
?>
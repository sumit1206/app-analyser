<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$imei_no = $_POST['imei'];
$sql = "UPDATE imei_info SET is_pending='0' WHERE imei='$imei_no'";
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
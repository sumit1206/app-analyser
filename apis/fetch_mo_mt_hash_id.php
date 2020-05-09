<?php
include("../../connection/con.php");
$hash_id = "940564655";//$_GET['hash_id'];
//ini_set("display_errors",1);
$sql = "SELECT  hash_id,mo_id,mt_id, wait_time,android_id,last_active FROM call_details WHERE hash_id='$hash_id'";
$res = pg_query($con,$sql);
$response= array();
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){ 
	$response['success']=1; 
    $response['data']= array();	
	while ($row=pg_fetch_assoc($res)) {
		$details['hash_id']= $row['hash_id'];
		$details['mo_id']= $row['mo_id'];
		$details['mt_id'] = $row['mt_id'];
		$details['android_id'] = $row['android_id'];
		$details['last_active'] = $row['last_active'];
		array_push($response['data'], $details); 		
		}
	}else{
		$response['success']= 0 ;
	}
}
echo json_encode ($response);
?>
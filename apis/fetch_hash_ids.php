<?php
include("../../connection/con.php");
ini_set("display_errors",1);
$sql = "SELECT  bb_id FROM black_box_info WHERE is_pending = '0'";
$sql1 = "SELECT  imei FROM imei_info WHERE is_pending = '0' AND is_deleted = '0'";
$res = pg_query($con,$sql);
$res1 = pg_query($con,$sql1);
$response= array();
if($res && $res1){
	$response['success']=1;
	$numRows=pg_num_rows($res);
	$numRows1=pg_num_rows($res1);
	if($numRows>0 && $numRows1>0){  
    $response['data_hash_id']= array();
    $response['data_android_id']= array();	
	while ($row=pg_fetch_assoc($res)) {
			$details['bb_id']= $row['bb_id'];
		    array_push($response['data_hash_id'], $details['bb_id']);
		}	
	while ($row2=pg_fetch_assoc($res1)) {
			$details['imei']= $row2['imei'];
		    array_push($response['data_android_id'], $details['imei']);
		}
	}
}else{
	$response['success']=1;
}
echo json_encode ($response);
?>
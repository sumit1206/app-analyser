<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$sql = "SELECT imei FROM imei_info WHERE is_deleted='0' AND is_pending = '0'";
$res = pg_query($con,$sql);
$response= array();
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){ 
	$response['success']=1; 
    $response['data']= array();	
	while ($row=pg_fetch_assoc($res)){

		$details['android_id'] = $row['imei'];
		array_push($response['data'], $details); 		
		}
   }else{
		$response['success']= 0 ;
	}
}
echo json_encode ($response);
?>
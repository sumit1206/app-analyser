<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$sql = " SELECT hash_id, mo_id, mt_id, wait_time, ip_address, android_id, lat, lan, last_active,call_time FROM call_details ";
//echo $sql;
$res = pg_query($con,$sql);
$response= array();
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){ 
	    $response['success']=1; 
        $response['data']= array();	
	    while ($row=pg_fetch_assoc($res)) {
			$details['hash_id'] = $row['hash_id'];
			$details['mo_id'] = $row['mo_id'];
			$details['mt_id'] = $row['mt_id'];
			$details['wait_time'] = $row['wait_time'];
			$details['ip_address'] = $row['ip_address'];
			$details['android_id'] = $row['android_id'];
			$details['lat'] = $row['lat'];
			$details['last_active'] = $row['last_active'];
			$details['call_time'] = $row['call_time'];
		    array_push($response['data'], $details);
		}		 		
	}
}else{
		$response['success']= 0 ;
}
echo json_encode ($response);
?>
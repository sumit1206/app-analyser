<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$hash_id = $_GET['hash_id'];
$time=time();
$from_time = $time - 30;
$sql = "SELECT   hash_id, mo_id, mt_id, wait_time, ip_address, android_id, lat, lan, last_active, call_time FROM call_details WHERE  hash_id='$hash_id' ";
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
			$details['android_id'] = $row['android_id'];
			$details['mo_id'] = $row['mo_id'];
			$details['mt_id'] = $row['mt_id'];
			$details['call_time'] = $row['call_time'];
			$details['wait_time'] = $row['wait_time'];
		    array_push($response['data'], $details);
		}		 		
	}
}else{
		$response['success']= 0 ;
}
echo json_encode ($response);
?>
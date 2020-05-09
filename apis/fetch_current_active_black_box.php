<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$time=time();
$from_time = $time - 30;
$sql = "SELECT DISTINCT call_details.android_id, call_details.hash_id FROM app_analyser_data_for_plot_on_map INNER JOIN call_details ON call_details.android_id=app_analyser_data_for_plot_on_map.android_id WHERE time BETWEEN '$from_time' AND '$time'";
//echo $sql;
$res = pg_query($con,$sql);
$response= array();
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){ 
	$response['success']=1; 
    $response['data']= array();	
	while ($row=pg_fetch_assoc($res)) {
		$hash_id= $row['hash_id'];
		$android_id = $row['android_id'];
		if($hash_id != "" && $android_id != ""){
			$details['hash_id']= $row['hash_id'];
			$details['android_id'] = $row['android_id'];
			$details['lat'] = "28.65025845";
			$details['lan'] = "77.36508703";
			$details['avg_sinr'] = "11.0";
			$details['avg_rsrq'] = "-14";
			$details['avg_rsrp'] = "-108";
		    array_push($response['data'], $details);
		}
		
		 		
		}
	}else{
		$response['success']= 0 ;
	}
}
echo json_encode ($response);
?>
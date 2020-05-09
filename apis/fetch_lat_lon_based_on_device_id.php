<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$hash_id = $_GET['hash_id'];
$sql = "SELECT DISTINCT app_analyser_data_for_plot_on_map.android_id  FROM call_details INNER JOIN app_analyser_data_for_plot_on_map ON app_analyser_data_for_plot_on_map.android_id = call_details.android_id  WHERE  hash_id='$hash_id' ";
$sql1="SELECT (app_analyser_data_for_plot_on_map.rsrp),(app_analyser_data_for_plot_on_map.rsrq),(app_analyser_data_for_plot_on_map.sinr) FROM call_details INNER JOIN app_analyser_data_for_plot_on_map ON app_analyser_data_for_plot_on_map.android_id = call_details.android_id WHERE hash_id = '$hash_id'  ORDER BY app_analyser_data_for_plot_on_map.time DESC LIMIT 1";
$sql2="SELECT app_analyser_data_for_plot_on_map.lat , app_analyser_data_for_plot_on_map.lon FROM call_details INNER JOIN app_analyser_data_for_plot_on_map ON app_analyser_data_for_plot_on_map.android_id = call_details.android_id WHERE hash_id = '1688001287' ORDER BY app_analyser_data_for_plot_on_map.time DESC LIMIT 1";
//echo $sql."</br></br></br>".$sql1."</br></br></br>".$sql2;
$res = pg_query($con,$sql);
$res2 = pg_query($con,$sql1);
$res3 = pg_query($con,$sql2);
$response= array();
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){ 
	    $response['success']=1; 
        $response['data']= array();	
	    while ($row=pg_fetch_assoc($res)) {
			$row2=pg_fetch_assoc($res2);
			$row3=pg_fetch_assoc($res3);
			$details['hash_id'] = $hash_id;
			$details['android_id'] = $row['android_id'];
			$details['lat'] = $row3['lat'];
			$details['lan'] = $row3['lon'];
			$details['avg_sinr'] = $row2['sinr'];
			$details['avg_rsrq'] = $row2['rsrq'];
			$details['avg_rsrp'] = $row2['rsrp'];
		    array_push($response['data'], $details);
		}		 		
	}else{
		$response['success']= 0 ;
	}
}else{
		$response['success']= 0 ;
}
echo json_encode ($response);
?>
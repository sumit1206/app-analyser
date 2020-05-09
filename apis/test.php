<?php
include("../connection/con.php");
//ini_set("display_errors",1);
$hash_id = "1688001287";//$_GET['hash_id'];
$sql = "SELECT DISTINCT app_analyser_data_for_plot_on_map.android_id  FROM `call_details` INNER JOIN app_analyser_data_for_plot_on_map ON app_analyser_data_for_plot_on_map.android_id = call_details.android_id  WHERE  `hash_id`='$hash_id' ";
$sql1="SELECT AVG(app_analyser_data_for_plot_on_map.rsrp),AVG(app_analyser_data_for_plot_on_map.rsrq),AVG(app_analyser_data_for_plot_on_map.sinr) FROM `call_details` INNER JOIN app_analyser_data_for_plot_on_map ON app_analyser_data_for_plot_on_map.android_id = call_details.android_id WHERE hash_id = '$hash_id'";
$sql2="SELECT app_analyser_data_for_plot_on_map.lat , app_analyser_data_for_plot_on_map.lon FROM `call_details` INNER JOIN app_analyser_data_for_plot_on_map ON app_analyser_data_for_plot_on_map.android_id = call_details.android_id WHERE hash_id = '1688001287' ORDER BY app_analyser_data_for_plot_on_map.time DESC LIMIT 1";
//echo $sql;
$res = $con->query($sql);
$res2 = $con->query($sql1);
$res3 = $con->query($sql2);
$response= array();
if($res){
	$numRows=$res->num_rows;
	if($numRows>0){ 
	    $response['success']=1; 
        $response['data']= array();	
	    while ($row=$res->fetch_assoc()) {
			$row2=$res2->fetch_assoc();
			$row3=$res3->fetch_assoc();
			$details['hash_id'] = $hash_id;
			$details['android_id'] = $row['android_id'];
			$details['lat'] = $row3['lat'];
			$details['lan'] = $row3['lon'];
			$details['avg_sinr'] = $row2['AVG(app_analyser_data_for_plot_on_map.sinr)'];
			$details['avg_rsrq'] = $row2['AVG(app_analyser_data_for_plot_on_map.rsrq)'];
			$details['avg_rsrp'] = $row2['AVG(app_analyser_data_for_plot_on_map.rsrp)'];
		    array_push($response['data'], $details);
		}		 		
	}
}else{
		$response['success']= 0 ;
}
echo json_encode ($response);
?>
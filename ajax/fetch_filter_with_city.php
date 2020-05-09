<?php
date_default_timezone_set("Asia/Kolkata");
include("../../connection/con.php");
ini_set("display_errors",1);
$state = $_GET['city'] ;

$sql = "SELECT distinct os, running_app, spn from rf_data_with_buffer WHERE state = '$state' GROUP BY running_app, os, spn";
$response = array();
$os_arr = array();
$app_arr = array();
$spn_arr = array();
$resp = pg_query($con,$sql);
$response['success'] = 0;
if($resp){
	$numRows=pg_num_rows($resp);
	if($numRows>0){
		$response['success'] = 1;
		$response['os'] = array();
		$response['app'] = array();
		$response['spn'] = array();
		while ($row=pg_fetch_assoc($resp)) {
			$os = $row['os'];
			$spn = $row['spn'];
			$app = $row['running_app'];
			
			if (!in_array($os, $os_arr)){
				array_push($os_arr, $os);
				//echo $os;
			}
			if (!in_array($app, $app_arr)){
				array_push($app_arr, $app);
				//echo $app;
			}
			if (!in_array($spn, $spn_arr)){
				array_push($spn_arr, $spn);
				//echo $spn;
			}
		}
		$response['os'] = $os_arr;
		$response['app'] = $app_arr;
		$response['spn'] = $spn_arr;
	}
}
echo json_encode ($response);
?>
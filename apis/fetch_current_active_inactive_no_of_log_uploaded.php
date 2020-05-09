<?php
date_default_timezone_set("Asia/Kolkata");
include("../../connection/con.php");
//ini_set("display_errors",1);
$time=time();
$from_time = $time - 30;
$from_timestamp = $_GET['from_timestamp'];
$to_timestamp = $_GET['to_timestamp'];
if($to_timestamp == ""){
	$to_timestamp = $from_timestamp."23:59:59";
}
$from_timestamp = $from_timestamp."00:00:01";
$unx_to = strtotime($to_timestamp);
$unx_frm = strtotime($from_timestamp);
$response = array();

//SELECT DISTINCT(`session`) FROM `rf_data_with_buffer` INNER JOIN audio_details ON audio_details.session_id = rf_data_with_buffer.session WHERE 1
$sql1 ="SELECT COUNT(DISTINCT(session)) FROM rf_data_with_buffer WHERE timestamp BETWEEN '$unx_frm' AND '$unx_to'" ;
$sql2 ="SELECT COUNT( DISTINCT android_id) FROM app_analyser_data_for_plot_on_map WHERE time BETWEEN '$from_time' AND '$time'" ;
$sql3 ="SELECT COUNT( DISTINCT android_id) FROM app_analyser_data_for_plot_on_map WHERE time NOT BETWEEN '$from_time' AND '$time' " ;
//echo $sql2;
//echo $sql3;
$res_1 = pg_query($con,$sql1);
$res_2 = pg_query($con,$sql2);
$res_3 = pg_query($con,$sql3);
if($res_1){
	$numRows_1=pg_num_rows($res_1);
	if($numRows_1>0){
		//echo "came";
		while($row_1=pg_fetch_assoc($res_1)){
             $response['number_of_log_uploaded'] =  $row_1['count'];  			 
		}
	}
}
if($res_2){
	$numRows_2=pg_num_rows($res_2);
	if($numRows_2>0){
		while($row_2=pg_fetch_assoc($res_2)){
             $active_device = $response['active_device'] = $row_2['count'];  			 
		}
	}
}
if($res_3){
	$numRows_3=pg_num_rows($res_3);
	if($numRows_3>0){
		while($row_3=pg_fetch_assoc($res_3)){
             $response['inactive_device'] = $row_3['count'] - $active_device;  			 
		}
	}
}

echo json_encode ($response);
?>
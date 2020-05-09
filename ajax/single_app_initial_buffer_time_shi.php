<?php
include("../../connection/con.php");
date_default_timezone_set('Asia/Kolkata');
//ini_set("display_errors",1);
$from_timestamp =$_GET['from_timestamp'];
$to_timestamp =$_GET['to_timestamp'];
if($to_timestamp == ""){
	$to_timestamp = $from_timestamp."23:59:59";
}else{
	$to_timestamp = $to_timestamp."23:59:59";
}
$from_timestamp = $from_timestamp."00:00:01";
$application = $_GET['application'];
$unx_to = strtotime($to_timestamp) ;
$unx_frm = strtotime($from_timestamp) ;
$operator = $_GET['operator'];
$os = $_GET['operatingSystem'] ;
$city = $_GET['city'];

$response= array();
$response["details"]=array();
$max_value = 0 ;
$min_value = 0 ;
$count = 0;
$sum = 0 ;

$baseSql = "SELECT * from rf_data_with_buffer WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  running_app = '$application' AND  spn  = '$operator'  AND  wifi_state !='1' AND  wifi_state !='-' and state = '$city' and os = '$os'";
if($operator == 'WIFI'){
	$baseSql = "SELECT * from rf_data_with_buffer WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  running_app = '$application'  AND  wifi_state = '1' AND  wifi_state !='-' and state = '$city' and os = '$os'";
}
$sql = 
"SELECT Max(timestamp) as ts, Max(initial_buffr_time) as ibt from (".$baseSql.") as view1 WHERE package_load_time != '0' GROUP BY session order by Max(timestamp) ASC";

$res = pg_query($con,$sql);
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){
		$response['success'] = 1;
		while ($row=pg_fetch_assoc($res)) {
			$initialBufferTime = $row['ibt'];
			$time = date('m/d/Y H:i:s', $row['ts']/1000);
			$details['initial_buffr_time']=$initialBufferTime;
            $details['date_time']=$time;	
            array_push($response["details"], $details);
            
			if($max_value== 0 && $min_value==0){
				$min_value = $initialBufferTime;
				$max_value = $initialBufferTime;
			}
			if($max_value < $initialBufferTime){
				$max_value = $initialBufferTime;
			}
			if($min_value > $initialBufferTime){
				$min_value = $initialBufferTime;
			}
            $sum = $sum + $initialBufferTime;
            $count = $count + 1 ;						 		     
		}
		$avg = $sum / $count;
		$response['max_value'] = $max_value;
		$response['min_value'] = $min_value;
		$response['average']=$sum / $count ;
	}else{
		$response['success'] = 0;
		$response['error'] = "No data found";
	}
}else{
	$response['success'] = 0;
	$response['error'] = "Cannot execute query";
}
//$response['sql'] = $sql;
echo json_encode ($response);
?>
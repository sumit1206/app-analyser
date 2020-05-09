<?php
include("../../connection/con.php");
$from_timestamp =$_GET['from_timestamp'] ;
$to_timestamp =$_GET['to_timestamp'] ;
$operating_system = $_GET['operating_system'];
$application = $_GET['application'];
if($from_timestamp== "" || $operating_system == "" || $application == "" ){
	exit();
}

if($to_timestamp == ""){
$to_timestamp = $from_timestamp."23:59:59";
}else{
$to_timestamp = $to_timestamp."23:59:59";	
}
$from_timestamp = $from_timestamp."00:00:01";

$unx_to = strtotime($to_timestamp);
$unx_frm = strtotime($from_timestamp);
$count = 0;
//ini_set("display_errors",1);
$sql = "SELECT  wifi_state , wifi_ssid , wifi_rssi , wifi_ip , wifi_freq , wifi_link_speed  FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND   os  = '$operating_system'  AND  running_app   = '$application' AND   wifi_state  = '1' AND  status  = '1'  ORDER BY timestamp DESC  LIMIT 150 ";
if($application == 'All Application'){
	$sql = "SELECT  wifi_state , wifi_ssid , wifi_rssi , wifi_ip , wifi_freq , wifi_link_speed  FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND   os  = '$operating_system'   AND   wifi_state  = '1' AND  status  = '1'  ORDER BY timestamp DESC  LIMIT 150  ";
}
//echo $sql;
$res = pg_query($con,$sql);
$response= array();
if($res){		  
	$numRows=pg_num_rows($res);
	if($numRows>0 ){
		$response['success'] = 1;
		$response['message'] = "Successfuly data fetched.";
		$response['data'] = array();
		while ($row=pg_fetch_assoc($res) ) {
			$count=$count+1;
			$details['count'] = $count;
		    $details['wifi_state'] = $row['wifi_state'];
		    $details['wifi_ssid'] = $row['wifi_ssid'];
		    $details['wifi_rssi'] = $row['wifi_rssi'];
		    $details['wifi_ip'] = $row['wifi_ip'];
		    $details['wifi_freq'] = $row['wifi_freq'];
		    $details['wifi_link_speed'] = $row['wifi_link_speed'];
		    array_push($response['data'], $details);
		}
	}else{
		$response['success'] = 0;
		$response['message'] = "Failled to fetch data.";
	}
}else{
	    $response['success'] = 0;
		$response['message'] = "Failled to execute query.";
}
echo json_encode($response);

?> 
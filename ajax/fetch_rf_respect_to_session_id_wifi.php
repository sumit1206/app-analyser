<?php
include("../../connection/con.php");
date_default_timezone_set('Asia/Kolkata');
$session = $_GET['session'];
$response= array();
$response['details'] = array();
//$sql = "SELECT  wifi_state , wifi_ssid , wifi_rssi , wifi_ip , wifi_freq , wifi_link_speed  FROM  rf_data_with_buffer  WHERE  session  = '$session' and buffer_time != 0  ORDER BY timestamp DESC  LIMIT 150 ";
$sql = "SELECT timestamp, wifi_state , wifi_ssid , wifi_rssi , wifi_ip , wifi_freq , wifi_link_speed  FROM  rf_data_with_buffer  where status = '1' and session = '$session'  ORDER BY timestamp ASC";
$res1 = pg_query($con,$sql);
if($res1){		  
	$numRows1=pg_num_rows($res1);
	if($numRows1>0 ){
		while ($row1=pg_fetch_assoc($res1) ) {
			$details = array();
			$time = date('m/d/Y H:i:s', $row1['timestamp']/1000);
			$details['date_time'] =  $time;
			$details['wifi_state'] = $row1['wifi_state'];
			$details['wifi_ssid'] = $row1['wifi_ssid'];
			$details['wifi_rssi'] = $row1['wifi_rssi'];
			$details['wifi_ip'] = $row1['wifi_ip'];
			$details['wifi_freq'] = $row1['wifi_freq'];
			$details['wifi_link_speed'] = $row1['wifi_link_speed'];
			array_push($response['details'], $details);
		}
	}
}
echo json_encode($response);
?>
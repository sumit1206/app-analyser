<?php
include("../../connection/con.php");
date_default_timezone_set("Asia/Kolkata");
ini_set("display_errors",1);
$from_timestamp = $_GET['from_timestamp'] ;
$to_timestamp = $_GET['to_timestamp'] ;
$running_app = $_GET['running_app'] ;
$operator = $_GET['operator'] ;
$os = $_GET['operatingSystem'] ;
$city = $_GET['city'] ;
if($from_timestamp!= ""){
	if($to_timestamp == ""){
		$to_timestamp = $from_timestamp."23:59:59";
	}else{
		$to_timestamp = $to_timestamp."23:59:59";
	}
}
$from_timestamp = $from_timestamp."00:00:01";
$unx_to = strtotime($to_timestamp);
$unx_frm = strtotime($from_timestamp);
//echo $from_timestamp."</br>".$to_timestamp."</br>".$unx_frm."</br>".$unx_to."</br>".$running_app."</br>".$operator."</br>".$os."</br>".$city;
$response = array();
$where = "";
if($operator=='WIFI' && $running_app == 'All Application' ){
	$where = "state = '$city' AND os = '$os' AND  wifi_state ='1' AND  wifi_state !='-'";
}
if($operator=='WIFI' && $running_app != 'All Application' ){
	$where = "state = '$city' AND os = '$os' AND  wifi_state ='1' AND  wifi_state !='-' AND running_app = '$running_app'";
}
if($operator!='WIFI' && $running_app == 'All Application' ){
	$where = "state = '$city' AND os = '$os' AND  wifi_state !='1' AND  wifi_state !='-' AND spn = '$operator'";
}
if($operator!='WIFI' && $running_app != 'All Application' ){
	$where = "state = '$city' AND os = '$os' AND  wifi_state !='1' AND  wifi_state !='-' AND spn = '$operator' AND running_app = '$running_app'";
}
$sql = 
"select count(nob) as count, sum(nob) as totalnob, sum(bt) as totalbt, sum(playtime) as totalpt from
(select MAX(no_of_buffer_per_session) as nob, SUM(buffer_time) as bt, 
(CAST(MAX(timestamp) as bigint) - CAST(MIN(timestamp) as bigint)) as playtime
from rf_data_with_buffer
where ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND $where 
AND recording = 'true' 
group by session) as view1";

$res = pg_query($con,$sql);

if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){
		while($row=pg_fetch_assoc($res)){
             $response['total_test'] = $row['count'];
			 $totalBuffer = $row['totalnob'];
			 $response['total_buffer'] = $totalBuffer==null?0:$totalBuffer;
			 $totalBufferTime = $row['totalbt']/1000;
			 $response['total_buffer_time'] = round($totalBufferTime);
			 $totalPlayTime = $row['totalpt']/1000;
			 $response['total_play_time'] = round($totalPlayTime);
		}
	}
}
//echo "<br>".$sql."<br>";
echo json_encode($response);
//echo "<br>".$where;
/*
*/
?>
<?php
date_default_timezone_set("Asia/Kolkata");
include("../../connection/con.php");
//ini_set("display_errors",1);
$from_timestamp =$_GET['from_timestamp'] ;
$to_timestamp = $_GET['to_timestamp'] ;
if($to_timestamp == ""){
    $to_timestamp = $from_timestamp."23:59:59";
}else{
	$to_timestamp = $to_timestamp."23:59:59";
}
$from_timestamp = $from_timestamp."00:00:01";
$operating_system =$_GET['operatingSystem'];
$operator = $_GET['operator'];
$city = $_GET['city'];
$unx_to = strtotime($to_timestamp);
$unx_frm = strtotime($from_timestamp);


$sql_dis_app = "SELECT running_app, AVG(session_throughput) as session_tp, AVG( no_of_buffer_per_session ) as avg_no_bf, AVG( initial_buffr_time ) as avg_ibt FROM  public.rf_data_with_buffer WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  spn  = '$operator' AND  wifi_state !='-' AND  wifi_state !='1' AND state = '$city' AND os = '$operating_system' GROUP BY running_app";
//"SELECT DISTINCT  running_app  FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  spn  = '$operator' AND  wifi_state !='-' AND  wifi_state !='1' AND state = '$city'";
if($operator == 'WIFI'){
	$sql_dis_app = "SELECT running_app, AVG(session_throughput) as session_tp, AVG( no_of_buffer_per_session ) as avg_no_bf, AVG( initial_buffr_time ) as avg_ibt FROM  public.rf_data_with_buffer WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  wifi_state !='-'  AND  wifi_state ='1' AND state = '$city' AND os = '$operating_system' GROUP BY running_app";
	//"SELECT DISTINCT running_app  FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  wifi_state !='-'  AND  wifi_state ='1' AND state = '$city'";
}
$response= array();
$res_dis_app = pg_query($con,$sql_dis_app);
if($res_dis_app){
	$numRows=pg_num_rows($res_dis_app);
	if($numRows>0){ 
		while ($row=pg_fetch_assoc($res_dis_app)) {
			$running_app = $row['running_app'];
			
			$stp = $row['session_tp'];
			$nob = $row['avg_no_bf'];
			$ibt = $row['avg_ibt'];
			$total_point = 0;
			
			if($stp == 0 or $ibt == 0){
				continue;
			}
			//if($stp !=0 and $ibt != 0){
			$return_stp = get_session_throughput_point($stp);
			$return_nob = get_no_of_buffer_point($nob);
			$return_ibt = get_initial_buffer_time($ibt);
			$total_point = $return_stp + $return_nob + $return_ibt ;
			//}
			
			$details['running_app']=$running_app;
            $details['total_point']=$total_point;	
            array_push($response, $details);
		}
	}
}




function get_session_throughput_point($avg_stp) {			   
	if($avg_stp=="NULL"){  	 
		return 0 ;
	}else if($avg_stp==""){
		return 0;
	}else if( $avg_stp <=500){
		return 1;
	}else if($avg_stp > 500 && $avg_stp <=1000){
		return 2;
	}else if($avg_stp > 1000 && $avg_stp <=2000){
		return 3;
	}else if($avg_stp > 2000 && $avg_stp <=3000){
		return 4;
	}else if($avg_stp > 3000 && $avg_stp <=4000){
		return 5;
	}else if($avg_stp > 4000 && $avg_stp <=5000){
		return 6;
	}else if($avg_stp > 5000 && $avg_stp <=6000){
		return 7;
	}else if($avg_stp > 6000 && $avg_stp <=7000){
		return 8;
	}else if($avg_stp > 7000 && $avg_stp <=9000){
		return 9;
	}else if($avg_stp > 9000 && $avg_stp <=11000){
		return 10;
	}else if($avg_stp > 11000 && $avg_stp <=15000){
		return 11;
	}else{ 
		return 0;
	}
}
function get_no_of_buffer_point($avg_nob) {
	if($avg_nob=="NULL"){
		return 0 ;
	}else if($avg_nob==""){
		return 0;
	}else if( $avg_nob <=1){
		return 4;
	}else if($avg_nob > 1 && $avg_nob <=2){
		return 3;
	}else if($avg_nob > 2 && $avg_nob <=4){
		return 2;
	}else if($avg_nob > 4 && $avg_nob <=6){
		return 1;
	}else if($avg_nob > 6 ){
		return 0;
	}else{
		return 0;
	}
}
function get_initial_buffer_time($avg_ibt) {
    if($avg_ibt=="NULL"){
		return 0 ;
	}else if($avg_ibt==""){
		return 0;
	}else if( $avg_ibt <=100){
		return 13;
	}else if($avg_ibt > 100 && $avg_ibt <=200){
		return 12;
	}else if($avg_ibt > 200 && $avg_ibt <=300){
		return 11;
	}else if($avg_ibt > 300 && $avg_ibt <=400){
		return 10;
	}else if($avg_ibt > 400 && $avg_ibt <=500){
		return 9;
	}else if($avg_ibt > 500 && $avg_ibt <=600){
		return 8;
	}else if($avg_ibt > 600 && $avg_ibt <=700){
		return 7;
	}else if($avg_ibt > 700 && $avg_ibt <=800){
		return 6;
	}else if($avg_ibt > 800 && $avg_ibt <=900){
		return 5;
	}else if($avg_ibt > 900 && $avg_ibt <=1000){
		return 4;
	}else if($avg_ibt > 1000 && $avg_ibt <=1100){
		return 3;
	}else if($avg_ibt > 1100 && $avg_ibt <=1200){
		return 2;
	}else if( $avg_ibt > 1200){
		return 1;
	}else{
		return 0;
	}
}
echo json_encode ($response);
?>
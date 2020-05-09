<?php
date_default_timezone_set("Asia/Kolkata");
include("../../connection/con.php");
//ini_set("display_errors",1);
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
$from_timestamp = $from_timestamp."00:00:01";
//echo $from_timestamp."</br>".$to_timestamp;
$unx_to = strtotime($to_timestamp);
$unx_frm = strtotime($from_timestamp);
}
$response = array();
if($running_app== 'All Application'){
	$sql_os=" SELECT COUNT(DISTINCT  session ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  spn  ='$operator' AND  wifi_state !='1' AND  wifi_state !='-' and state = '$city' and os = '$os' ";
	//echo $sql_os ."</br>";
    $sql_bf="SELECT  SUM( buffer_time ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  spn  ='$operator' AND  wifi_state !='1' and state = '$city' and os = '$os' ";
	$sql_no_of_bu = "SELECT SUM( no_of_buffer_per_session ) FROM  rf_data_with_buffer  WHERE  no_of_buffer_per_session  != '0' AND ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')   AND  spn  ='$operator' AND  wifi_state !='1' AND  wifi_state !='-' and state = '$city' and os = '$os' ";
    $sql_ib="SELECT  MAX( initial_buffr_time ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  spn  ='$operator' AND  wifi_state !='1' AND  wifi_state !='-' and state = '$city' and os = '$os' ";
    $sql_st="SELECT  MAX( session_throughput ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  spn  ='$operator' AND  wifi_state !='1' AND  wifi_state !='-' and state = '$city' and os = '$os' ";
}else{
	$sql_os=" SELECT COUNT(DISTINCT  session ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  running_app  = '$running_app' AND  spn  ='$operator' AND  wifi_state !='1' AND  wifi_state !='-' and state = '$city' and os = '$os' ";
	//echo $sql_os ."</br>";
    $sql_bf="SELECT  SUM( buffer_time ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')  AND  running_app  = '$running_app' AND  spn  ='$operator' AND  wifi_state !='1' AND  wifi_state !='-' and state = '$city' and os = '$os' ";
	//echo $sql_bf;
	$sql_no_of_bu = "SELECT SUM( no_of_buffer_per_session ) FROM  rf_data_with_buffer  WHERE  no_of_buffer_per_session  != '0' AND ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')   AND  spn  ='$operator' AND   running_app  = '$running_app' AND  wifi_state !='1' AND  wifi_state !='-' and state = '$city' and os = '$os' ";
	//echo $sql_bf;
    $sql_ib="SELECT  MAX( initial_buffr_time ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')  AND   running_app  = '$running_app' AND  spn  ='$operator' AND  wifi_state !='1' AND  wifi_state !='-' and state = '$city' and os = '$os' ";
	//echo $sql_ib;
    $sql_st="SELECT  MAX( session_throughput ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')  AND   running_app  = '$running_app' AND  spn  ='$operator' AND  wifi_state !='1' AND  wifi_state !='-' and state = '$city' and os = '$os' ";
}
if($operator=='WIFI' && $running_app== 'All Application'){
	$sql_os=" SELECT COUNT(DISTINCT  session ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')  AND  wifi_state ='1' and state = '$city' and os = '$os' ";
	//echo $sql_os ."</br>";
    $sql_bf="SELECT  SUM( buffer_time ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')   AND  wifi_state ='1' and state = '$city' and os = '$os' ";
	//echo $sql_bf;
	$sql_no_of_bu = "SELECT SUM( no_of_buffer_per_session ) FROM  rf_data_with_buffer  WHERE  no_of_buffer_per_session  != '0' AND ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')  AND  wifi_state ='1' and state = '$city' and os = '$os' ";
	//echo $sql_bf;
    $sql_ib="SELECT  MAX( initial_buffr_time ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')   AND  wifi_state ='1' and state = '$city' and os = '$os'  ";
	//echo $sql_ib;
    $sql_st="SELECT  MAX( session_throughput ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  wifi_state ='1' and state = '$city' and os = '$os'  ";
}
if($operator=='WIFI' && $running_app != 'All Application' ){
	$sql_os=" SELECT COUNT(DISTINCT  session ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')  AND  wifi_state ='1' AND   running_app  = '$running_app' and state = '$city' and os = '$os'  ";
	//echo $sql_os ."</br>";
    $sql_bf="SELECT  SUM( buffer_time ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')   AND  wifi_state ='1' AND   running_app  = '$running_app' and state = '$city' and os = '$os'  ";
	//echo $sql_bf;
	$sql_no_of_bu = "SELECT SUM( no_of_buffer_per_session ) FROM  rf_data_with_buffer  WHERE  no_of_buffer_per_session  != '0' AND ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND   running_app  = '$running_app' AND  wifi_state ='1' and state = '$city' and os = '$os'  ";
	//echo $sql_bf;
    $sql_ib="SELECT  MAX( initial_buffr_time ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND   running_app  = '$running_app'  AND  wifi_state ='1' and state = '$city' and os = '$os'  ";
	//echo $sql_ib;
    $sql_st="SELECT  MAX( session_throughput ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  wifi_state ='1' AND   running_app  = '$running_app' and state = '$city' and os = '$os'  ";
}

//echo $sql_os."</br></br></br>".$sql_bf."</br></br></br>".$sql_no_of_bu."</br></br></br>".$sql_ib."</br></br></br>".$sql_st;
$res_os = pg_query($con,$sql_os);
$res_bf = pg_query($con,$sql_bf);
$res_nbf = pg_query($con,$sql_no_of_bu);
$res_ib = pg_query($con,$sql_ib);
$res_st = pg_query($con,$sql_st);

if($res_os){
	$numRows_os=pg_num_rows($res_os);
	if($numRows_os>0){
		$details = array();
		while($row_os=pg_fetch_assoc($res_os)){
             $response['total_number_of_test'] = $row_os['count'];  			 
		}
	}
}
if($res_bf){
	$numRows_bf=pg_num_rows($res_bf);
	if($numRows_bf>0){
		$details = array();
		while($row_bf=pg_fetch_assoc($res_bf)){
            $total_buffer_time =  $response['total_buffer_time'] = ceil($row_bf['sum']);             			 
		}
	}
}
if($res_nbf){
	$numRows_nbf=pg_num_rows($res_nbf);
	if($numRows_nbf>0){
		$details = array();
		while($row_nbf=pg_fetch_assoc($res_nbf)){
           $no_of_buffer=  $response['no_of_buffer'] = $row_nbf['sum'];             			 
		}
	}
}
if($no_of_buffer && $total_buffer_time){
	$response['average_buffer_time'] = ceil($total_buffer_time/$no_of_buffer);
}else{
	$response['average_buffer_time'] = 0;
}
if($res_ib){
	$numRows_ib=pg_num_rows($res_ib);
	if($numRows_ib>0){
		$details = array();
		while($row_ib=pg_fetch_assoc($res_ib)){
             $response['max_initial_buffer'] = ceil($row_ib['max']);             			 
		}
	}
}
if($res_st){
	$numRows_ib=pg_num_rows($res_st);
	if($numRows_ib>0){
		$details = array();
		while($row_tp=pg_fetch_assoc($res_st)){
             $response['max_session_throughput'] = ceil($row_tp['max']);             			 
		}
	}
}

echo json_encode($response);
?>
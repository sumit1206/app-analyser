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


$sql_dis_app="SELECT DISTINCT  running_app  FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  spn  = '$operator' AND  wifi_state !='-' AND  wifi_state !='1' and state = '$city'";
if($operator == 'WIFI'){
	$sql_dis_app = "SELECT DISTINCT  running_app  FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')  AND  wifi_state ='1' and state = '$city'";
}
$response= array();
$res_dis_app = pg_query($con,$sql_dis_app);
if($res_dis_app){
	$numRows=pg_num_rows($res_dis_app);
	if($numRows>0){ 
		while ($row=pg_fetch_assoc($res_dis_app)) {
			$running_app = $row['running_app'];
			//echo $running_app;
			$return_stp = session_throughput_point($city,$unx_frm,$unx_to,$operator,$operating_system,$running_app,$con);
            $return_nob = no_of_buffer_point($city,$unx_frm,$unx_to,$operator,$operating_system,$running_app,$con);
            $return_ibt = initial_buffer_time($city,$unx_frm,$unx_to,$operator,$operating_system,$running_app,$con);
            //echo $return_stp."----".$return_nob."----".$return_ibt."</br>";

			$total_point = $return_stp + $return_nob + $return_ibt ;
			$details['running_app']=$running_app;
            $details['total_point']=$total_point;	
            array_push($response, $details);
		}
	}
}




function session_throughput_point($city,$unx_frm,$unx_to,$operator,$operating_system,$application,$con) {
    $sql_stp = "SELECT AVG( session_throughput ) as avg_tp FROM  rf_data_with_buffer  WHERE  ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  spn  = '$operator' AND  os  = '$operating_system' AND  running_app   = '$application' AND  wifi_state !='-' AND  wifi_state !='1' and state = '$city'";
    if($operator == 'WIFI'){
	   $sql_stp = "SELECT AVG( session_throughput ) as avg_tp FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  os  = '$operating_system'  AND  running_app   = '$application' AND  wifi_state ='1' and state = '$city'";
    }
    //echo $sql_stp."</br></br>";
	$res_stp =pg_query($con,$sql_stp);
	if($res_stp){
	         $numRows_stp=pg_num_rows($res_stp);
	         if($numRows_stp>0){
				 while ($row_stp=pg_fetch_assoc($res_stp) ) {
					 $avg_stp = $row_stp['avg_tp'];					 
					 $avg_stp=ceil($avg_stp);
                     //echo $avg_stp;					 
				   }
				   
				   if($avg_stp=="NULL"){
				   	 
					   return 0 ;
				   }else if($avg_stp==""){

					   return 0;
				   }else if( $avg_stp <=500){
					   return 1;
				   }else if($avg_stp > 501 && $avg_stp <=1000){
					   return 2;
				   }else if($avg_stp > 1001 && $avg_stp <=2000){
					   return 3;
				   }else if($avg_stp > 2001 && $avg_stp <=3000){
					   return 4;
				   }else if($avg_stp > 3001 && $avg_stp <=4000){
					   return 5;
				   }else if($avg_stp > 4001 && $avg_stp <=5000){
					   return 6;
				   }else if($avg_stp > 5001 && $avg_stp <=6000){
					   return 7;
				   }else if($avg_stp > 6001 && $avg_stp <=7000){
					   return 8;
				   }else if($avg_stp > 7001 && $avg_stp <=9000){
					   return 9;
				   }else if($avg_stp > 9001 && $avg_stp <=11000){
					   return 10;
				   }else if($avg_stp > 11001 && $avg_stp <=15000){
					   return 11;
				   }
				}else{ 
					return 0;
				}
	}else{
		return 0 ;
	}
}
function no_of_buffer_point($city,$unx_frm,$unx_to,$operator,$operating_system,$application,$con) {
    $sql_nob = "SELECT AVG( no_of_buffer_per_session ) as avg_no_bf FROM  rf_data_with_buffer  WHERE   ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  os  = '$operating_system' AND  running_app   = '$application' AND  spn  = '$operator' AND  wifi_state !='-' AND  wifi_state !='1' and state = '$city'";
    if($operator == 'WIFI'){
	   $sql_nob = "SELECT AVG( no_of_buffer_per_session ) as avg_no_bf FROM  rf_data_with_buffer  WHERE   ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  os  = '$operating_system' AND  running_app   = '$application'   AND  wifi_state ='1' and state = '$city'";
    }
   // echo $sql_nob;
	$res_nob = pg_query($con,$sql_nob);
	if($res_nob){
	         $numRows_nob=pg_num_rows($res_nob);
	         if($numRows_nob>0){
				 while ($row_nob=pg_fetch_assoc($res_nob) ) {
					 $avg_nob = $row_nob['avg_no_bf'];					 
					 $avg_nob=ceil($avg_nob); 
				   }
				   
				   if($avg_nob=="NULL"){
					   return 0 ;
				   }else if($avg_nob==""){
					   return 0;
				   }else if( $avg_nob <=1){
					   return 4;
				   }else if($avg_nob > 1 && $avg_nob <=2){
					   return 3;
				   }else if($avg_nob > 3 && $avg_nob <=4){
					   return 2;
				   }else if($avg_nob > 5 && $avg_nob <=6){
					   return 1;
				   }else if($avg_nob > 7 ){
					   return 0;
				   }
				}else{
					return 0;
				}
	}else{
		return 0 ;
	}
}
function initial_buffer_time($city,$unx_frm,$unx_to,$operator,$operating_system,$application,$con) {
    $sql_ibt = "SELECT  AVG( initial_buffr_time ) as avg_ib FROM  rf_data_with_buffer  WHERE   initial_buffr_time  != '0' AND ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  os  = '$operating_system' AND  running_app   = '$application' AND  spn  = '$operator' AND  wifi_state !='-' AND  wifi_state !='1' and state = '$city'";
    if($operator == 'WIFI'){
	   $sql_ibt = "SELECT  AVG( initial_buffr_time ) as avg_ib FROM  rf_data_with_buffer  WHERE   initial_buffr_time  != '0' AND ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  os  = '$operating_system'  AND  running_app   = '$application'   AND  wifi_state ='1' and state = '$city'";
    }
    //echo $sql_ibt;
	$res_ibt = pg_query($con,$sql_ibt);
	if($res_ibt){
	         $numRows_ibt=pg_num_rows($res_ibt);
	         if($numRows_ibt>0){
				 while ($row_ibt=pg_fetch_assoc($res_ibt) ) {
					 $avg_ibt = $row_ibt['avg_ib'];					 
					 $avg_ibt=ceil($avg_ibt); 
				   }
				   
				   if($avg_ibt=="NULL"){
					   return 0 ;
				   }else if($avg_ibt==""){
					   return 0;
				   }else if( $avg_ibt <=100){
					   return 13;
				   }else if($avg_ibt > 101 && $avg_ibt <=200){
					   return 12;
				   }else if($avg_ibt > 201 && $avg_ibt <=300){
					   return 11;
				   }else if($avg_ibt > 301 && $avg_ibt <=400){
					   return 10;
				   }else if($avg_ibt > 401 && $avg_ibt <=500){
					   return 9;
				   }else if($avg_ibt > 501 && $avg_ibt <=600){
					   return 8;
				   }else if($avg_ibt > 601 && $avg_ibt <=700){
					   return 7;
				   }else if($avg_ibt > 701 && $avg_ibt <=800){
					   return 6;
				   }else if($avg_ibt > 801 && $avg_ibt <=900){
					   return 5;
				   }else if($avg_ibt > 901 && $avg_ibt <=1000){
					   return 4;
				   }else if($avg_ibt > 1001 && $avg_ibt <=1100){
					   return 3;
				   }else if($avg_ibt > 1101 && $avg_ibt <=1200){
					   return 2;
				   }else if( $avg_ibt <1200){
					   return 1;
				   }
				}else{
					return 0;
				}
	}else{
		return 0 ;
	}
}
echo json_encode ($response);
?>
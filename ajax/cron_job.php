<?php
include("../../connection/con.php");
date_default_timezone_set(DateTimeZone::listIdentifiers(DateTimeZone::UTC)[0]);
//ini_set("display_errors",1); //28 Feb 2020 11:11:15 PM
$date = date("d M Y");
$date2 = date("d/m/Y");
//echo $date; 
//$unx_to = strtotime($date."00:00:01");
//$unx_frm = strtotime($date."23:59:59");
$sql = "SELECT DISTINCT  running_app , os , spn ,wifi_state,state  FROM  rf_data_with_buffer  WHERE  date_time  like '$date%' and wifi_state != '1' and os != '' and spn != '' and running_app != '' ";

$sql0 = "DELETE FROM public.performance WHERE date ='$date2'";

$sql1 = "SELECT DISTINCT  MIN(timestamp) as time  FROM  rf_data_with_buffer  WHERE  date_time  like '$date%' and wifi_state != '1' and os != '' and spn != '' and running_app != '' ";
$res1 = pg_query($con,$sql1);
$res0 = pg_query($con,$sql0);
$res = pg_query($con,$sql);
//echo $sql;
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){ 
		$t_r = pg_fetch_assoc($res1);
		while ($row=pg_fetch_assoc($res)) {
			$running_app = $row['running_app'];
			$time = $t_r['time'];
			$os = $row['os'];
			$state = $row['state'];
			$spn = $row['spn'];
			$wifi_state = $row['wifi_state'];
			$stp = session_throughput_point($date ,$running_app , $os , $spn ,$con);
			$nob = no_of_buffer_point($date ,$running_app , $os , $spn ,$con);
			$ibt = initial_buffer_time($date ,$running_app , $os , $spn ,$con);
			$total_point = $stp + $nob + $ibt;
			$sq_i="INSERT INTO public.performance (app_name, point, date, operator, os, timestamp, wifi_state,state) VALUES ('$running_app', $total_point, '$date2', '$spn', '$os','$time','$wifi_state','$state');";
			$res_i = pg_query($con,$sq_i);
			
		}if($res_i){
            echo "Success for date :- [ ".$date2." ].";
		}else{
            echo "Failled.";
		}
	}
}

function session_throughput_point($date ,$running_app , $os , $spn ,$con) {
    $sql_stp = "SELECT AVG( session_throughput ) as avg_tp FROM  rf_data_with_buffer  WHERE    spn  = '$spn' AND  os  = '$os' AND  running_app   = '$running_app' AND  date_time  like '$date%' and wifi_state != '1'";
    
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
function no_of_buffer_point($date ,$running_app , $os , $spn ,$con) {
    $sql_nob = "SELECT AVG( no_of_buffer_per_session ) as avg_no_bf FROM  rf_data_with_buffer  WHERE  os  = '$os' AND  running_app   = '$running_app' AND  spn  = '$spn' AND  date_time  like '$date%' and wifi_state != '1'";
    
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
function initial_buffer_time($date ,$running_app , $os , $spn ,$con) {
    $sql_ibt = "SELECT  AVG( initial_buffr_time ) as avg_ib FROM  rf_data_with_buffer  WHERE   initial_buffr_time  != '0' AND  os  = '$os' AND  running_app   = '$running_app' AND  spn  = '$spn' AND date_time  like '$date%' and wifi_state != '1'";
    
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
?>
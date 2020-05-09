<?php
date_default_timezone_set("Asia/Kolkata");
include("../../connection/con.php");
//ini_set("display_errors",1);
$from_timestamp = $_GET['from_timestamp'] ;
$to_timestamp = $_GET['to_timestamp'] ;
$running_app =  $_GET['running_app'] ;
$operator = $_GET['operator'] ;
if($from_timestamp!= ""){
if($to_timestamp == ""){
	$to_timestamp = $from_timestamp."23:59:59";
}
$from_timestamp = $from_timestamp."00:00:01";
$unx_to = strtotime($to_timestamp);
$unx_frm = strtotime($from_timestamp);
}
$response = array();
if($running_app== 'All Application'){
	$sql_os=" SELECT COUNT(DISTINCT  session ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  spn  ='$operator'";
	$sql = "SELECT DISTINCT  running_app  FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')";
	if($operator == 'WIFI'){
	       $sql = "SELECT DISTINCT  running_app  FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  wifi_state ='1'";
	       $sql_os=" SELECT COUNT(DISTINCT  session ) FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  wifi_state ='1'";
     }
	$response= array();
    $res = pg_query($con,$sql);
	if($res){
	   $numRows=pg_num_rows($res);
	   if($numRows>0){ 
		   while ($row=pg_fetch_assoc($res)) {
		       $running_app = $row['running_app'];
		       $sql1="SELECT  DISTINCT session ,  initial_buffr_time  FROM  rf_data_with_buffer  WHERE  running_app  = '$running_app' AND ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND   initial_buffr_time  != '0'";
		       if($operator == 'WIFI'){
	                 $sql1="SELECT  DISTINCT session ,  initial_buffr_time  FROM  rf_data_with_buffer  WHERE  running_app  = '$running_app' AND ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND   initial_buffr_time  != '0' AND  wifi_state ='1'";
                }
		       $res1 = pg_query($con,$sql1);
		       if($res1){
	               $numRows=pg_num_rows($res1);
				   $total_initial_buffer = 0;
				   $total_count = 0 ;
	               if($numRows>0){ 
		                while ($row1=pg_fetch_assoc($res1)) {
						     $total_initial_buffer = $total_initial_buffer + $row1['initial_buffr_time'];
						     $total_count = $total_count + 1 ;
						 						 
						 
					        }
					    $average = $total_initial_buffer / $total_count ;
					    echo $average;
                        
			        }
		        }
		    }
	    }
	}
	
}
?>
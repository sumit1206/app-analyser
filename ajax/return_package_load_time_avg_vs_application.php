<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$from_timestamp =$_GET['from_timestamp'] ;
$to_timestamp =$_GET['to_timestamp'] ;
if($to_timestamp == ""){
	$to_timestamp = $from_timestamp."23:59:59";
}else{
	$to_timestamp = $to_timestamp."23:59:59";
}
$from_timestamp = $from_timestamp."00:00:01";
$unx_to = strtotime($to_timestamp) ;
$unx_frm = strtotime($from_timestamp) ;
$operator = $_GET['operator'];
$os = $_GET['operatingSystem'] ;
$city = $_GET['city'] ;
$sql = "SELECT DISTINCT  running_app  FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  wifi_state  !='1' AND  wifi_state !='-'  AND  spn  = '$operator' and state = '$city' and os = '$os'";
if($operator == "WIFI"){
	$sql = "SELECT DISTINCT  running_app  FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  wifi_state  = '1' and state = '$city' and os = '$os'";
}
$response= array();
$res = pg_query($con,$sql);
//echo $sql;
$response= array();
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){ 
		while ($row=pg_fetch_assoc($res)) {
		  $running_app = $row['running_app'];
		  $sql1="SELECT DISTINCT session ,  package_load_time  FROM  rf_data_with_buffer  WHERE  running_app  = '$running_app' AND ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND   package_load_time  != '0' AND  wifi_state  !='1' AND  wifi_state !='-'  AND  spn  = '$operator' and state = '$city' and os = '$os'";
		  if($operator == "WIFI"){
		  	$sql1 = "SELECT DISTINCT session ,  package_load_time  FROM  rf_data_with_buffer  WHERE  running_app  = '$running_app' AND ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND   package_load_time  != '0' AND  wifi_state  = '1' and state = '$city' and os = '$os'"; 
		  }
		  $res1 = pg_query($con,$sql1);
		  if($res1){
	            $numRows=pg_num_rows($res1);
				$total_load_time = 0;
				$total_count = 0 ; 
	            if($numRows>0){ 
		             while ($row1=pg_fetch_assoc($res1)) {
						 $total_load_time = $total_load_time + $row1['package_load_time'];
						 $total_count = $total_count + 1 ;
						 						 
						 
					 }
					 $average = $total_load_time / $total_count ;
					 $details['running_app']=$running_app;
                     $details['average']=$average;	
                     array_push($response, $details);
			    }else{
				     $details['running_app']=$running_app;
                     $details['average']=0;	
                     array_push($response, $details);
			        }
		    }
        }
	}
}
echo json_encode ($response);
?>
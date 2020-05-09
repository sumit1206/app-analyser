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
$application =$_GET['application'];
$unx_to = strtotime($to_timestamp);
$unx_frm = strtotime($from_timestamp);
$operator = $_GET['operator'];
$os = $_GET['operatingSystem'] ;
$city = $_GET['city'] ;
$max_value = 0 ;
$min_value = 0 ;
$count = 0;
$sum = 0 ;

$sql = "SELECT DISTINCT  session  FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  running_app  = '$application'  AND  spn  = '$operator'  AND  wifi_state !='1' AND  wifi_state !='-'  and state = '$city' and os = '$os'";
if($operator == 'WIFI'){
	$sql = "SELECT DISTINCT  session  FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  running_app  = '$application'  AND  wifi_state ='1'  and state = '$city' and os = '$os'";
}
//echo $sql;
$res = pg_query($con,$sql);
$response= array();
$response["details"]=array();
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){ 
		while ($row=pg_fetch_assoc($res)) {
		  $session = $row['session'];
		  $sql1="SELECT  no_of_buffer_per_session  , SUBSTRING( date_time ,12,9) FROM  rf_data_with_buffer  WHERE  session ='$session' AND  no_of_buffer_per_session  = (SELECT MAX( no_of_buffer_per_session ) FROM  rf_data_with_buffer  WHERE  session ='$session' ) LIMIT 1";
		  $res1 = pg_query($con,$sql1);
		  if($res1){
	            $numRows=pg_num_rows($res1);
	            if($numRows>0){ 
		             while ($row1=pg_fetch_assoc($res1)) {
						 if($max_value== 0 && $min_value==0){
							 $min_value = $row1['no_of_buffer_per_session'];
							 $max_value = $row1['no_of_buffer_per_session'];
						 }
                         $details['no_of_buffer_per_session']=$row1['no_of_buffer_per_session'];
                         $details['date_time']=$row1['substring'];	
                         array_push($response["details"], $details);
                         if($max_value<$row1['no_of_buffer_per_session']){
							 $max_value = $row1['no_of_buffer_per_session'];
						 }
						 if($min_value>$row1['no_of_buffer_per_session']){
							 $min_value = $row1['no_of_buffer_per_session'];
						 }
                         $sum = $sum + 	$row1['no_of_buffer_per_session'];
                         $count = $count + 1 ;						 
						 
					 }
					 $response['max_value'] = $max_value;
					 $response['min_value'] = $min_value;
					 $response['average']=$sum / $count ;
			    }
		    }
        }
	}
}
echo json_encode ($response);
?>
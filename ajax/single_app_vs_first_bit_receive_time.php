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
$application = $_GET['application'];
$unx_to = strtotime($to_timestamp) ;
$unx_frm = strtotime($from_timestamp) ;
$operator = $_GET['operator'];
$os = $_GET['operatingSystem'] ;
$city = $_GET['city'] ;
$sql = "SELECT DISTINCT  session  FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  running_app  ='$application' AND  spn  = '$operator'  AND  wifi_state !='1' AND  wifi_state !='-' and state = '$city' and os = '$os'";
if($operator == 'WIFI'){
	$sql = "SELECT DISTINCT  session  FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND  running_app  = '$application'  AND  wifi_state ='1' and state = '$city' and os = '$os' ";
}
$max_value = 0 ;
$min_value = 0 ;
$count = 0;
$sum = 0 ;
$response= array();
$response["details"]=array();
$res = pg_query($con,$sql);
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){ 
		while ($row=pg_fetch_assoc($res)) {
		  $session = $row['session'];
		  $sql1="SELECT  package_load_time  ,SUBSTRING( date_time ,12,9) FROM  rf_data_with_buffer  WHERE  session  = '$session' AND  package_load_time  = (SELECT MAX( package_load_time ) FROM  rf_data_with_buffer  WHERE  session  = '$session' ) AND  spn  = '$operator'  AND  wifi_state !='1' AND  wifi_state !='-' LIMIT 1";
		  if($operator == 'WIFI'){
	            $sql1="SELECT  package_load_time  ,SUBSTRING( date_time ,12,9) FROM  rf_data_with_buffer  WHERE  session  = '$session' AND  package_load_time  = (SELECT MAX( package_load_time ) FROM  rf_data_with_buffer  WHERE  session  = '$session' ) AND  wifi_state ='1' LIMIT 1";
          }
          //echo $sql1;
		  $res1 = pg_query($con,$sql1);
		  if($res1){
	            $numRows=pg_num_rows($res1); 
	            if($numRows>0){ 
		             while ($row1=pg_fetch_assoc($res1)) {
						 if($max_value== 0 && $min_value==0){
							 $min_value = $row1['package_load_time'];
							 $max_value = $row1['package_load_time'];
						 }
					     $details['package_load_time']=$row1['package_load_time'];
                         $details['date_time']=$row1['substring'];	
                         array_push($response["details"], $details);
                         if($max_value<$row1['package_load_time']){
							 $max_value = $row1['package_load_time'];
						 }
						 if($min_value>$row1['package_load_time']){
							 $min_value = $row1['package_load_time'];
						 }
                         $sum = $sum + 	$row1['package_load_time'];
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
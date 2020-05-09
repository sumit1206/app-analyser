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
$unx_to = strtotime($to_timestamp);
$unx_frm = strtotime($from_timestamp);
$operator = $_GET['operator'];
$os = $_GET['operatingSystem'] ;
$city = $_GET['city'] ;
$sql = "SELECT running_app, AVG( session_throughput ) as avg_st FROM  rf_data_with_buffer WHERE package_load_time != '0' AND ( timestamp  BETWEEN '$unx_frm' AND '$unx_to') AND wifi_state  !='1' AND wifi_state !='-'  AND spn  = '$operator' and state = '$city' and os = '$os' AND session_throughput != 0 GROUP BY running_app";
if($operator == 'WIFI'){
	$sql = "SELECT running_app, AVG( session_throughput ) as avg_st FROM  rf_data_with_buffer WHERE package_load_time != '0' AND ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')  AND  wifi_state ='1' and state = '$city' and os = '$os' AND session_throughput != 0 GROUP BY running_app";

}
$res = pg_query($con,$sql);
$response= array();
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){ 
		while ($row=pg_fetch_assoc($res)) {
			//$running_app = $row['running_app'];
			$details['running_app'] = $row['running_app'];
            $details['average'] = $row['avg_st'];	
            array_push($response, $details);					 
        }
	}
}
//$response['query'] = $sql;
echo json_encode ($response);
?>
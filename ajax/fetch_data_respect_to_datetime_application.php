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
$session_concat = "";
$from_timestamp = $from_timestamp."00:00:01";
$application =$_GET['application'];
$operator = $_GET['operator'];
$os = $_GET['operatingSystem'] ;
$city = $_GET['city'] ;
$unx_to = strtotime($to_timestamp) ; 
$unx_frm = strtotime($from_timestamp) ;
// GETTING ALL SESSION BETWEEN TWO TIMESTAMP WHERE RUNNING APPLICATION MATCHES
$sql = "SELECT DISTINCT session FROM rf_data_with_buffer WHERE (timestamp BETWEEN '$unx_frm' AND '$unx_to') AND running_app ='$application' AND spn = '$operator' AND wifi_state!='1' AND wifi_state!='-' and state = '$city' and os = '$os'";
if($operator == 'WIFI'){
	$sql = "SELECT DISTINCT session FROM rf_data_with_buffer WHERE (timestamp BETWEEN '$unx_frm' AND '$unx_to') AND running_app = '$application'  AND wifi_state='1' and state = '$city' and os = '$os'";
}
$res = pg_query($con,$sql);
$response= array();
$response["details"]=array();
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){ 
		while ($row=pg_fetch_assoc($res)) {
		  $session = $row['session'];
		  $session_concat = $session_concat."'".$session."',";
		  // GETTING SUM OF BUFFER TIME AND TIME FOR EACH PERTICULAR BUFFER
          $sql2 = "SELECT MAX(session_throughput) FROM rf_data_with_buffer WHERE session = '$session'";
		  $sql3 = "SELECT SUBSTRING(date_time,12,9) FROM rf_data_with_buffer WHERE session = '$session'  LIMIT 1";
		  $res2 = pg_query($con,$sql2);
		  $res3 = pg_query($con,$sql3);
		  if($res2){
		        $numRows2=pg_num_rows($res2);
				if($numRows2>0){
					while ($row2=pg_fetch_assoc($res2)) {
                           $max_session_through_put = $row2['max'];						
					}
				}
		  }
		  if($res3){
	            $numRows3=pg_num_rows($res3);
				if($numRows3>0){
					 while ($row3=pg_fetch_assoc($res3)) {
                           $time = $row3['substring'];						
					}  
				}
		  }
		  $details['session_throughput']=$max_session_through_put;
          $details['date_time']=$time;
		  array_push($response["details"], $details);
		  
        }
		$session_concat  = substr($session_concat, 0, -1);
		$sql4 = "SELECT MAX(session_throughput) , MIN(session_throughput) , AVG(session_throughput) FROM rf_data_with_buffer WHERE session IN ($session_concat) ";
		//echo $sql4;
		$res4 = pg_query($con,$sql4);
		if($res4){
	            $numRows4=pg_num_rows($res4);
				if($numRows4>0){
					 while ($row4=pg_fetch_assoc($res4)) {
                           $response['max_value'] = ceil($row4['max']);
					       $response['min_value'] = ceil($row4['min']);
					       $response['average']   = ceil($row4['avg']);						
					}  
				}
		  }
		
	}
}
echo json_encode ($response);
?>
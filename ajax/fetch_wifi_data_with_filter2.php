<?php
include("../../connection/con.php");
date_default_timezone_set('Asia/Kolkata');
ini_set("display_errors",1);
$from_timestamp =$_GET['from_timestamp'] ;
$count = 0;
$to_timestamp =$_GET['to_timestamp'] ;
$os = $_GET['operating_system'];
$application = $_GET['application'];
$city = $_GET['city'] ;
if($from_timestamp== "" || $os == "" || $application == "" ){
	exit();
}

if($to_timestamp == ""){
$to_timestamp = $from_timestamp."23:59:59";
}else{
$to_timestamp = $to_timestamp."23:59:59";	
}
$from_timestamp = $from_timestamp."00:00:01";

$unx_to = strtotime($to_timestamp);
$unx_frm = strtotime($from_timestamp);
$sql= "SELECT  MAX(total_bits_received) as total_bits_received , session,MAX(timestamp) as maxt, MIN(timestamp) as mint,MAX( initial_buffr_time ) as max_initial_buffer ,MAX( timestamp ) as MAX_time,MAX( percentage ) as max_percentage,SUM( buffer_time ) as total_buffer_time,MAX( no_of_buffer_per_session ) as no_of_buffer_per_session,MAX( first_bit_recieve_time ) as first_bit_recieve_time, MAX( session_throughput ) as max_session_tp,MAX( running_app ) as running_app,MAX( package_load_time ) as max_package_load_time FROM  rf_data_with_buffer  WHERE  package_load_time != '0' AND ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')  AND  running_app  = '$application'  AND  wifi_state ='1' and state = '$city' and os = '$os' group by session order by MAX_time desc";
if($application == 'All Application'){
	     $sql= "SELECT MAX(total_bits_received) as total_bits_received , session,MAX(timestamp) as maxt, MIN(timestamp) as mint, MAX( initial_buffr_time ) as max_initial_buffer ,MAX( timestamp ) as MAX_time,MAX( percentage ) as max_percentage,SUM( buffer_time ) as total_buffer_time,MAX( no_of_buffer_per_session ) as no_of_buffer_per_session,MAX( first_bit_recieve_time ) as first_bit_recieve_time, MAX( session_throughput ) as max_session_tp,MAX( running_app ) as running_app,MAX( package_load_time ) as max_package_load_time FROM  rf_data_with_buffer  WHERE   package_load_time != '0' AND ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')  AND  wifi_state ='1' and state = '$city' and os = '$os' group by session order by MAX_time desc";
 }
 //echo $sql;
$res = pg_query($con,$sql);
$response= array();
$response['details'] = array();
$details = array();
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){
		while ($row1=pg_fetch_assoc($res) ) {
			            $maxt = $row1['maxt'];
			            $mint = $row1['mint'];
           			    if(($mint < ($unx_frm * 1000) + 60000) || ($maxt > ($unx_to * 1000) - 60000)){
				              continue;
			            }
                        $count+=1;
                        $details['count'] = $count;
                        $details['total_bits_received'] = $row1['total_bits_received'];
					    $details['initial_buffr_time'] = $row1['max_initial_buffer'];
					    $time = date('m/d/Y H:i:s', $row1['max_time']/1000);
						$details['date_time'] =  $time;
					    $details['percentage'] = $row1['max_percentage'];
					    $details['total_buffer_time'] = $row1['total_buffer_time'];
					    $details['no_of_buffer_per_session'] = $row1['no_of_buffer_per_session'];
					    $details['first_bit_recieve_time'] = $row1['first_bit_recieve_time'];
					    $details['session_throughput'] = $row1['max_session_tp'];
					    $details['package_load_time'] = $row1['max_package_load_time'];
					    $details['running_app'] = $row1['running_app'];
					    $details['session'] = $row1['session'];
					    array_push($response['details'], $details);
					}
    }
}


echo json_encode($response);

?>
<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$from_timestamp =$_GET['from_timestamp'] ;
$count = 0;
$to_timestamp =$_GET['to_timestamp'] ;
$operating_system = $_GET['operating_system'];
$application = $_GET['application'];
if($from_timestamp== "" || $operating_system == "" || $application == "" ){
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
$sql = "SELECT   DISTINCT  session  FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')  AND  running_app  = '$application'  AND  wifi_state ='1' ";
if($application == 'All Application'){
	     $sql = "SELECT   DISTINCT  session  FROM  rf_data_with_buffer  WHERE ( timestamp  BETWEEN '$unx_frm' AND '$unx_to')  AND  wifi_state ='1' ";
 }
// echo $sql;
$res = pg_query($con,$sql);
$response= array();
$response['details'] = array();
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){
		while ($row=pg_fetch_assoc($res)) {
		     $count = $count +1;
		     $session = $row['session'];
	         $sql1 = "SELECT MAX( initial_buffr_time ) as max_initial_buffer ,MAX( date_time ) as max_timestamp,MAX( percentage ) as max_percentage,SUM( buffer_time ) as total_buffer_time,MAX( no_of_buffer_per_session ) as no_of_buffer_per_session,MAX( first_bit_recieve_time ) as first_bit_recieve_time, MAX( session_throughput ) as max_session_tp,MAX( running_app ) as running_app,MAX( package_load_time ) as max_package_load_time FROM  rf_data_with_buffer  WHERE  session ='$session'  LIMIT 1";
		     $res1 = pg_query($con,$sql1);
			 //echo $sql1;
             if($res1){		  
	              $numRows1=pg_num_rows($res1);
	              if($numRows1>0 ){
	              	    while ($row1=pg_fetch_assoc($res1) ) {
	              	      $details = array();
				 	      $details['count']=$count+1;
					      $details['initial_buffr_time'] = $row1['max_initial_buffer'];
					      $details['date_time'] = $row1['max_timestamp'];
					      $details['percentage'] = $row1['max_percentage'];
					      $details['total_buffer_time'] = $row1['total_buffer_time'];
					      $details['no_of_buffer_per_session'] = $row1['no_of_buffer_per_session'];
					      $details['first_bit_recieve_time'] = $row1['first_bit_recieve_time'];
					      $details['session_throughput'] = $row1['max_session_tp'];
					      $details['package_load_time'] = $row1['max_package_load_time'];
					      $details['running_app'] = $row1['running_app'];
					      $details['session'] = $session;
					      $details['count'] = $count;
					      array_push($response['details'], $details);
					}

	               }
	            }
	        }

	}
}
echo json_encode($response);
/*
$card="";
$len_ar = count($response);
for($i=0 ;$i<$len_ar;$i++){
	$card.= 	   
	'<tr>
	    <td>'.$response[$i]['count'].'</td>
		<td>'.$response[$i]['date_time'].'</td>
		<td>'.$response[$i]['running_app'].'</td>
		<td>'.$response[$i]['session'].'</td>
		<td>'.$response[$i]['session_throughput'].'</td>
		<td>'.$response[$i]['initial_buffr_time'].'</td>
		<td>'.$response[$i]['no_of_buffer_per_session'].'</td>
		<td>'.$response[$i]['total_buffer_time'].'</td>
		<td>'.$response[$i]['package_load_time'].'</td>
		<td>'.$response[$i]['percentage'].'</td>
      </tr>';
}
echo $card;
*/

?>
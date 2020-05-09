<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$from_timestamp =$_GET['from_timestamp'] ;
$count = 0;
$to_timestamp =$_GET['to_timestamp'] ;
$operating_system = $_GET['operating_system'];
$operator = $_GET['operator'];
$application = $_GET['application'];
//$page_no = $_GET['page_no'];
if($from_timestamp== "" || $operating_system == "" || $operator == "" || $application == "" ){
	exit();
}
//if($page_no == '' || $page_no == 0){

//}

if($to_timestamp == ""){
$to_timestamp = $from_timestamp."23:59:59";
}else{
$to_timestamp = $to_timestamp."23:59:59";	
}
$from_timestamp = $from_timestamp."00:00:01";

$unx_to = strtotime($to_timestamp);
$unx_frm = strtotime($from_timestamp);
$sql = "SELECT   DISTINCT session FROM rf_data_with_buffer WHERE (timestamp BETWEEN '$unx_frm' AND '$unx_to') AND spn ='$operator' AND running_app = '$application'  AND  wifi_state!='-'";
//$sql0 = "SELECT   COUNT(DISTINCT session) FROM rf_data_with_buffer WHERE (timestamp BETWEEN '$unx_frm' AND '$unx_to') AND spn ='$operator' AND running_app = '$application'   AND wifi_state!='-' ";
if($application == 'All Application'){
	     $sql = "SELECT   DISTINCT session FROM rf_data_with_buffer WHERE (timestamp BETWEEN '$unx_frm' AND '$unx_to') AND spn ='$operator'   AND wifi_state!='-' ";
	    // $sql0 = "SELECT   COUNT(DISTINCT session) FROM rf_data_with_buffer WHERE (timestamp BETWEEN '$unx_frm' AND '$unx_to') AND spn ='$operator'  AND wifi_state!='-' ";
 }
 //echo $sql;
 //echo "</br></br></br></br></br>";
// echo $sql0;
$res = pg_query($con,$sql);
//$res0 = pg_query($con,$sql0);
$response= array();
//$row0 = pg_fetch_assoc($res0);
$response['details'] = array();
$details = array();
//$response['no_of_page']=ceil($row0['count']/25);
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){
		while ($row=pg_fetch_assoc($res)) {
		     $count = $count +1;
		     $session = $row['session'];
	         $sql1 = "SELECT MAX(initial_buffr_time) as MAX_initial_buffr_time, MAX(date_time)as MAX_time, MAX(percentage)as MAX_percentage,SUM(buffer_time) as total_buffer_time,MAX(no_of_buffer_per_session) as MAX_no_of_buffer,MAX(first_bit_recieve_time)as MAX_first_bit_recieve_time ,MAX(session_throughput)as MAX_session_tp,MAX(running_app) as running_app,MAX(package_load_time) as MAX_package_load_time  FROM rf_data_with_buffer WHERE session='$session'  LIMIT 1";
	         //echo $sql1;
		     $res1 = pg_query($con,$sql1);
             if($res1){	

	              $numRows1=pg_num_rows($res1);
	              if($numRows1>0 ){
	              	

	              	
	              	    while ($row1=pg_fetch_assoc($res1) ) {
                          $details['count']=$count+1;
					      $details['initial_buffr_time'] = $row1['max_initial_buffr_time'];
					      $details['date_time'] =  $row1['max_time'];
					      $details['percentage'] = $row1['max_percentage'];
					      $details['total_buffer_time'] = $row1['total_buffer_time'];
					      $details['no_of_buffer_per_session'] = $row1['max_no_of_buffer'];
					      $details['first_bit_recieve_time'] = $row1['max_first_bit_recieve_time'];
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
echo $card;*/
echo json_encode($response);


?>
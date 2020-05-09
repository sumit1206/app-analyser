<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$from_timestamp =$_GET['from_timestamp'] ;
$to_timestamp =$_GET['to_timestamp'] ;
$operating_system = $_GET['operating_system'];
$operator = $_GET['operator'];
$application = $_GET['application'];
if($from_timestamp== "" || $operating_system == "" || $operator == "" || $application == "" ){
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
$sql = "SELECT   DISTINCT session FROM rf_data_with_buffer WHERE (timestamp BETWEEN '$unx_frm' AND '$unx_to') AND spn ='$operator' AND running_app = '$application'  AND wifi_state!='-' AND no_of_buffer_per_session != '0' ";
//$sql0 = "SELECT   COUNT(DISTINCT session) FROM rf_data_with_buffer WHERE (timestamp BETWEEN '$unx_frm' AND '$unx_to') AND spn ='$operator' AND running_app = '$application'  AND no_of_buffer_per_session != '0' AND wifi_state!='-' ";
if($application == 'All Application'){
	    $sql = "SELECT   DISTINCT session FROM rf_data_with_buffer WHERE (timestamp BETWEEN '$unx_frm' AND '$unx_to') AND spn ='$operator' AND no_of_buffer_per_session != '0' AND no_of_buffer_per_session != '0' AND  wifi_state!='-' ";
	   // $sql0 = "SELECT   COUNT(DISTINCT session) FROM rf_data_with_buffer WHERE (timestamp BETWEEN '$unx_frm' AND '$unx_to') AND spn ='$operator' AND no_of_buffer_per_session != '0'  AND wifi_state!='-' ";
 }
 //echo $sql;
 //AND no_of_buffer_per_session != '0' 
$res = pg_query($con,$sql);
$response= array();
$response['details'] = array();
$details = array();
//$res0 = pg_query($con,$sql0);
//$row0 = pg_fetch_assoc($res0);
//$response['no_of_page']=ceil($row0['count']/25);
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){
    $count = 0;	
		while ($row=pg_fetch_assoc($res)) {
		  $count = $count +1;
		  $session = $row['session'];
	      $sql1 = "SELECT  * FROM rf_data_with_buffer WHERE session = '$session' AND percentage != '0'  AND status = '1'  ORDER BY timestamp DESC  ";
	      //echo $sql1;
		  //AND `tech`!= '-' AND `rsrp`!= '-' AND `rscp`!= '-' AND `sinr`!= '-' AND `psc_pci`!= '-' AND `cell_id` != '-' AND `spn`!= '-'
		  $res1 = pg_query($con,$sql1);
          if($res1){		  
	         $numRows1=pg_num_rows($res1);
	         if($numRows1>0 ){
				 while ($row1=pg_fetch_assoc($res1) ) {
				   $details['sl_no'] = $count;
				   $details['session'] = $session;
		           $details['date_time'] = $row1['date_time'];
		           $details['tech'] = $row1['tech'];
		           $details['sub_tech'] = $row1['sub_tech'];
		           $details['rsrp'] = $row1['rsrp'];
		           $details['rsrq'] = $row1['rsrq'];
		           $details['sinr'] = $row1['sinr'];
		           $details['cell_id'] = $row1['cell_id'];
		           $details['psc_pci'] = $row1['psc_pci'];
		           $details['spn'] = $row1['spn'];
		           $details['cqi'] = $row1['cqi'];
		           array_push($response['details'], $details);
				 }
			 }
		  }
        }
	}
}
//print_r ($response); 
/*
$card2="";
$len_ar = count($response);
for($i=0 ;$i<$len_ar;$i++){
	$card2.= 	   
	'<tr>
		<td>'.($i+1).'</td>
		<td>'.$response[$i]['session'].'</td>
		<td>'.$response[$i]['date_time'].'</td>
		<td>'.$response[$i]['spn'].'</td>
		<td>'.$response[$i]['tech'].'</td>
		<td>'.$response[$i]['sub_tech'].'</td>
		<td>'.$response[$i]['rsrp'].'</td>
		<td>'.$response[$i]['rsrq'].'</td>
		<td>'.$response[$i]['sinr'].'</td>
		<td>'.$response[$i]['psc_pci'].'</td>
		<td>'.$response[$i]['cell_id'].'</td>
		<td>'.$response[$i]['cqi'].'</td>
      </tr>';
}
echo $card2;*/
echo json_encode($response);
?>
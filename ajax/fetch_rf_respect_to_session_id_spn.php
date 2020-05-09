<?php
include("../../connection/con.php");
date_default_timezone_set('Asia/Kolkata');
$session =$_GET['session'];
$response= array();
$response['details'] = array();
$sql="SELECT * FROM public.rf_data_with_buffer where status = '1' and session = '$session' order by timestamp ASC ";
//echo $sql;
$res1 = pg_query($con,$sql);
if($res1){		  
	 $numRows1=pg_num_rows($res1);
	 if($numRows1>0 ){
		 while ($row1=pg_fetch_assoc($res1) ) {
			$details = array();
			$details['sl_no'] = $count;
			$details['session'] = $session;
			$time = date('m/d/Y H:i:s', $row1['timestamp']/1000);
			$details['date_time'] =  $time;
			$details['tech'] = $row1['tech'];
			$details['sub_tech'] = $row1['sub_tech'];
			$details['rsrp'] = $row1['rsrp'];
			$details['rsrq'] = $row1['rsrq'];
			$details['sinr'] = $row1['sinr'];
			$details['cell_id'] = $row1['cell_id'];
			$details['psc_pci'] = $row1['psc_pci'];
			$details['spn'] = $row1['spn'];
			$details['cqi'] = $row1['cqi'];
			$details['total_bits_received'] = $row1['total_bits_received'];
			array_push($response['details'], $details);
		}
	}
}
echo json_encode($response);
?>
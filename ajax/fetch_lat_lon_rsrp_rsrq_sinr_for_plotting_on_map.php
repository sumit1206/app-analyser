<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$total_rsrp=0;
$total_rsrq=0;
$total_sinr=0;
$count=0;
$sql = "SELECT lat, lon, rsrp, rsrq, sinr, android_id, time FROM app_analyser_data_for_plot_on_map LIMIT 500";
$res = pg_query($con,$sql);
$response= array();
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){
        $response['success']=1;
        $response['data']=array();		
		while ($row=pg_fetch_assoc($res)) {
			$details = array();
			$lat = $row['lat'];
			$lon = $row['lon'];
			$rsrp = $row['rsrp'];
			$rsrq = $row['rsrq'];
			$sinr = $row['sinr'];
			if($lat != '' && $lon != '' && $lat != '0' && $lon != '0'){
				if( $rsrp != '-'&& $rsrq != '-'&& $sinr != '-'){
					$total_rsrp=$total_rsrp + $rsrp;
			        $total_rsrq=$total_rsrq + $rsrq;
			        $total_sinr=$total_sinr + $sinr;
			        $count= $count+1;
				}
			
			$details['lat'] = $row['lat'];
			$details['lon'] = $row['lon'];
			$details['rsrp'] = $row['rsrp'];
			$details['rsrq'] = $row['rsrq'];
			$details['sinr'] = $row['sinr'];
			$details['android_id'] = $row['android_id'];
			$details['time'] = $row['time'];
			array_push($response['data'], $details);}
		}
		$response['avq_rsrp']= $total_rsrp/$count;
		$response['avq_rsrq']= $total_rsrq/$count;
		$response['avq_sinr']= $total_sinr/$count; 
	}else{
		$response['success']=0;
		$response['message']="No data found.";
	}
}else{
	$response['success']=0;
	$response['message']="Failed to execute query.";
}
echo json_encode($response);
?>
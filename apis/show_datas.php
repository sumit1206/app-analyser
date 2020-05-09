<?php
//ini_set("display_errors",1);
include("../../connection/con.php");
$from_timestamp = $_POST['from_timestamp'];
$to_timestamp = $_POST['to_timestamp'];
$sql = "SELECT * FROM rf_data_with_buffer WHERE timestamp BETWEEN '$from_timestamp' AND '$to_timestamp' ORDER BY timestamp ASC";
$result = pg_query($con,$sql);
$numRows=pg_num_rows($result);
if($result){
	$numRows=pg_num_rows($result);
	if($numRows>0){
		$response["success"] = 1;
        $response["message"] = "success";
		$response["details"] = array();
		while ($row=pg_fetch_assoc($result)) {
	       $details["make"] = $row['make'];
		   $details["model"] = $row['model'];
		   $details["os"] = $row['os'];
		   $details["app_version"] = $row['app_version'];
		   $details["imei"] = $row['imei'];
		   $details["timestamp"] = $row['timestamp'];
		   $details["lat"] = $row['lat'];
		   $details["lon"] = $row['lon'];
		   $details["accuracy"] = $row['accuracy'];
		   $details["tech"] = $row['tech'];
		   $details["sub_tech"] = $row['sub_tech'];
		   $details["asu"] = $row['asu'];
		   $details["rsrp"] = $row['rsrp'];
		   $details["rscp"] = $row['rscp'];
		   $details["rx_level"] = $row['rx_level'];
		   $details["rsrq"] = $row['rsrq'];
		   $details["ecio"] = $row['ecio'];
		   $details["rx_qual"] = $row['rx_qual'];
		   $details["earfcn"] = $row['earfcn'];
		   $details["uarfcn"] = $row['uarfcn'];
		   $details["arfcn"] = $row['arfcn'];
		   $details["sinr"] = $row['sinr'];
		   $details["mcc"] = $row['mcc'];
		   $details["mnc"] = $row['mnc'];
		   $details["lac_tac"] = $row['lac_tac'];
		   $details["psc_pci"] = $row['psc_pci'];
		   $details["spn"] = $row['spn'];
		   $details["data_state"] = $row['data_state'];
		   $details["service_state"] = $row['service_state'];
		   $details["rnc"] = $row['rnc'];
		   $details["cqi"] = $row['cqi'];
		   $details["freq"] = $row['freq'];
		   $details["band"] = $row['band'];
		   $details["ta"] = $row['ta'];
		   $details["call_state"] = $row['call_state'];
		   $details["call_duration"] = $row['call_duration'];
		   $details["test_state"] = $row['test_state'];
		   $details["rssi"] = $row['rssi'];
		   $details["ss"] = $row['ss'];
		   $details["recording"] = $row['recording'];
		   $details["running_app"] = $row['running_app'];
		   $details["initial_buffr_time"] = $row['initial_buffr_time'];
		   $details["through_put"] = $row['through_put'];
		   $details["buffer_time"] = $row['buffer_time'];
		   $details["status"] = $row['status'];
	       
		   array_push($response["details"], $details);
		}

		echo json_encode($response);
	}else{
      $response["success"] = 0;
      $response["message"] = "No data found.";
	  echo json_encode($response);
}
	     			
}else{
      $response["success"] = 0;
      $response["message"] = "Failed to connect. Please try again.";
	  echo json_encode($response);
}

?>
<?php
date_default_timezone_set("Asia/Kolkata");
//ini_set("display_errors",1);
include("../../connection/con.php");
$from_timestamp = $_GET['from_timestamp'];
$to_timestamp = $_GET['to_timestamp'];
if($to_timestamp == ""){
	$to_timestamp = $from_timestamp."23:59:59";
}else{
	$to_timestamp = $to_timestamp."23:59:59";
}
$from_timestamp = $from_timestamp."00:00:01";

$from_timestamp=strtotime($from_timestamp);
$to_timestamp=strtotime($to_timestamp);
//echo $from_timestamp;
$operating_system = $_GET['operating_system'];
$operator = $_GET['operator'];
$application = $_GET['application'];
$city = $_GET['city'];
$filename = "buffer_logs_".date("d-m-Y-H-i-s").".csv";
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: text/csv");
$out = fopen("php://output",'w');

$sql = "SELECT * FROM rf_data_with_buffer WHERE (timestamp BETWEEN '$from_timestamp' AND '$to_timestamp') AND os = '$operating_system' AND spn = '$operator' AND running_app  = '$application'  and state = '$city' ORDER BY timestamp ASC";
//echo $sql;
if($operator == 'WIFI' && $application == 'All Application'){
	$sql = "SELECT * FROM rf_data_with_buffer WHERE (timestamp BETWEEN '$from_timestamp' AND '$to_timestamp') AND os = '$operating_system'  AND wifi_state='1'  and state = '$city' ORDER BY timestamp ASC";
}
if($operator == 'WIFI' && $application != 'All Application'){
	$sql = "SELECT * FROM rf_data_with_buffer WHERE (timestamp BETWEEN '$from_timestamp' AND '$to_timestamp') AND os = '$operating_system'  AND running_app  = '$application' AND wifi_state='1'  and state = '$city' ORDER BY timestamp ASC";
}
if($application == 'All Application' && $operator != 'WIFI'){
	$sql = "SELECT * FROM rf_data_with_buffer WHERE (timestamp BETWEEN '$from_timestamp' AND '$to_timestamp') AND os = '$operating_system'  and state = '$city'  ORDER BY timestamp ASC";
}
//echo $sql;
$res = pg_query($con,$sql);
if (pg_num_rows($res) > 0) {
    $flag = false;
    while ($row=pg_fetch_assoc($res)) {
        if(!$flag) {
            fputcsv($out, array_keys($row), ',', '"');
            $flag = true;
        }
        fputcsv($out, array_values($row), ',', '"');
    }
}

fclose($out);
//echo $out;
?>
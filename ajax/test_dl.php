<?php
date_default_timezone_set("Asia/Kolkata");
//ini_set("display_errors",1);
include("../../connection/con.php");

//echo $from_timestamp;
$operating_system = $_GET['operating_system'];
$operator = $_GET['operator'];
$application = $_GET['application'];
$filename = "buffer_logs_".date("d-m-Y-H-i-s").".csv";
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: text/csv");
$out = fopen("php://output",'w');

$sql = "SELECT * FROM rf_data_with_buffer WHERE date_time like '03 Mar 2020%' AND  imei = '865184039554346' ORDER BY timestamp ASC";

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
<?php 
include("../../connection/con.php");
$from_timestamp =$_GET['from_timestamp'] ."00:00:00";
$to_timestamp =$_GET['to_timestamp'] ."23:59:59";
$operating_system = $_GET['operating_system'];
$operator = $_GET['operator'];
$application = $_GET['application'];
$unx_to = strtotime($to_timestamp);
$unx_frm = strtotime($from_timestamp);


$query ="SELECT running_app,session_throughput FROM rf_data_with_buffer WHERE (timestamp BETWEEN '$unx_frm' AND '$unx_to') AND session_throughput != '-' AND os = '$operating_system' AND spn = '$operator' AND running_app  = '$application'";
$initial_buffer_time_query ="SELECT running_app,initial_buffr_time FROM rf_data_with_buffer WHERE (timestamp BETWEEN '$unx_frm' AND '$unx_to') AND os = '$operating_system' AND spn = '$operator' AND running_app  = '$application' ";
$result = pg_query($con,$query);
$initial_buffer_time_resullt = pg_query($con,$initial_buffer_time_query);
$chart_data = '';
$initial_buffer_time='';
while($row = pg_fetch_assoc($initial_buffer_time_resullt))
{
 $initial_buffer_time .= "{ running_app:'".$row["running_app"]."', initial_buffr_time:'".$row["initial_buffr_time"]."'}, ";
}
$initial_buffer_time = substr($initial_buffer_time, 0, -2);
$initial_buffer_time_json = json_encode($initial_buffer_time);
echo $initial_buffer_time_json;
while($row1 = pg_fetch_assoc($result))
{
 $chart_data .= "{ running_app:'".$row1["running_app"]."', session_throughput:'".$row1["session_throughput"]."', initial_buffr_time:'".$row1["initial_buffr_time"]."'}, ";
}
$chart_data = substr($chart_data, 0, -2);
$chart_data_json = json_encode($chart_data);
echo $chart_data_json;
?>
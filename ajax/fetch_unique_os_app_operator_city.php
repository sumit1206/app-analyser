<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$response = array();
$response['os'] = array();
$response['app'] = array();
$response['operator'] = array();
$response['city'] = array();

$sql_state="SELECT DISTINCT state FROM rf_data_with_buffer WHERE state != ''  and state != '-' ";
$sql_os="SELECT DISTINCT  os  FROM  rf_data_with_buffer  WHERE 1";
$sql_app ="SELECT DISTINCT  running_app  FROM  rf_data_with_buffer  WHERE  running_app  != ''";
$sql_operator ="SELECT DISTINCT  spn  FROM  rf_data_with_buffer  WHERE  spn  != '' AND  spn != '-'";

//$sql_city ="SELECT DISTINCT  city  FROM  rf_data_with_buffer  WHERE 1";
$res_s = pg_query($con,$sql_state);
$res_os = pg_query($con,$sql_os);
$res_app = pg_query($con,$sql_app);
$res_operator = pg_query($con,$sql_operator);
//$res_city = $con->query($sql_city);
//$details['state'][$i] = $row_os['state'];

if($res_s){
  $i=0;
  $numRows_s=pg_num_rows($res_s);
  if($numRows_s>0){
    $city = array();
    while($row_s=pg_fetch_assoc($res_s)){
             $details['state'] = $row_s['state'];
             array_push($response['city'], $details['state']); 
             $i=$i+1;      
    } 
  } 
}
if($res_os){
	$i=0;
	$numRows_os=pg_num_rows($res_os);
	if($numRows_os>0){
		$details = array();
		while($row_os=pg_fetch_assoc($res_os)){
             $details['os'][$i] = $row_os['os'];
             //array_push($response['os'], $details['os']); 
             $i=$i+1;			 
		}
	}
}
if($res_app){
	$i=0;
	$numRows_app=pg_num_rows($res_app);
	if($numRows_app>0){
		$details = array();
		while($row_app=pg_fetch_assoc($res_app)){
             $details['running_app'][$i] = $row_app['running_app'];
            // array_push($response['app'], $details['running_app']); 
             $i=$i+1;			 
		}
	}
}
if($res_operator){
	$i=0;
	$numRows_operator=pg_num_rows($res_operator);
	if($numRows_operator>0){
		$details = array();
		while($row_op=pg_fetch_assoc($res_operator)){
             $details['operator'][$i] = $row_op['spn'];
             //array_push($response['operator'], $details['operator']);
             $i=$i+1; 			 
		}
	}
}
echo json_encode($response);
?>
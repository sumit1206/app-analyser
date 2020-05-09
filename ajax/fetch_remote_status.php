<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$hash_id=$_GET['hash_id'];
$android_id=$_GET['android_id'];


$sql = "SELECT internet_status, start_btn, stop_btn, upload_btn, power_btn, reboot_btn, red_led, green_led, yellow_led, blue_led FROM black_box_remote_control WHERE android_id='$android_id' AND bb_id= '$hash_id'";
//echo $sql;
$res = pg_query($con,$sql);
$response= array();
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){ 
	$response['success']=1;  
	while ($row=pg_fetch_assoc($res)) {
		$response['data']= array();
		$details['internet_status']= $row['internet_status'];
		$details['start_btn']= $row['start_btn'];
		$details['stop_btn']= $row['stop_btn'];
		$details['upload_btn']= $row['upload_btn'];
		$details['power_btn']= $row['power_btn'];
		$details['reboot_btn']= $row['reboot_btn'];
		$details['red_led']= $row['red_led'];
		$details['green_led']= $row['green_led'];
		$details['yellow_led']= $row['yellow_led'];
		$details['blue_led']= $row['blue_led'];
		array_push($response['data'], $details); 
		
		}
	}else{
		$response['success']= 0 ;
	}
}
echo json_encode ($response);
?>
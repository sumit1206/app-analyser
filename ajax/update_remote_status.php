<?php
include("../connection/con.php");
//ini_set("display_errors",1);
$hash_id=$_GET['hash_id'];
$start_btn=$_GET['start_btn'];
$stop_btn=$_GET['stop_btn'];
$upload_btn=$_GET['upload_btn'];
$power_btn=$_GET['power_btn'];
$reboot_btn=$_GET['reboot_btn'];
$android_id=$_GET['android_id'];


$sql = "UPDATE `black_box_remote_control` SET `start_btn`='$start_btn',`stop_btn`='$stop_btn',`upload_btn`='$upload_btn',`power_btn`='$power_btn',`reboot_btn`='$reboot_btn' WHERE `android_id`='$android_id' AND `bb_id`='$hash_id'";
$res = $con->query($sql);
$response= array();
if($res){
	    $response['success']= 1 ;
	}else{
		$response['success']= 0 ;
	}

echo json_encode ($response);
?>
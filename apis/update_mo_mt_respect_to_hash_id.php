<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$hash_id=$_GET['hash_id'];
$mo_id=$_GET['mo_id'];
$mt_id=$_GET['mt_id'];
$android_id=$_GET['android_id'];
$call_time=$_GET['call_time'];
$wait_time=$_GET['wait_time'];
$sql0 = "UPDATE call_details SET android_id='' WHERE android_id = '$android_id'";
$sql6="SELECT * FROM call_details WHERE hash_id = '$hash_id'";
$res6 = pg_query($con,$sql6);
if($res6){
	$numRows6=pg_num_rows($res6);
	if($numRows6>0){
		$sql = "UPDATE call_details SET mo_id='$mo_id',mt_id='$mt_id',android_id='$android_id',call_time='$call_time',wait_time='$wait_time' WHERE hash_id ='$hash_id'";
        //echo $sql;
        $res = pg_query($con,$sql);
        $response= array();
        if($res){
         	$response['success']= 1 ;
        }else{
         	$response['success']= 0 ;
        }
	}else{
		$sql99="INSERT INTO call_details (hash_id, mo_id, mt_id, wait_time, ip_address, android_id, lat, lan, last_active, call_time) VALUES ('$hash_id','$mo_id','$mt_id','$wait_time','','$android_id','','','','$call_time')";
		$res99 =pg_query($con,$sql99);
		if($res99){
         	$response['success']= 1 ;
        }else{
         	$response['success']= 0 ;
        }
	}
}



echo json_encode ($response);
?>
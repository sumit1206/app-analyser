<?php
date_default_timezone_set('Asia/Kolkata');
$json_array = array();
error_reporting(E_ALL);
//ini_set('display_errors', 1);
ini_set('max_execution_time', 0);

include_once('../ajax_mysql/obj_connection.php');

/*
$max_mute_no = 0;
$res = $con->query("select max(mute_no) as max_mute_no from black_box_datas");
if ($res->num_rows > 0) {
     while ($row = $res->fetch_assoc()) {
        $max_mute_no = $row['max_mute_no'];
    }
}

$res = $con->query("select IMEI, MUTE_ON, MUTE_OF/1000 as mute_duration from black_box_datas where MUTE_OF > 0 and is_mute = 0 order by IMEI, MUTE_ON");
if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $max_mute_no++;
        $start_time = $row['MUTE_ON'];
        $mute_duration = $row['mute_duration'];
        $mute_duration_arr = explode(".", $mute_duration);
//        echo date("Y-m-d H:i:s", substr_replace($start_time, "", -3))."<br>";
        $end_time = strtotime('+'. floor($mute_duration).' seconds', $start_time);
        $end_time += $mute_duration_arr[1];
//        echo date("Y-m-d H:i:s", substr_replace($end_time, "", -3));
        
        $con->query("update black_box_datas set is_mute = 1, mute_no = $max_mute_no where IMEI = '".$row['IMEI']."' and TIMESTAMP BETWEEN $start_time and $end_time");
    }
    echo "Done";
} else {
    echo 'No raw data found';
}
 */

$max_call_no = 1;
$res = $con->query("select max(call_no) as max_call_no from black_box_datas");
if ($res->num_rows > 0) {
     while ($row = $res->fetch_assoc()) {
        $max_call_no = $row['max_call_no'];
    }
}

$res = $con->query("select sl_no, IMEI, TIMESTAMP, CALL_STATE, CALL_DURATION from black_box_datas where call_no is null order by IMEI, TIMESTAMP");
    
if ($res->num_rows > 0) {
    $prev_call_status = 0;
    $prev_imei = '';
    while ($row = $res->fetch_assoc()) {
        $current_call_status = $row['CALL_STATE'];
        $current_imei = $row['IMEI'];
        
        if(!empty($prev_imei) && $prev_imei != $current_imei) {
            $prev_call_status = 0;
        }
        
        $con->query("update black_box_datas set call_no = '$max_call_no' where sl_no = '".$row['sl_no']."'");
        
        if($prev_call_status > 0 and $current_call_status == 0) {
            $max_call_no++;
        }
        
        $prev_imei = $current_imei;
        $prev_call_status = $current_call_status;
    }
    echo 'Done';
//    echo '<pre>';
//    print_r($data);
} else {
    echo 'No raw data found';
}

mysqli_close($con);
exit();
?>
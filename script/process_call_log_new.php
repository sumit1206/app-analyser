<?php
date_default_timezone_set('Asia/Kolkata');
$json_array = array();
error_reporting(E_ALL);
//ini_set('display_errors', 1);
ini_set('max_execution_time', 0);

include_once('../ajax/obj_connection.php');

/*
$max_mute_no = 0;
$res = pg_query($con, "select max(mute_no) as max_mute_no from black_box_datas_new");
if (pg_num_rows($res) > 0) {
     while ($row = pg_fetch_assoc($res)) {
        $max_mute_no = $row['max_mute_no'];
    }
}

$res = pg_query($con, "select sl_no, imei, ts, call_no from black_box_datas_new where is_mute = 1 and mute_no is null order by imei, ts, call_no");
if (pg_num_rows($res) > 0) {
    $prev_call_no = 0;
    while ($row = pg_fetch_assoc($res)) {
        if($prev_call_no == 0) {
            $max_mute_no++;
            $prev_call_no = $row['call_no'];
        }
        
        if($prev_call_no != $row['call_no']) {
            $max_mute_no++;
        }
          
        pg_query($con, "update black_box_datas_new set mute_no = $max_mute_no where sl_no = '".$row['sl_no']."'");
        
        $prev_call_no = $row['call_no'];
    }
    echo "Done";
} else {
    echo 'No raw data found';
}
exit;
*/
/*
$max_mute_no = 0;
$res = pg_query($con, "select max(mute_no) as max_mute_no from black_box_datas_new");
if (pg_num_rows($res) > 0) {
     while ($row = pg_fetch_assoc($res)) {
        $max_mute_no = $row['max_mute_no'];
    }
}

$res = pg_query($con, "select imei, ts from black_box_datas_new where MUTE_OF > 0 and is_mute = 0 order by imei, MUTE_ON");
if (pg_num_rows($res) > 0) {
    while ($row = pg_fetch_assoc($res)) {
        $max_mute_no++;
        $start_time = $row['MUTE_ON'];
        $mute_duration = $row['mute_duration'];
        $mute_duration_arr = explode(".", $mute_duration);
//        echo date("Y-m-d H:i:s", substr_replace($start_time, "", -3))."<br>";
        $end_time = strtotime('+'. floor($mute_duration).' seconds', $start_time);
        $end_time += $mute_duration_arr[1];
//        echo date("Y-m-d H:i:s", substr_replace($end_time, "", -3));
        
        pg_query($con, "update black_box_datas_new set is_mute = 1, mute_no = $max_mute_no where imei = '".$row['imei']."' and ts BETWEEN $start_time and $end_time");
    }
    echo "Done";
} else {
    echo 'No raw data found';
}
*/

$max_call_no = 1;
$res = pg_query($con, "select max(call_no) as max_call_no from black_box_datas_new");
if (pg_num_rows($res) > 0) {
     while ($row = pg_fetch_assoc($res)) {
        $max_call_no = $row['max_call_no'];
    }
}

$res = pg_query($con, "select sl_no, imei, ts, call_state, lag(call_state) over (partition by imei order by ts) as prev_call_state from black_box_datas_new where call_no is null order by imei, ts");
    
if (pg_num_rows($res) > 0) {
    $prev_call_status = 0;
    $prev_imei = '';
    while ($row = pg_fetch_assoc($res)) {
        $current_call_status = $row['call_state'];
        $current_imei = $row['imei'];
        
        if(!empty($prev_imei) && $prev_imei != $current_imei) {
            $prev_call_status = 0;
        }
        
        if($current_call_status > 0) {
            pg_query($con, "update black_box_datas_new set call_no = '$max_call_no' where sl_no = '".$row['sl_no']."'");
        }
        
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

pg_close($con);
exit();
?>
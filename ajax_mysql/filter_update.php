<?php
session_start();

$country = !empty($_POST['country']) ? $_POST['country'] : '';
$city = !empty($_POST['city']) ? $_POST['city'] : '';
$op = !empty($_POST['op']) ? $_POST['op'] : '';
$os = !empty($_POST['os']) ? $_POST['os'] : '';
$trend = !empty($_POST['trend']) ? $_POST['trend'] : '';
$ym = !empty($_POST['ym']) ? $_POST['ym'] : '';
$start_dt = !empty($_POST['start_dt']) ? $_POST['start_dt'] : '';
$end_dt = !empty($_POST['end_dt']) ? $_POST['end_dt'] : '';

$response['status'] = 500;
$response['msg'] = "";
if(!empty($country) && !empty($city) && !empty($op) && !empty($os) && !empty($trend) && (!empty($ym) || (!empty($start_dt) && !empty($end_dt)))) {
    $_SESSION['filterData'] = $_POST;
} elseif(!empty($_SESSION['filterData'])) {
    $response['status'] = 200;
    $response['msg'] = $_SESSION['filterData'];
}
if(empty($_SESSION['filterData'])) {
    $response['msg'] = "Mandatory parameters missing";
}
echo json_encode($response);
?>
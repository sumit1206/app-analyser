<?php
date_default_timezone_set('Asia/Kolkata');

$json_array = array();
error_reporting(E_ALL);
//ini_set('display_errors', 1);
session_start();
$json_array = array();
if (!isset($_SESSION['login_details'])) {
    $json_array['status'] = 401;
    $json_array['message'] = "Session Logged Out";
    echo json_encode($json_array);
    exit();
}
include_once('obj_connection.php');
//$master_operators = array('Airtel', 'Jio');
//$block_call_max_threshold = 10; // in seconds
//$drop_call_max_threshold = 180; // in seconds
$downlink_mute_bb_ids = array('1234', '5285');
$uplink_mute_bb_ids = array('12340', '52850');
$total_calls_bb_ids_cond = " BB_ID in ('" . implode("','", $downlink_mute_bb_ids) . "') ";
$country_lat_lng = array(
                        'india' => array(
                                        'lat' => '23.249840', 'lng' => '77.280891',
                                        'min_lat' => '6.5546079', 'max_lat' => '35.6745457',
                                        'min_lng' => '68.1113787', 'max_lng' => '97.395561'
                                    ),
                        'japan' => array(
                                        'lat' => '36.204824', 'lng' => '138.252924',
                                        'min_lat' => '20.2145811', 'max_lat' => '45.7112046',
                                        'min_lng' => '122.7141754', 'max_lng' => '154.205541'
                                    )
                    );

if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_network_details_sinr') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = isset($_POST['city']) ? strtolower($_POST['city']) : '';
//    $op = isset($_POST['op']) ? "lower(operator) = '".strtolower($_POST['op'])."'" : '';
    $op = isset($_POST['op']) ? "lower(spn) like '%".strtolower($_POST['op'])."%'" : '';
    $op_rf = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(spn) like '%".strtolower($_POST['op'])."%'" : '';
    $os = isset($_POST['os']) ? (($_POST['os']=='IOS') ? "lower(os) = 'apple'" : "lower(os) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    $os_rf = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(make) = 'apple'" : "lower(make) != 'apple'") : '';
    $os_rf = (empty($op_rf)) ? $os_rf : 'AND ' . $os_rf;
    
    $start_ts = isset($_POST['start_dt']) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = isset($_POST['end_dt']) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (start_time BETWEEN $start_ts AND $end_ts)";
        $ts_rf = " AND (ts BETWEEN $start_ts AND $end_ts)";
    }
    
    if(!empty($city)) {
        $city_lat_lng = pg_query($con, "select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if (pg_num_rows($city_lat_lng) > 0) {
            while ($rows = pg_fetch_assoc($city_lat_lng)) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
            $ts_rf .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " and lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lng >= " . $country_lat_lng[$country]['min_lng'] . " and lng <= " . $country_lat_lng[$country]['max_lng'];
        $ts_rf = (empty($ts_rf)) ? '' : $ts_rf . ' AND ';
        $ts_rf .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
         
    $filename = "sinr_details.csv";

    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: text/csv");

    $out = fopen("php://output", 'w');
    
    //$sql_sinr = "select lat, lon lng, sinr FROM voice_test.rf_data WHERE lat is not null and lat > 0 and lon is not null and lon > 0 and sinr is not null and $op_rf $os_rf $ts_rf and $total_calls_bb_ids_cond";
    
    $sql_sinr = "select * from (select lat, lon lng, sinr, row_number() over (partition by round(lat*12000),round(lon*12000)) rowno from voice_test.rf_data where lat is not null and lat > 0 and lon is not null and lon > 0 and sinr is not null and $op_rf $os_rf $ts_rf and $total_calls_bb_ids_cond) tbl where rowno = 1";
    $res_sinr = pg_query($con, $sql_sinr);
    
    if (pg_num_rows($res_sinr) > 0) {
        while ($row= pg_fetch_assoc($res_sinr)) {
            fputcsv($out, array_values($row), ',', '"');
        }
        fclose($out);
    }
}
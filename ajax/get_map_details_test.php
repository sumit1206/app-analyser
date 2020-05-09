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

$dwnlink_bb_ids_cond = " bb_id in ('" . implode("','", $downlink_mute_bb_ids) . "') ";
$uplink_bb_ids_cond = " bb_id in ('" . implode("','", $uplink_mute_bb_ids) . "') ";

if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_map_details') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = (isset($_POST['city']) && !empty($_POST['city'])) ? strtolower($_POST['city']) : '';
    $trend = isset($_POST['trend']) ? $_POST['trend'] : '';
//    $op = isset($_POST['op']) ? "lower(operator) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(operator) like '%".strtolower($_POST['op'])."%'" : '';
    $op_rf = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(spn) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(os) = 'apple'" : "lower(os) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    $os_rf = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(make) = 'apple'" : "lower(make) != 'apple'") : '';
    $os_rf = (empty($op_rf)) ? $os_rf : 'AND ' . $os_rf;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (start_time BETWEEN $start_ts AND $end_ts)";
        $ts_rf = " AND (ts BETWEEN $start_ts AND $end_ts)";
    }
    
    $lat = $lng = '';
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
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lng >= " . $country_lat_lng[$country]['min_lng'] . " and lng <= " . $country_lat_lng[$country]['max_lng'];
        $ts_rf = (empty($ts_rf)) ? '' : $ts_rf . ' AND ';
        $ts_rf .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
    
    // Mute Locations Downlink
    $sql_mute_locations = "select lat, lon lng, TIMESTAMP 'epoch' + substring((ts+19800000)::varchar,0,11)::bigint * INTERVAL '1 second' as ts, rsrp, sinr, cell_id from voice_test.rf_data where is_mute = 1 and call_state > 0 and $dwnlink_bb_ids_cond and lat is not null and lat > 0 and lon is not null and lon > 0 and $op_rf $os_rf $ts_rf";
    $res_mute_locations = pg_query($con, $sql_mute_locations);    
    $mute_locations_downlink = false;
    if (pg_num_rows($res_mute_locations) > 0) {
        while ($row=pg_fetch_assoc($res_mute_locations)) {
            $mute_locations_downlink[] = array('lat' => $row['lat'], 'lon' => $row['lng'], 'ts' => $row['ts'], 'rsrp' => number_format($row['rsrp'], 1), 'sinr' => number_format($row['sinr'], 1), 'cell_id' => $row['cell_id']);
        }
    }
    
    // Mute Locations Uplink
    $sql_mute_locations = "select lat, lon lng, TIMESTAMP 'epoch' + substring((ts+19800000)::varchar,0,11)::bigint * INTERVAL '1 second' as ts, rsrp, sinr, cell_id from voice_test.rf_data where is_mute = 1 and call_state > 0 and $uplink_bb_ids_cond and lat is not null and lat > 0 and lon is not null and lon > 0 and $op_rf $os_rf $ts_rf";
    $res_mute_locations = pg_query($con, $sql_mute_locations);    
    $mute_locations_uplink = false;
    if (pg_num_rows($res_mute_locations) > 0) {
        while ($row=pg_fetch_assoc($res_mute_locations)) {
            $mute_locations_uplink[] = array('lat' => $row['lat'], 'lon' => $row['lng'], 'ts' => $row['ts'], 'rsrp' => number_format($row['rsrp'], 1), 'sinr' => number_format($row['sinr'], 1), 'cell_id' => $row['cell_id']);
        }
    }
    
    // Drop & Block Locations
    $sql_cdb_locations = "select TIMESTAMP 'epoch' + substring((ts+19800000)::varchar,0,11)::bigint * INTERVAL '1 second' as ts, lat, lon lng, rsrp, rsrq, sinr, psc_pci pci, cell_id, call_type from voice_test.rf_data rf inner join voice_test.call_details cd on (rf.ts=cd.end_time and rf.bb_id=cd.bb_id) where is_mo = 1 and call_type in (1,2) and is_mapped = 1 and lat is not null and lon is not null and $op_rf $os_rf $ts_rf";
    $sql_cdb_locations = str_replace("lat", "rf.lat", $sql_cdb_locations);
    
    $res_cdb_locations = pg_query($con, $sql_cdb_locations);    
    $drop_locations = $block_locations = false;
    if (pg_num_rows($res_cdb_locations) > 0) {
        while ($row=pg_fetch_assoc($res_cdb_locations)) {
            if($row['call_type'] == 1) {
                $block_locations[] = array('lat' => $row['lat'], 'lon' => $row['lng'], 'ts' => $row['ts'], 'rsrp' => number_format($row['rsrp'], 1), 'sinr' => number_format($row['sinr'], 1), 'cell_id' => round($row['cell_id']));
            } elseif($row['call_type'] == 2) {
                $drop_locations[] = array('lat' => $row['lat'], 'lon' => $row['lng'], 'ts' => $row['ts'], 'rsrp' => number_format($row['rsrp'], 1), 'sinr' => number_format($row['sinr'], 1), 'cell_id' => round($row['cell_id']));
            }
        }
    }
    
    $json_array['status'] = 200;
    $json_array['message']['lat'] = $lat;
    $json_array['message']['lng'] = $lng;
    $json_array['message']['mute_locations_downlink'] = $mute_locations_downlink;
    $json_array['message']['mute_locations_uplink'] = $mute_locations_uplink;
    $json_array['message']['block_locations'] = $block_locations;
    $json_array['message']['drop_locations'] = $drop_locations;
    echo json_encode($json_array);
}
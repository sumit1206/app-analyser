<?php
date_default_timezone_set('Asia/Kolkata');

$log_file = "logs/queries_".date("Y-m-d").".log";

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

$master_operators = array('Airtel', 'Jio');
$block_call_max_threshold = 10; // in seconds
$drop_call_max_threshold = 180; // in seconds
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

include_once('obj_connection.php');

$total_calls_bb_ids_cond = " BB_ID in ('" . implode("','", $downlink_mute_bb_ids) . "') ";

// Get Country Lat Lng
if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'country_lat_lng') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $json_array['status'] = 200;
    $json_array['message'] = $country_lat_lng[$country];
    echo json_encode($json_array);
}

// Get City Details
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_city_details') {    
    $city = $con->query("select city from city_lat_long order by city");
    
    if ($city->num_rows > 0) {
        $json_array['status'] = 200;
        while ($rows = $city->fetch_assoc()) {
            $json_array['message'][] = ucwords($rows['city']);
        }
    } else {
        $json_array['status'] = 400;
        $json_array['message'] = '';
    }
    echo json_encode($json_array);
}

// Get City LatLong
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_city_lat_lng') {
    $city = isset($_POST['city']) ? $_POST['city'] : '';
    $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
    if ($city_lat_lng->num_rows > 0) {
        $json_array['status'] = 200;
        while ($rows = $city_lat_lng->fetch_assoc()) {
            $json_array['message']['lat'] = $rows['latitude'];
            $json_array['message']['lng'] = $rows['longitude'];
        }
    } else {
        $json_array['status'] = 400;
        $json_array['message'] = '';
    }
    echo json_encode($json_array);
}

// operators
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'op_drop_down') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    
    $cond = '';
    if(isset($country_lat_lng[$country])) {
        $cond = " and lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }

//    $op = $con->query("select distinct lower(SPN) as operator from black_box_datas where SPN is not null and SPN != '' and SPN != '(null)' $cond");
//    
//    if ($op->num_rows > 0) {
//        $json_array['status'] = 200;
//        while ($rows = $op->fetch_assoc()) {
//            $json_array['message'][] = ucwords($rows['operator']);
//        }
//    } else {
//        $json_array['status'] = 400;
//        $json_array['message'] = '';
//    }
    
    $json_array['status'] = 200;
    $json_array['message'] = $master_operators;
    echo json_encode($json_array);
}

// export csv data
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'export_csv_data') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = isset($_POST['city']) ? strtolower($_POST['city']) : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = isset($_POST['op']) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = isset($_POST['os']) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = isset($_POST['start_dt']) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = isset($_POST['end_dt']) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts .= " and lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
    
    // filename for download
    $filename = "call_analyser_dump.csv";

    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: text/csv");

    $out = fopen("php://output", 'w');

//    $sql = "SELECT MAKE, MODEL, OS, appVersion,IMEI, TIMESTAMP, LAT, LON, ACCURACY, TECH, SUB_TECH, ASU, RSRP, RSCP, RX_LEVEL, RSRQ, ECIO, RX_QUAL, EARFCN,  UARFCN, ARFCN, SINR, MCC, MNC, LAC_TAC, CELL_ID, PSC_PCI, SPN, DATA_STATE, SERVICE_STATE, RNC, CQI, FREQ, BAND, TA, CALL_STATE, CALL_DURATION, TEST_STATE, RSSI, SS, MUTE_ON, MUTE_OF, BB_ID , call_drop, call_block FROM black_box_datas WHERE $op $os $ts";
    $sql = "SELECT * FROM black_box_datas WHERE $op $os $ts";
    $res = $con->query($sql);

    if ($res->num_rows > 0) {
        $flag = false;
        while ($row=$res->fetch_assoc()) {
            $row['Formatted_TIMESTAMP'] = (!empty($row['TIMESTAMP'])) ? date('Y-m-d H:i:s', substr_replace($row['TIMESTAMP'], "", -3)) : '';
            $row['Formatted_MUTE_ON'] = (!empty($row['MUTE_ON'])) ? date('Y-m-d H:i:s', substr_replace($row['MUTE_ON'], "", -3)) : '';
            if(!$flag) {
                fputcsv($out, array_keys($row), ',', '"');
                $flag = true;
            }

            fputcsv($out, array_values($row), ',', '"');
        }
    }
    fclose($out);
}

// get_summary_box_details
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_summary_box_details') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = (isset($_POST['city']) && !empty($_POST['city'])) ? strtolower($_POST['city']) : '';
    $trend = isset($_POST['trend']) ? $_POST['trend'] : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    $lat = $lng = '';
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
    
    // Total Calls
    $sql_total_calls = "select count(distinct call_no) total_calls from black_box_datas WHERE CALL_STATE = 2 AND $op $os $ts and $total_calls_bb_ids_cond";
    error_log($sql_total_calls."\n", 3, $log_file);
    $res_total_calls = $con->query($sql_total_calls);
    $total_calls = 0;
    if ($res_total_calls->num_rows > 0) {
        while ($row=$res_total_calls->fetch_assoc()) {
            $total_calls = $row['total_calls'];
        }
    }
//    $json_array['message']['qry1'] = $sql_total_calls;
    
    // Drop & Block Calls
    if(isset($_POST['os']) && $_POST['os'] == 'IOS') {
        $sql_cdb_calls = "select count(call_no) total_calls, sum(call_block) block_calls, sum(call_drop) drop_calls from (select call_no, call_duration, states, if(FIND_IN_SET('3', states), 0, 1) as call_block, if(FIND_IN_SET('3', states), if(call_duration < $drop_call_max_threshold, 1, 0), 0) as call_drop from (select call_no, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration from black_box_datas  where MAKE = 'Apple' and call_no > 0 and CALL_STATE > 0 and $op $ts and $total_calls_bb_ids_cond group by call_no)tbl)tbl2";
    } else {
        $sql_cdb_calls = "select count(call_no) total_calls, sum(call_block) block_calls, sum(call_drop) drop_calls from (select call_no, call_duration, states, if((setup_time is null or setup_time <= 0) and call_duration < $block_call_max_threshold, 1, 0) as call_block, if(call_duration > $block_call_max_threshold and call_duration < $drop_call_max_threshold, 1, 0) as call_drop from (select call_no, sum(setup_time) setup_time, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration from black_box_datas  where MAKE != 'Apple' and call_no > 0 and CALL_STATE > 0 and $op $ts and $total_calls_bb_ids_cond group by call_no)tbl)tbl2";
    }
    error_log($sql_cdb_calls."\n", 3, $log_file);
    $res_cdb_calls = $con->query($sql_cdb_calls);
    $block_calls = $drop_calls = 0;
    if ($res_cdb_calls->num_rows > 0) {
        while ($row=$res_cdb_calls->fetch_assoc()) {
            $block_calls = $row['block_calls'];
            $drop_calls = $row['drop_calls'];
        }
    }
    $call_drop_rate = number_format($drop_calls/($total_calls-$block_calls) * 100, 1) . ' %';
    $call_block_rate = number_format($block_calls/$total_calls * 100, 1) . ' %';
        
    // Mute Calls
    $sql_mute_calls = "select count(distinct call_no) mute_calls, min(MUTE_OF/1000) min_mute_sec, max(MUTE_OF/1000) max_mute_sec, avg(MUTE_OF/1000) avg_mute_sec from black_box_datas WHERE MUTE_ON > 0 and BB_ID in ('" . implode("','", $downlink_mute_bb_ids) . "') AND $op $os $ts UNION ALL select count(distinct call_no) mute_calls, min(MUTE_OF/1000) min_mute_sec, max(MUTE_OF/1000) max_mute_sec, avg(MUTE_OF/1000) avg_mute_sec from black_box_datas WHERE MUTE_ON > 0 and BB_ID in ('" . implode("','", $uplink_mute_bb_ids) . "') AND $op $os $ts";
    error_log($sql_mute_calls."\n", 3, $log_file);
    $res_mute_calls = $con->query($sql_mute_calls);
    $mute_calls_downlink = $mute_calls_uplink = $min_mute_sec_downlink = $max_mute_sec_downlink = $avg_mute_sec_downlink = $min_mute_sec_uplink = $max_mute_sec_uplink = $avg_mute_sec_uplink = 0;
    if ($res_mute_calls->num_rows > 0) {
        $i = 1;
        while ($row=$res_mute_calls->fetch_assoc()) {
            if($i == 1) {
                $mute_calls_downlink = $row['mute_calls'];
                $min_mute_sec_downlink = number_format($row['min_mute_sec'], 1);
                $max_mute_sec_downlink = number_format($row['max_mute_sec'], 1);
                $avg_mute_sec_downlink = number_format($row['avg_mute_sec'], 1);
            } elseif($i == 2) {
                $mute_calls_uplink = $row['mute_calls'];
                $min_mute_sec_uplink = number_format($row['min_mute_sec'], 1);
                $max_mute_sec_uplink = number_format($row['max_mute_sec'], 1);
                $avg_mute_sec_uplink = number_format($row['avg_mute_sec'], 1);
            }
            $i++;
        }
    }
//    $json_array['message']['qry3'] = $sql_mute_calls;
    
    $mute_call_rate_downlink = $mute_call_rate_uplink = 0;
    $mute_call_rate_downlink = number_format($mute_calls_downlink/($total_calls-$block_calls) * 100, 1) . ' %';
    $mute_call_rate_uplink = number_format($mute_calls_uplink/($total_calls-$block_calls) * 100, 1) . ' %';
        
    // Call Connect
    $sql_call_connect = "select count(cst) samples, avg(cst)/1000 as avg_cst, min(cst)/1000 min_cst, max(cst)/1000 max_cst from (select call_no, avg(setup_time) as cst from black_box_datas  where setup_time > 0 and $op $os $ts group by call_no)tbl";
    error_log($sql_call_connect."\n", 3, $log_file);
    $res_call_connect = $con->query($sql_call_connect);    
    $call_connect = $call_connect_samples = $min_call_connect = $max_call_connect = $avg_call_connect = 0;
    if ($res_call_connect->num_rows > 0) {
        while ($row=$res_call_connect->fetch_assoc()) {
            $call_connect = number_format($row['avg_cst'], 1) . ' sec';
            $call_connect_samples = $row['samples'];
            $min_call_connect = number_format($row['min_cst'], 1);
            $max_call_connect = number_format($row['max_cst'], 1);
            $avg_call_connect = number_format($row['avg_cst'], 1);
        }
    }
    
    $json_array['status'] = 200;
    $json_array['message']['call_connect'] = $call_connect;
    $json_array['message']['call_drop_rate'] = $call_drop_rate;
    $json_array['message']['call_block_rate'] = $call_block_rate;
    $json_array['message']['mute_call_rate_downlink'] = $mute_call_rate_downlink;
    $json_array['message']['mute_call_rate_uplink'] = $mute_call_rate_uplink;    
    echo json_encode($json_array);
}

// get_map_details
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_map_details') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = (isset($_POST['city']) && !empty($_POST['city'])) ? strtolower($_POST['city']) : '';
    $trend = isset($_POST['trend']) ? $_POST['trend'] : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    $lat = $lng = '';
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
    
    // Mute Locations Downlink
    $sql_mute_locations = "select lat, lon, FROM_UNIXTIME(SUBSTR(MUTE_ON, 1, 10), '%Y-%m-%d %H:%i:%s') ts, RSRP, SINR, CELL_ID from black_box_datas where MUTE_OF > 0 and BB_ID in ('" . implode("','", $downlink_mute_bb_ids) . "') and lat is not null and lat != '' and lat != '-' and lon is not null and lon != '' and lon != '-' and $op $os $ts";
    error_log($sql_mute_locations."\n", 3, $log_file);
    $res_mute_locations = $con->query($sql_mute_locations);    
    $mute_locations_downlink = false;
    if ($res_mute_locations->num_rows > 0) {
        while ($row=$res_mute_locations->fetch_assoc()) {
            $mute_locations_downlink[] = array('lat' => $row['lat'], 'lon' => $row['lon'], 'ts' => $row['ts'], 'rsrp' => number_format($row['RSRP'], 1), 'sinr' => number_format($row['SINR'], 1), 'cell_id' => $row['CELL_ID']);
        }
    }
    
    // Mute Locations Uplink
    $sql_mute_locations = "select lat, lon, FROM_UNIXTIME(SUBSTR(MUTE_ON, 1, 10), '%Y-%m-%d %H:%i:%s') ts, RSRP, SINR, CELL_ID from black_box_datas where MUTE_OF > 0 and BB_ID in ('" . implode("','", $uplink_mute_bb_ids) . "') and lat is not null and lat != '' and lat != '-' and lon is not null and lon != '' and lon != '-' and $op $os $ts";
    error_log($sql_mute_locations."\n", 3, $log_file);
    $res_mute_locations = $con->query($sql_mute_locations);    
    $mute_locations_uplink = false;
    if ($res_mute_locations->num_rows > 0) {
        while ($row=$res_mute_locations->fetch_assoc()) {
            $mute_locations_uplink[] = array('lat' => $row['lat'], 'lon' => $row['lon'], 'ts' => $row['ts'], 'rsrp' => number_format($row['RSRP'], 1), 'sinr' => number_format($row['SINR'], 1), 'cell_id' => $row['CELL_ID']);
        }
    }
    
    // Drop & Block Locations
    if(isset($_POST['os']) && strtolower($_POST['os']) == 'ios') {
        $sql_cdb_locations = "select call_no, call_duration, lat, lon, ts, RSRP, SINR, CELL_ID, states, if(FIND_IN_SET('3', states), 0, 1) as call_block, if(FIND_IN_SET('3', states), if(call_duration < $drop_call_max_threshold, 1, 0), 0) as call_drop from (select call_no, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration, avg(lat) lat, avg(lon) lon, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10), '%Y-%m-%d %H:%i:%s'))) ts, avg(RSRP) RSRP, avg(SINR) SINR, max(CELL_ID) CELL_ID from black_box_datas  where MAKE = 'Apple' and call_no > 0 and CALL_STATE > 0 and lat is not null and lat != '' and lat != '-' and lon is not null and lon != '' and lon != '-' and $op $ts group by call_no)tbl";
    } elseif(isset($_POST['os']) && strtolower($_POST['os']) == 'android') {
        $sql_cdb_locations = "select call_no, call_duration, lat, lon, ts, RSRP, SINR, CELL_ID, states, if((setup_time is null or setup_time <= 0) and call_duration < $block_call_max_threshold, 1, 0) as call_block, if(call_duration > $block_call_max_threshold and call_duration < $drop_call_max_threshold, 1, 0) as call_drop from (select call_no, sum(setup_time) setup_time, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration, avg(lat) lat, avg(lon) lon, FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10), '%Y-%m-%d %H:%i:%s') ts, avg(RSRP) RSRP, avg(SINR) SINR, max(CELL_ID) CELL_ID from black_box_datas  where MAKE != 'Apple' and call_no > 0 and CALL_STATE > 0 and lat is not null and lat != '' and lat != '-' and lon is not null and lon != '' and lon != '-' and $op $ts group by call_no)tbl";
    } else {
        $sql_cdb_locations = "select call_no, call_duration, lat, lon, ts, RSRP, SINR, CELL_ID, states, if(call_duration < $block_call_max_threshold, 1, 0) as call_block, if(call_duration > $block_call_max_threshold and call_duration < $drop_call_max_threshold, 1, 0) as call_drop from (select call_no, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration, avg(lat) lat, avg(lon) lon, FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10), '%Y-%m-%d %H:%i:%s') ts, avg(RSRP) RSRP, avg(SINR) SINR, max(CELL_ID) CELL_ID from black_box_datas  where call_no > 0 and CALL_STATE > 0 and lat is not null and lat != '' and lat != '-' and lon is not null and lon != '' and lon != '-' and $op $ts group by call_no)tbl";
    }
    error_log($sql_cdb_locations."\n", 3, $log_file);
    $res_cdb_locations = $con->query($sql_cdb_locations);    
    $drop_locations = $block_locations = false;
    if ($res_cdb_locations->num_rows > 0) {
        while ($row=$res_cdb_locations->fetch_assoc()) {
            if($row['call_block']) {
                $block_locations[] = array('lat' => $row['lat'], 'lon' => $row['lon'], 'ts' => $row['ts'], 'rsrp' => number_format($row['RSRP'], 1), 'sinr' => number_format($row['SINR'], 1), 'cell_id' => round($row['CELL_ID']));
            } elseif($row['call_drop']) {
                $drop_locations[] = array('lat' => $row['lat'], 'lon' => $row['lon'], 'ts' => $row['ts'], 'rsrp' => number_format($row['RSRP'], 1), 'sinr' => number_format($row['SINR'], 1), 'cell_id' => round($row['CELL_ID']));
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

// get_call_connect_trend_details
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_call_connect_trend_details') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = (isset($_POST['city']) && !empty($_POST['city'])) ? strtolower($_POST['city']) : '';
    $trend = isset($_POST['trend']) ? $_POST['trend'] : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    $lat = $lng = '';
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
    
    // Get Call Connect Time average
    if($trend == 'day' || $trend == 'custom') {
        $no_of_days_or_hr = 31;
        $sql_call_connect_time_wise = "select day(FROM_UNIXTIME(SUBSTR(TIMESTAMP, 1, 10)+19800)) day, avg(setup_time)/1000 as avg_cst from black_box_datas  where setup_time > 0 and $op $os $ts and $total_calls_bb_ids_cond group by day";
    } elseif($trend == 'hour') {
        $no_of_days_or_hr = 24;
        $sql_call_connect_time_wise = "select hour(FROM_UNIXTIME(SUBSTR(TIMESTAMP, 1, 10)+19800)) hour, avg(setup_time)/1000 as avg_cst from black_box_datas  where setup_time > 0 and $op $os $ts and $total_calls_bb_ids_cond group by hour";
    }
    error_log($sql_call_connect_time_wise."\n", 3, $log_file);
    $res_call_connect_time_wise = $con->query($sql_call_connect_time_wise);
    $call_connect_time_wise = false;
    if ($res_call_connect_time_wise->num_rows > 0) {
        while ($row=$res_call_connect_time_wise->fetch_assoc()) {
            if($trend == 'day' || $trend == 'custom') {
                $call_connect_time_wise[$row['day']] = number_format($row['avg_cst'], 1);
            } elseif($trend == 'hour') {
                $call_connect_time_wise[$row['hour']] = number_format($row['avg_cst'], 1);
            }
        }
    }
    
    for($i=0; $i<$no_of_days_or_hr; $i++) {
        if(!array_key_exists($i, $call_connect_time_wise)) {
            $call_connect_time_wise[$i] = 0;
        }
    }
    
    $json_array['status'] = 200;
    $json_array['message']['call_connect_time_wise'] = $call_connect_time_wise;
    echo json_encode($json_array);
}

// get_block_call_trend_details
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_block_call_trend_details') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = (isset($_POST['city']) && !empty($_POST['city'])) ? strtolower($_POST['city']) : '';
    $trend = isset($_POST['trend']) ? $_POST['trend'] : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    $lat = $lng = '';
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
    
    // Total Calls
    $sql_total_calls = "select count(distinct call_no) total_calls from black_box_datas WHERE CALL_STATE = 2 AND $op $os $ts and $total_calls_bb_ids_cond";
    error_log($sql_total_calls."\n", 3, $log_file);
    $res_total_calls = $con->query($sql_total_calls);    
    $total_calls = 0;
    if ($res_total_calls->num_rows > 0) {
        while ($row=$res_total_calls->fetch_assoc()) {
            $total_calls = $row['total_calls'];
        }
    }
    
    // Get Block Call Count
    if($trend == 'day' || $trend == 'custom') {
        $no_of_days_or_hr = 31;
        $sql_block_time_wise = "select count(call_no) block_calls, day from (select call_no, day(ts) day, if((setup_time is null or setup_time <= 0) and call_duration < $block_call_max_threshold, 1, 0) as call_block from (select call_no, sum(setup_time) setup_time, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration, max(FROM_UNIXTIME(SUBSTR(TIMESTAMP, 1, 10)+19800)) ts from black_box_datas  where call_no > 0 and CALL_STATE > 0 and $op $os $ts and $total_calls_bb_ids_cond group by call_no)tbl)tbl2 where call_block = 1 group by day";
    } elseif($trend == 'hour') {
        $no_of_days_or_hr = 24;
        $sql_block_time_wise = "select count(call_no) block_calls, hour from (select call_no, hour(ts) hour, if((setup_time is null or setup_time <= 0) and call_duration < $block_call_max_threshold, 1, 0) as call_block from (select call_no, sum(setup_time) setup_time, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration, max(FROM_UNIXTIME(SUBSTR(TIMESTAMP, 1, 10)+19800)) ts from black_box_datas  where call_no > 0 and CALL_STATE > 0 and $op $os $ts and $total_calls_bb_ids_cond group by call_no)tbl)tbl2 where call_block = 1 group by hour";
    }
    error_log($sql_block_time_wise."\n", 3, $log_file);
    $res_block_time_wise = $con->query($sql_block_time_wise);
    $block_time_wise = false;
    if ($res_block_time_wise->num_rows > 0) {
        while ($row=$res_block_time_wise->fetch_assoc()) {
            if($trend == 'day' || $trend == 'custom') {
                $block_time_wise[$row['day']] = number_format(($row['block_calls']/$total_calls) * 100, 1);
            } elseif($trend == 'hour') {
                $block_time_wise[$row['hour']] = number_format(($row['block_calls']/$total_calls) * 100, 1);
            }
        }
    }
    
    for($i=0; $i<$no_of_days_or_hr; $i++) {
        if(!array_key_exists($i, $block_time_wise)) {
            $block_time_wise[$i] = 0;
        }
    }
    
    $json_array['status'] = 200;
    $json_array['message']['call_block_time_wise'] = $block_time_wise;
    echo json_encode($json_array);
}

// get_drop_call_trend_details
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_drop_call_trend_details') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = (isset($_POST['city']) && !empty($_POST['city'])) ? strtolower($_POST['city']) : '';
    $trend = isset($_POST['trend']) ? $_POST['trend'] : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    $lat = $lng = '';
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
    
    // Total Calls
    $sql_total_calls = "select count(distinct call_no) total_calls from black_box_datas WHERE CALL_STATE = 2 AND $op $os $ts and $total_calls_bb_ids_cond";
    error_log($sql_total_calls."\n", 3, $log_file);
    $res_total_calls = $con->query($sql_total_calls);    
    $total_calls = 0;
    if ($res_total_calls->num_rows > 0) {
        while ($row=$res_total_calls->fetch_assoc()) {
            $total_calls = $row['total_calls'];
        }
    }
    
    // Drop & Block Calls
    if(isset($_POST['os']) && $_POST['os'] == 'IOS') {
        $sql_cdb_calls = "select count(call_no) total_calls, sum(call_block) block_calls, sum(call_drop) drop_calls from (select call_no, call_duration, states, if(FIND_IN_SET('3', states), 0, 1) as call_block, if(FIND_IN_SET('3', states), if(call_duration < $drop_call_max_threshold, 1, 0), 0) as call_drop from (select call_no, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration from black_box_datas  where MAKE = 'Apple' and call_no > 0 and CALL_STATE > 0 and $op $ts and $total_calls_bb_ids_cond group by call_no)tbl)tbl2";
    } else {
        $sql_cdb_calls = "select count(call_no) total_calls, sum(call_block) block_calls, sum(call_drop) drop_calls from (select call_no, call_duration, states, if((setup_time is null or setup_time <= 0) and call_duration < $block_call_max_threshold, 1, 0) as call_block, if(call_duration > $block_call_max_threshold and call_duration < $drop_call_max_threshold, 1, 0) as call_drop from (select call_no, sum(setup_time) setup_time, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration from black_box_datas  where MAKE != 'Apple' and call_no > 0 and CALL_STATE > 0 and $op $ts and $total_calls_bb_ids_cond group by call_no)tbl)tbl2";
    }
    error_log($sql_cdb_calls."\n", 3, $log_file);
    $res_cdb_calls = $con->query($sql_cdb_calls);
    $block_calls = $drop_calls = 0;
    if ($res_cdb_calls->num_rows > 0) {
        while ($row=$res_cdb_calls->fetch_assoc()) {
            $block_calls = $row['block_calls'];
            $drop_calls = $row['drop_calls'];
        }
    }
    
    // Get Drop Call Count
    if($trend == 'day' || $trend == 'custom') {
        $no_of_days_or_hr = 31;
        $sql_drop_time_wise = "select count(call_no) drop_calls, day from (select call_no, day(ts) day, if(call_duration > $block_call_max_threshold and call_duration < $drop_call_max_threshold, 1, 0) as call_drop from (select call_no, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration, max(FROM_UNIXTIME(SUBSTR(TIMESTAMP, 1, 10)+19800)) ts from black_box_datas  where call_no > 0 and CALL_STATE > 0 and $op $os $ts and $total_calls_bb_ids_cond group by call_no)tbl)tbl2 where call_drop = 1 group by day";
    } elseif($trend == 'hour') {
        $no_of_days_or_hr = 24;
        $sql_drop_time_wise = "select count(call_no) drop_calls, hour from (select call_no, hour(ts) hour, if(call_duration > $block_call_max_threshold and call_duration < $drop_call_max_threshold, 1, 0) as call_drop from (select call_no, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration, max(FROM_UNIXTIME(SUBSTR(TIMESTAMP, 1, 10)+19800)) ts from black_box_datas  where call_no > 0 and CALL_STATE > 0 and $op $os $ts and $total_calls_bb_ids_cond group by call_no)tbl)tbl2 where call_drop = 1 group by hour";
    }
    error_log($sql_drop_time_wise."\n", 3, $log_file);
    $res_drop_time_wise = $con->query($sql_drop_time_wise);
    $drop_time_wise = false;
    if ($res_drop_time_wise->num_rows > 0) {
        while ($row=$res_drop_time_wise->fetch_assoc()) {
            if($trend == 'day' || $trend == 'custom') {
                $drop_time_wise[$row['day']] = number_format(($row['drop_calls']/($total_calls-$block_calls)) * 100, 1);
            } elseif($trend == 'hour') {
                $drop_time_wise[$row['hour']] = number_format(($row['drop_calls']/($total_calls-$block_calls)) * 100, 1);
            }
        }
    }
    
    for($i=0; $i<$no_of_days_or_hr; $i++) {
        if(!array_key_exists($i, $drop_time_wise)) {
            $drop_time_wise[$i] = 0;
        }
    }
    
    $json_array['status'] = 200;
    $json_array['message']['call_drop_time_wise'] = $drop_time_wise;
    echo json_encode($json_array);
}

// get_mute_call_trend_details
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_mute_call_trend_details') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = (isset($_POST['city']) && !empty($_POST['city'])) ? strtolower($_POST['city']) : '';
    $trend = isset($_POST['trend']) ? $_POST['trend'] : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    $lat = $lng = '';
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
    
    // Total Calls
    $sql_total_calls = "select count(distinct call_no) total_calls from black_box_datas WHERE CALL_STATE = 2 AND $op $os $ts and $total_calls_bb_ids_cond";
    error_log($sql_total_calls."\n", 3, $log_file);
    $res_total_calls = $con->query($sql_total_calls);    
    $total_calls = 0;
    if ($res_total_calls->num_rows > 0) {
        while ($row=$res_total_calls->fetch_assoc()) {
            $total_calls = $row['total_calls'];
        }
    }
    
    // Get Block Call Count
    if($trend == 'day' || $trend == 'custom') {
        $no_of_days_or_hr = 31;
        $sql_block_time_wise = "select count(call_no) block_calls, day from (select call_no, day(ts) day, if((setup_time is null or setup_time <= 0) and call_duration < $block_call_max_threshold, 1, 0) as call_block from (select call_no, sum(setup_time) setup_time, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration, max(FROM_UNIXTIME(SUBSTR(TIMESTAMP, 1, 10)+19800)) ts from black_box_datas  where call_no > 0 and CALL_STATE > 0 and $op $os $ts and $total_calls_bb_ids_cond group by call_no)tbl)tbl2 where call_block = 1 group by day";
    } elseif($trend == 'hour') {
        $no_of_days_or_hr = 24;
        $sql_block_time_wise = "select count(call_no) block_calls, hour from (select call_no, hour(ts) hour, if((setup_time is null or setup_time <= 0) and call_duration < $block_call_max_threshold, 1, 0) as call_block from (select call_no, sum(setup_time) setup_time, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration, max(FROM_UNIXTIME(SUBSTR(TIMESTAMP, 1, 10)+19800)) ts from black_box_datas  where call_no > 0 and CALL_STATE > 0 and $op $os $ts and $total_calls_bb_ids_cond group by call_no)tbl)tbl2 where call_block = 1 group by hour";
    }
    error_log($sql_block_time_wise."\n", 3, $log_file);
    $res_block_time_wise = $con->query($sql_block_time_wise);
    $block_time_wise = false;
    $block_calls = 0;
    if ($res_block_time_wise->num_rows > 0) {
        while ($row=$res_block_time_wise->fetch_assoc()) {
            if($trend == 'day' || $trend == 'custom') {
                $block_time_wise[$row['day']] = $row['block_calls'];
            } elseif($trend == 'hour') {
                $block_time_wise[$row['hour']] = $row['block_calls'];
            }
        }
    }
    
    for($i=0; $i<$no_of_days_or_hr; $i++) {
        if(!array_key_exists($i, $block_time_wise)) {
            $block_time_wise[$i] = 0;
        }
        $block_calls += $block_time_wise[$i];
    }
    
    // Mute Calls
    $sql_mute_calls = "select count(distinct call_no) mute_calls, min(MUTE_OF/1000) min_mute_sec, max(MUTE_OF/1000) max_mute_sec, avg(MUTE_OF/1000) avg_mute_sec from black_box_datas WHERE MUTE_ON > 0 and BB_ID in ('" . implode("','", $downlink_mute_bb_ids) . "') AND $op $os $ts UNION ALL select count(distinct call_no) mute_calls, min(MUTE_OF/1000) min_mute_sec, max(MUTE_OF/1000) max_mute_sec, avg(MUTE_OF/1000) avg_mute_sec from black_box_datas WHERE MUTE_ON > 0 and BB_ID in ('" . implode("','", $uplink_mute_bb_ids) . "') AND $op $os $ts";
    error_log($sql_mute_calls."\n", 3, $log_file);
    $res_mute_calls = $con->query($sql_mute_calls);
    $mute_calls_downlink = $mute_calls_uplink = $min_mute_sec_downlink = $max_mute_sec_downlink = $avg_mute_sec_downlink = $min_mute_sec_uplink = $max_mute_sec_uplink = $avg_mute_sec_uplink = 0;
    if ($res_mute_calls->num_rows > 0) {
        $i = 1;
        while ($row=$res_mute_calls->fetch_assoc()) {
            if($i == 1) {
                $mute_calls_downlink = $row['mute_calls'];
                $min_mute_sec_downlink = number_format($row['min_mute_sec'], 1);
                $max_mute_sec_downlink = number_format($row['max_mute_sec'], 1);
                $avg_mute_sec_downlink = number_format($row['avg_mute_sec'], 1);
            } elseif($i == 2) {
                $mute_calls_uplink = $row['mute_calls'];
                $min_mute_sec_uplink = number_format($row['min_mute_sec'], 1);
                $max_mute_sec_uplink = number_format($row['max_mute_sec'], 1);
                $avg_mute_sec_uplink = number_format($row['avg_mute_sec'], 1);
            }
            $i++;
        }
    }
    
    // Get Mute count Downlink
    if($trend == 'day' || $trend == 'custom') {
        $no_of_days_or_hr = 31;
        $sql_mute_calls_time_wise = "select count(DISTINCT IF(MUTE_ON > 0 and BB_ID in ('" . implode("','", $downlink_mute_bb_ids) . "'), call_no, NULL)) as mute_calls, count(DISTINCT IF(CALL_STATE > 0 and $total_calls_bb_ids_cond, call_no, NULL)) as total_calls, day(FROM_UNIXTIME(SUBSTR(TIMESTAMP, 1, 10)+19800)) day from black_box_datas  WHERE $op $os $ts group by day";
    } elseif($trend == 'hour') {
        $no_of_days_or_hr = 24;
        $sql_mute_calls_time_wise = "select count(DISTINCT IF(MUTE_ON > 0 and BB_ID in ('" . implode("','", $downlink_mute_bb_ids) . "'), call_no, NULL)) as mute_calls, count(DISTINCT IF(CALL_STATE > 0 and $total_calls_bb_ids_cond, call_no, NULL)) as total_calls, hour(FROM_UNIXTIME(SUBSTR(TIMESTAMP, 1, 10)+19800)) hour from black_box_datas  WHERE $op $os $ts group by hour";
    }
    error_log($sql_mute_calls_time_wise."\n", 3, $log_file);
    $res_mute_calls_time_wise = $con->query($sql_mute_calls_time_wise);
    $mute_calls_time_wise_downlink = false;
    if ($res_mute_calls_time_wise->num_rows > 0) {
        while ($row=$res_mute_calls_time_wise->fetch_assoc()) {
            if($trend == 'day' || $trend == 'custom') {
                $mute_calls_time_wise_downlink[$row['day']] = number_format($row['mute_calls']/($row['total_calls']-$block_time_wise[$row['day']]) * 100, 1);
            } elseif($trend == 'hour') {
                $mute_calls_time_wise_downlink[$row['hour']] = number_format($row['mute_calls']/($row['total_calls']-$block_time_wise[$row['hour']]) * 100, 1);
            }
        }
    }
    
    for($i=0; $i<$no_of_days_or_hr; $i++) {
        if(!array_key_exists($i, $mute_calls_time_wise_downlink)) {
            $mute_calls_time_wise_downlink[$i] = 0;
        }
    }
    
    // Get Mute count Uplink
    if($trend == 'day' || $trend == 'custom') {
        $no_of_days_or_hr = 31;
        $sql_mute_calls_time_wise = "select count(DISTINCT IF(MUTE_ON > 0 and BB_ID in ('" . implode("','", $uplink_mute_bb_ids) . "'), call_no, NULL)) as mute_calls, count(DISTINCT IF(CALL_STATE > 0 and $total_calls_bb_ids_cond, call_no, NULL)) as total_calls, day(FROM_UNIXTIME(SUBSTR(TIMESTAMP, 1, 10)+19800)) day from black_box_datas  WHERE $op $os $ts group by day";
    } elseif($trend == 'hour') {
        $no_of_days_or_hr = 24;
        $sql_mute_calls_time_wise = "select count(DISTINCT IF(MUTE_ON > 0 and BB_ID in ('" . implode("','", $uplink_mute_bb_ids) . "'), call_no, NULL)) as mute_calls, count(DISTINCT IF(CALL_STATE > 0 and $total_calls_bb_ids_cond, call_no, NULL)) as total_calls, hour(FROM_UNIXTIME(SUBSTR(TIMESTAMP, 1, 10)+19800)) hour from black_box_datas  WHERE $op $os $ts group by hour";
    }
    error_log($sql_mute_calls_time_wise."\n", 3, $log_file);
    $res_mute_calls_time_wise = $con->query($sql_mute_calls_time_wise);
    $mute_calls_time_wise_uplink = false;
    if ($res_mute_calls_time_wise->num_rows > 0) {
        while ($row=$res_mute_calls_time_wise->fetch_assoc()) {
            if($trend == 'day' || $trend == 'custom') {
                $mute_calls_time_wise_uplink[$row['day']] = number_format($row['mute_calls']/($row['total_calls']-$block_time_wise[$row['day']]) * 100, 1);
            } elseif($trend == 'hour') {
                $mute_calls_time_wise_uplink[$row['hour']] = number_format($row['mute_calls']/($row['total_calls']-$block_time_wise[$row['hour']]) * 100, 1);
            }
        }
    }
    
    for($i=0; $i<$no_of_days_or_hr; $i++) {
        if(!array_key_exists($i, $mute_calls_time_wise_uplink)) {
            $mute_calls_time_wise_uplink[$i] = 0;
        }
    }
    
    $json_array['status'] = 200;
    $json_array['message']['total_calls'] = $total_calls - $block_calls;
    $json_array['message']['mute_calls_downlink'] = $mute_calls_downlink;
    $json_array['message']['mute_calls_uplink'] = $mute_calls_uplink;
    $json_array['message']['min_mute_sec_downlink'] = $min_mute_sec_downlink;
    $json_array['message']['max_mute_sec_downlink'] = $max_mute_sec_downlink;
    $json_array['message']['avg_mute_sec_downlink'] = $avg_mute_sec_downlink;
    $json_array['message']['min_mute_sec_uplink'] = $min_mute_sec_uplink;
    $json_array['message']['max_mute_sec_uplink'] = $max_mute_sec_uplink;
    $json_array['message']['avg_mute_sec_uplink'] = $avg_mute_sec_uplink;
    $json_array['message']['mute_calls_time_wise_downlink'] = $mute_calls_time_wise_downlink;
    $json_array['message']['mute_calls_time_wise_uplink'] = $mute_calls_time_wise_uplink;
    echo json_encode($json_array);
}

// get_mute_duration_details
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_mute_duration_details') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = (isset($_POST['city']) && !empty($_POST['city'])) ? strtolower($_POST['city']) : '';
    $trend = isset($_POST['trend']) ? $_POST['trend'] : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    $lat = $lng = '';
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
    
    // Total Calls
    $sql_total_calls = "select count(distinct call_no) total_calls from black_box_datas WHERE CALL_STATE = 2 AND $op $os $ts and $total_calls_bb_ids_cond";
    error_log($sql_total_calls."\n", 3, $log_file);
    $res_total_calls = $con->query($sql_total_calls);    
    $total_calls = 0;
    if ($res_total_calls->num_rows > 0) {
        while ($row=$res_total_calls->fetch_assoc()) {
            $total_calls = $row['total_calls'];
        }
    }
    
    // Mute Calls
    $sql_mute_calls = "select count(distinct call_no) mute_calls, min(MUTE_OF/1000) min_mute_sec, max(MUTE_OF/1000) max_mute_sec, avg(MUTE_OF/1000) avg_mute_sec from black_box_datas WHERE MUTE_ON > 0 and BB_ID in ('" . implode("','", $downlink_mute_bb_ids) . "') AND $op $os $ts UNION ALL select count(distinct call_no) mute_calls, min(MUTE_OF/1000) min_mute_sec, max(MUTE_OF/1000) max_mute_sec, avg(MUTE_OF/1000) avg_mute_sec from black_box_datas WHERE MUTE_ON > 0 and BB_ID in ('" . implode("','", $uplink_mute_bb_ids) . "') AND $op $os $ts";
    error_log($sql_mute_calls."\n", 3, $log_file);
    $res_mute_calls = $con->query($sql_mute_calls);
    $mute_calls_downlink = $mute_calls_uplink = $min_mute_sec_downlink = $max_mute_sec_downlink = $avg_mute_sec_downlink = $min_mute_sec_uplink = $max_mute_sec_uplink = $avg_mute_sec_uplink = 0;
    if ($res_mute_calls->num_rows > 0) {
        $i = 1;
        while ($row=$res_mute_calls->fetch_assoc()) {
            if($i == 1) {
                $mute_calls_downlink = $row['mute_calls'];
                $min_mute_sec_downlink = number_format($row['min_mute_sec'], 1);
                $max_mute_sec_downlink = number_format($row['max_mute_sec'], 1);
                $avg_mute_sec_downlink = number_format($row['avg_mute_sec'], 1);
            } elseif($i == 2) {
                $mute_calls_uplink = $row['mute_calls'];
                $min_mute_sec_uplink = number_format($row['min_mute_sec'], 1);
                $max_mute_sec_uplink = number_format($row['max_mute_sec'], 1);
                $avg_mute_sec_uplink = number_format($row['avg_mute_sec'], 1);
            }
            $i++;
        }
    }
    
    // Mute Duration Histogram Downlink
    $sql_mute_duration_histogram = "select min(total_mute_duration) min_mute_duration, max(total_mute_duration) max_mute_duration, avg(total_mute_duration) avg_mute_duration, sum(IF(total_mute_duration >= 0 and total_mute_duration < 2, 1, 0)) AS range_1, sum(IF(total_mute_duration >= 2 and total_mute_duration < 4, 1, 0)) AS range_2, sum(IF(total_mute_duration >= 4 and total_mute_duration < 6, 1, 0)) AS range_3, sum(IF(total_mute_duration >= 6 and total_mute_duration < 8, 1, 0)) AS range_4, sum(IF(total_mute_duration >= 8 and total_mute_duration < 10, 1, 0)) AS range_5, sum(IF(total_mute_duration >= 10 and total_mute_duration < 12, 1, 0)) AS range_6, sum(IF(total_mute_duration >= 12 and total_mute_duration < 14, 1, 0)) AS range_7, sum(IF(total_mute_duration >= 14 and total_mute_duration < 16, 1, 0)) AS range_8, sum(IF(total_mute_duration >= 16, 1, 0)) AS range_9 from (select call_no, sum(MUTE_OF)/1000 as total_mute_duration from black_box_datas  where MUTE_OF > 0 and BB_ID in ('" . implode("','", $downlink_mute_bb_ids) . "') AND $op $os $ts group by call_no)tbl";
    error_log($sql_mute_duration_histogram."\n", 3, $log_file);
    $res_mute_duration_histogram = $con->query($sql_mute_duration_histogram);    
    $total_mute_calls_downlink = $min_mute_duration_downlink = $max_mute_duration_downlink = $avg_mute_duration_downlink = 0;
    if ($res_mute_duration_histogram->num_rows > 0) {
        while ($row=$res_mute_duration_histogram->fetch_assoc()) {
            for($range=1; $range<=9; $range++) {
                $mute_duration_histogram_downlink[] = $row['range_'.$range];
            }
            $min_mute_duration_downlink = number_format($row['min_mute_duration'], 1);
            $max_mute_duration_downlink = number_format($row['max_mute_duration'], 1);
            $avg_mute_duration_downlink = number_format($row['avg_mute_duration'], 1);
        }
    } else {
        $mute_duration_histogram_downlink = [0, 0, 0, 0, 0, 0, 0, 0, 0];
    }
    
    // Mute Duration Histogram Uplink
    $sql_mute_duration_histogram = "select min(total_mute_duration) min_mute_duration, max(total_mute_duration) max_mute_duration, avg(total_mute_duration) avg_mute_duration, sum(IF(total_mute_duration >= 0 and total_mute_duration < 2, 1, 0)) AS range_1, sum(IF(total_mute_duration >= 2 and total_mute_duration < 4, 1, 0)) AS range_2, sum(IF(total_mute_duration >= 4 and total_mute_duration < 6, 1, 0)) AS range_3, sum(IF(total_mute_duration >= 6 and total_mute_duration < 8, 1, 0)) AS range_4, sum(IF(total_mute_duration >= 8 and total_mute_duration < 10, 1, 0)) AS range_5, sum(IF(total_mute_duration >= 10 and total_mute_duration < 12, 1, 0)) AS range_6, sum(IF(total_mute_duration >= 12 and total_mute_duration < 14, 1, 0)) AS range_7, sum(IF(total_mute_duration >= 14 and total_mute_duration < 16, 1, 0)) AS range_8, sum(IF(total_mute_duration >= 16, 1, 0)) AS range_9 from (select call_no, sum(MUTE_OF)/1000 as total_mute_duration from black_box_datas  where MUTE_OF > 0 and BB_ID in ('" . implode("','", $uplink_mute_bb_ids) . "') AND $op $os $ts group by call_no)tbl";
    error_log($sql_mute_duration_histogram."\n", 3, $log_file);
    $res_mute_duration_histogram = $con->query($sql_mute_duration_histogram);    
    $total_mute_calls_uplink = $min_mute_duration_uplink = $max_mute_duration_uplink = $avg_mute_duration_uplink = 0;
    if ($res_mute_duration_histogram->num_rows > 0) {
        while ($row=$res_mute_duration_histogram->fetch_assoc()) {
            for($range=1; $range<=9; $range++) {
                $mute_duration_histogram_uplink[] = $row['range_'.$range];
            }
            $min_mute_duration_uplink = number_format($row['min_mute_duration'], 1);
            $max_mute_duration_uplink = number_format($row['max_mute_duration'], 1);
            $avg_mute_duration_uplink = number_format($row['avg_mute_duration'], 1);
        }
    } else {
        $mute_duration_histogram_uplink = [0, 0, 0, 0, 0, 0, 0, 0, 0];
    }
    
    $json_array['status'] = 200;
    $json_array['message']['mute_calls_downlink'] = $mute_calls_downlink;
    $json_array['message']['mute_calls_uplink'] = $mute_calls_uplink;
    $json_array['message']['mute_duration_histogram_downlink'] = $mute_duration_histogram_downlink;
    $json_array['message']['mute_duration_histogram_uplink'] = $mute_duration_histogram_uplink;
    $json_array['message']['min_mute_duration_uplink'] = $min_mute_duration_uplink;
    $json_array['message']['max_mute_duration_uplink'] = $max_mute_duration_uplink;
    $json_array['message']['avg_mute_duration_uplink'] = $avg_mute_duration_uplink;
    $json_array['message']['min_mute_duration_downlink'] = $min_mute_duration_downlink;
    $json_array['message']['max_mute_duration_downlink'] = $max_mute_duration_downlink;
    $json_array['message']['avg_mute_duration_downlink'] = $avg_mute_duration_downlink;
    echo json_encode($json_array);
}

// get_cov_qua_mute_details
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_cov_qua_mute_details') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = (isset($_POST['city']) && !empty($_POST['city'])) ? strtolower($_POST['city']) : '';
    $trend = isset($_POST['trend']) ? $_POST['trend'] : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    $lat = $lng = '';
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
        
    // Coverage V/S Quality V/S Mute
//    $sql_cov_qua_mute = "select call_no, RSRP, SINR, MUTE_OF/1000 as mute_duration from black_box_datas where MUTE_OF > 0 and BB_ID in ('" . implode("','", $downlink_mute_bb_ids) . "') and RSRP is not null and RSRP != '' and RSRP != '-' and SINR is not null and SINR != '' and SINR != '-' AND $op $os $ts";
    $sql_cov_qua_mute = "select avg(RSRP) RSRP, avg(SINR) SINR, sum(MUTE_OF)/1000 as mute_duration, mute_no from black_box_datas  where is_mute = 1 and mute_no > 0 and BB_ID in ('" . implode("','", $downlink_mute_bb_ids) . "') and RSRP REGEXP '[0-9]' and SINR REGEXP '[0-9]' AND $op $os $ts group by mute_no";
    error_log($sql_cov_qua_mute."\n", 3, $log_file);
    $res_cov_qua_mute = $con->query($sql_cov_qua_mute);
    $mute_samples = $min_mute_duration_cov = $max_mute_duration_cov = $avg_mute_duration_cov = 0;
    if ($res_cov_qua_mute->num_rows > 0) {
        while ($row=$res_cov_qua_mute->fetch_assoc()) {
            if($row['mute_duration'] >= 0 and $row['mute_duration'] <= 2) {
                $cov_qua[0][] = $row['RSRP'] . ',' . $row['SINR'];
            } elseif($row['mute_duration'] > 2 and $row['mute_duration'] <= 5) {
                $cov_qua[1][] = $row['RSRP'] . ',' . $row['SINR'];
            } elseif($row['mute_duration'] > 5 and $row['mute_duration'] <= 10) {
                $cov_qua[2][] = $row['RSRP'] . ',' . $row['SINR'];
            } elseif($row['mute_duration'] > 10 and $row['mute_duration'] <= 15) {
                $cov_qua[3][] = $row['RSRP'] . ',' . $row['SINR'];
            } elseif($row['mute_duration'] > 15) {
                $cov_qua[4][] = $row['RSRP'] . ',' . $row['SINR'];
            }
        }
        
        for($range=0; $range<5; $range++) {
            if(!isset($cov_qua[$range])) {
                $cov_qua[$range][] = '0,0';
            }
        }
    } else {
        for($range=0; $range<5; $range++) {
            if(!isset($cov_qua[$range])) {
                $cov_qua[$range][] = '0,0';
            }
        }
    }
    
//    $sql_cov_qua_mute = "select count(1) as mute_samples, min(mute_duration) min_mute_duration, max(mute_duration) max_mute_duration, avg(mute_duration) avg_mute_duration from (select call_no, RSRP, SINR, MUTE_OF/1000 as mute_duration from black_box_datas where MUTE_OF > 0 and BB_ID in ('" . implode("','", $downlink_mute_bb_ids) . "') and RSRP is not null and RSRP != '' and RSRP != '-' and SINR is not null and SINR != '' and SINR != '-' AND $op $os $ts)tbl";
    $sql_cov_qua_mute = "select count(1) as mute_samples, min(mute_duration) min_mute_duration, max(mute_duration) max_mute_duration, avg(mute_duration) avg_mute_duration from (select mute_no, sum(MUTE_OF)/1000 as mute_duration from black_box_datas  where is_mute = 1 and mute_no > 0 and BB_ID in ('" . implode("','", $downlink_mute_bb_ids) . "') and RSRP REGEXP '[0-9]' and SINR REGEXP '[0-9]' AND $op $os $ts group by mute_no)tbl";
    error_log($sql_cov_qua_mute."\n", 3, $log_file);
    $res_cov_qua_mute = $con->query($sql_cov_qua_mute);    
    $mute_samples = $min_mute_duration_cov = $max_mute_duration_cov = $avg_mute_duration_cov = 0;
    if ($res_cov_qua_mute->num_rows > 0) {
        while ($row=$res_cov_qua_mute->fetch_assoc()) {
            $mute_samples = $row['mute_samples'];
            $min_mute_duration_cov = number_format($row['min_mute_duration'], 1);
            $max_mute_duration_cov = number_format($row['max_mute_duration'], 1);
            $avg_mute_duration_cov = number_format($row['avg_mute_duration'], 1);
        }
    }
    
    $json_array['status'] = 200;
    $json_array['message']['cov_qua_mute'] = $cov_qua;
    $json_array['message']['mute_samples'] = $mute_samples;
    $json_array['message']['min_mute_duration_cov'] = $min_mute_duration_cov;
    $json_array['message']['max_mute_duration_cov'] = $max_mute_duration_cov;
    $json_array['message']['avg_mute_duration_cov'] = $avg_mute_duration_cov;
    echo json_encode($json_array);
}

// get_call_connect_histogram_details
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_call_connect_histogram_details') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = (isset($_POST['city']) && !empty($_POST['city'])) ? strtolower($_POST['city']) : '';
    $trend = isset($_POST['trend']) ? $_POST['trend'] : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    $lat = $lng = '';
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
        
    // Call Connect
    $sql_call_connect = "select count(cst) samples, avg(cst)/1000 as avg_cst, min(cst)/1000 min_cst, max(cst)/1000 max_cst from (select call_no, avg(setup_time) as cst from black_box_datas  where setup_time > 0 and $op $os $ts group by call_no)tbl";
    error_log($sql_call_connect."\n", 3, $log_file);
    $res_call_connect = $con->query($sql_call_connect);    
    $call_connect = $call_connect_samples = $min_call_connect = $max_call_connect = $avg_call_connect = 0;
    if ($res_call_connect->num_rows > 0) {
        while ($row=$res_call_connect->fetch_assoc()) {
            $call_connect = number_format($row['avg_cst'], 1) . ' sec';
            $call_connect_samples = $row['samples'];
            $min_call_connect = number_format($row['min_cst'], 1);
            $max_call_connect = number_format($row['max_cst'], 1);
            $avg_call_connect = number_format($row['avg_cst'], 1);
        }
    }
    
    // Call Connect Histogram
    $sql_call_connect_histogram = "select FORMAT(sum(IF(cst >= 0 and cst < 2, 1, 0))/count(call_no) * 100, 1) AS range_1, FORMAT(sum(IF(cst >= 2 and cst < 4, 1, 0))/count(call_no) * 100, 1) AS range_2, FORMAT(sum(IF(cst >= 4 and cst < 6, 1, 0))/count(call_no) * 100, 1) AS range_3, FORMAT(sum(IF(cst >= 6 and cst < 8, 1, 0))/count(call_no) * 100, 1) AS range_4, FORMAT(sum(IF(cst >= 8 and cst < 10, 1, 0))/count(call_no) * 100, 1) AS range_5, FORMAT(sum(IF(cst >= 10 and cst < 12, 1, 0))/count(call_no) * 100, 1) AS range_6, FORMAT(sum(IF(cst >= 12 and cst < 14, 1, 0))/count(call_no) * 100, 1) AS range_7, FORMAT(sum(IF(cst >= 14 and cst < 16, 1, 0))/count(call_no) * 100, 1) AS range_8, FORMAT(sum(IF(cst >= 16, 1, 0))/count(call_no) * 100, 1) AS range_9 from (select call_no, avg(setup_time)/1000 as cst from black_box_datas  where setup_time > 0 and $op $os $ts group by call_no)tbl";
    error_log($sql_call_connect_histogram."\n", 3, $log_file);
    $res_call_connect_histogram = $con->query($sql_call_connect_histogram);
    if ($res_call_connect_histogram->num_rows > 0) {
        while ($row=$res_call_connect_histogram->fetch_assoc()) {
            $call_connect_histogram = $row;
        }
    } else {
        $call_connect_histogram = [0, 0, 0, 0, 0, 0, 0, 0, 0];
    }
    
    $json_array['status'] = 200;
    $json_array['message']['call_connect_samples'] = $call_connect_samples;
    $json_array['message']['min_call_connect'] = $min_call_connect;
    $json_array['message']['max_call_connect'] = $max_call_connect;
    $json_array['message']['avg_call_connect'] = $avg_call_connect;
    $json_array['message']['call_connect_histogram'] = $call_connect_histogram;
    echo json_encode($json_array);
}

// get network details for map - mute
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_network_details_mute') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = isset($_POST['city']) ? strtolower($_POST['city']) : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = isset($_POST['op']) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = isset($_POST['os']) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = isset($_POST['start_dt']) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = isset($_POST['end_dt']) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts .= " and lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
           
    /*$sql_mute = "select GROUP_CONCAT(IF(mute_duration >= 0 and mute_duration <= 2, CONCAT('[', lat, ',', lng, ',', mute_duration, ']'), NULL) SEPARATOR ',') as range_1, GROUP_CONCAT(IF(mute_duration > 2 and mute_duration <= 5, CONCAT('[', lat, ',', lng, ',', mute_duration, ']'), NULL) SEPARATOR ',') as range_2, GROUP_CONCAT(IF(mute_duration > 5 and mute_duration <= 10, CONCAT('[', lat, ',', lng, ',', mute_duration, ']'), NULL) SEPARATOR ',') as range_3, GROUP_CONCAT(IF(mute_duration > 10 and mute_duration <= 15, CONCAT('[', lat, ',', lng, ',', mute_duration, ']'), NULL) SEPARATOR ',') as range_4, GROUP_CONCAT(IF(mute_duration > 15, CONCAT('[', lat, ',', lng, ',', mute_duration, ']'), NULL) SEPARATOR ',') as range_5 from ( select LAT lat, LON lng, MUTE_OF/1000 mute_duration FROM black_box_datas WHERE LAT REGEXP '[0-9]' and LON REGEXP '[0-9]' and MUTE_ON > 0 and $op $os $ts)tbl";
    $res_mute = $con->query($sql_mute);
    
    $mute_locations = $mute_locations_count = $mute_locations_percentage = false;
    $mute_locations_total_count = 0;
    if ($res_mute->num_rows > 0) {
        while ($row=$res_mute->fetch_assoc()) {       
            for($range=1; $range<=5; $range++) {
                if($row['range_'.$range] != null && $row['range_'.$range] != '') {
                    $array1 = array("[", "]");
                    $array2 = array("", "");
                    $locations = str_replace($array1, $array2, explode("],[", $row['range_'.$range]));
                    foreach($locations as $index => $val) {
                        if(count(explode(",", $val)) == 3) {
                            $mute_locations[$range-1][$index] = explode(",", $val);
                        }
                    }
                    $mute_locations_count[] = count($mute_locations[$range-1]);
                    $mute_locations_total_count += count($mute_locations[$range-1]);
                } else {
                        $mute_locations[] = [];
                }
            }
            
            foreach($mute_locations_count as $key => $val) {
                $mute_locations_percentage[] = ' ( ' . number_format($val/$mute_locations_total_count, 2) . '%  ) ';
                
                $mute_locations_count[$key] = ' ( ' . $val . ' ) ';
            }
        }
    }*/
    
    // Mute Locations Downlink
    $sql_mute = "select LAT lat, LON lng, MUTE_OF/1000 mute_duration FROM black_box_datas WHERE LAT REGEXP '[0-9]' and LON REGEXP '[0-9]' and MUTE_ON > 0 and BB_ID in ('" . implode("','", $downlink_mute_bb_ids) . "') and $op $os $ts";
    error_log($sql_mute."\n", 3, $log_file);
    $res_mute = $con->query($sql_mute);
    
    $mute_locations_downlink = false;
    if ($res_mute->num_rows > 0) {
        while ($row=$res_mute->fetch_assoc()) {       
            $mute_locations_downlink[] = array($row['lat'], $row['lng'], number_format($row['mute_duration'], 1).' sec');
        }
    }
    
    // Mute Locations Uplink
    $sql_mute = "select LAT lat, LON lng, MUTE_OF/1000 mute_duration FROM black_box_datas WHERE LAT REGEXP '[0-9]' and LON REGEXP '[0-9]' and MUTE_ON > 0 and BB_ID in ('" . implode("','", $uplink_mute_bb_ids) . "') and $op $os $ts";
    error_log($sql_mute."\n", 3, $log_file);
    $res_mute = $con->query($sql_mute);
    
    $mute_locations_uplink = false;
    if ($res_mute->num_rows > 0) {
        while ($row=$res_mute->fetch_assoc()) {       
            $mute_locations_uplink[] = array($row['lat'], $row['lng'], number_format($row['mute_duration'], 1).' sec');
        }
    }
    
    $json_array['status'] = 200;
    $json_array['message']['mute_locations_downlink'] = $mute_locations_downlink;
    $json_array['message']['mute_locations_uplink'] = $mute_locations_uplink;
//    $json_array['message']['mute_locations_count'] = $mute_locations_count;
//    $json_array['message']['mute_locations_percentage'] = $mute_locations_percentage;
//    $json_array['message']['query'] = $sql_rsrp;
    
    echo json_encode($json_array);
}

// get network details for map - rsrp
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_network_details_rsrp') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = isset($_POST['city']) ? strtolower($_POST['city']) : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = isset($_POST['op']) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = isset($_POST['os']) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = isset($_POST['start_dt']) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = isset($_POST['end_dt']) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts .= " and lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
         
    $filename = "rsrp_details.csv";

    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: text/csv");

    $out = fopen("php://output", 'w');
    
    $sql_rsrp = "select LAT lat, LON lng, RSRP rsrp FROM black_box_datas WHERE LAT REGEXP '[0-9]' and LON REGEXP '[0-9]' and RSRP REGEXP '[0-9]' and $op $os $ts and $total_calls_bb_ids_cond";
    error_log($sql_rsrp."\n", 3, $log_file);
    $res_rsrp = $con->query($sql_rsrp);
    
    if ($res_rsrp->num_rows > 0) {
        while ($row=$res_rsrp->fetch_assoc()) {
            fputcsv($out, array_values($row), ',', '"');
        }
        fclose($out);
    }
}

// get network details for map - rsrq
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_network_details_rsrq') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = isset($_POST['city']) ? strtolower($_POST['city']) : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = isset($_POST['op']) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = isset($_POST['os']) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = isset($_POST['start_dt']) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = isset($_POST['end_dt']) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts .= " and lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
         
    $filename = "rsrq_details.csv";

    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: text/csv");

    $out = fopen("php://output", 'w');
    
    $sql_rsrq = "select LAT lat, LON lng, RSRQ rsrp FROM black_box_datas WHERE LAT REGEXP '[0-9]' and LON REGEXP '[0-9]' and RSRQ REGEXP '[0-9]' and $op $os $ts and $total_calls_bb_ids_cond";
    error_log($sql_rsrq."\n", 3, $log_file);
    $res_rsrq = $con->query($sql_rsrq);
    
    if ($res_rsrq->num_rows > 0) {
        while ($row=$res_rsrq->fetch_assoc()) {
            fputcsv($out, array_values($row), ',', '"');
        }
        fclose($out);
    }
}

// get network details for map - sinr
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_network_details_sinr') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = isset($_POST['city']) ? strtolower($_POST['city']) : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = isset($_POST['op']) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = isset($_POST['os']) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = isset($_POST['start_dt']) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = isset($_POST['end_dt']) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts .= " and lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
         
    $filename = "sinr_details.csv";

    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: text/csv");

    $out = fopen("php://output", 'w');
    
    $sql_sinr = "select LAT lat, LON lng, SINR rsrp FROM black_box_datas WHERE LAT REGEXP '[0-9]' and LON REGEXP '[0-9]' and SINR REGEXP '[0-9]' and $op $os $ts and $total_calls_bb_ids_cond";
    error_log($sql_sinr."\n", 3, $log_file);
    $res_sinr = $con->query($sql_sinr);
    
    if ($res_sinr->num_rows > 0) {
        while ($row=$res_sinr->fetch_assoc()) {
            fputcsv($out, array_values($row), ',', '"');
        }
        fclose($out);
    }
}

// get network details for drop & block calls
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_rf_details_drop_block') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = isset($_POST['city']) ? strtolower($_POST['city']) : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = isset($_POST['op']) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = isset($_POST['os']) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = isset($_POST['start_dt']) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = isset($_POST['end_dt']) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts .= " and lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
         
    $sql_rf_drop_block = "select * from (select call_no, if((setup_time is null or setup_time <= 0) and call_duration < $block_call_max_threshold, 1, 0) as call_block, if(call_duration > $block_call_max_threshold and call_duration < $drop_call_max_threshold, 1, 0) as call_drop, max_sl_no, pci, cell_id from (select call_no, sum(setup_time) setup_time, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration, max(sl_no) max_sl_no, GROUP_CONCAT(distinct PSC_PCI SEPARATOR ',') pci, GROUP_CONCAT(distinct CELL_ID SEPARATOR ',') cell_id from black_box_datas  where call_no > 0 and CALL_STATE > 0 and $op $os $ts and $total_calls_bb_ids_cond group by call_no)tbl)tbl2 where call_block = 1 or call_drop = 1";
    error_log($sql_rf_drop_block."\n", 3, $log_file);
    $res_rf_drop_block = $con->query($sql_rf_drop_block);
    
    $rf_details = $id_arr = false;
    if ($res_rf_drop_block->num_rows > 0) {
        while ($row=$res_rf_drop_block->fetch_assoc()) {
//            $rf_details[$row['call_no']] = array('drop' => $row['call_drop'], 'block' => $row['call_block'], 'pci' => $row['pci'], 'cell_id' => $row['cell_id']);
            $rf_details[$row['call_no']] = array('drop' => $row['call_drop'], 'block' => $row['call_block']);
            $id_arr[] = $row['max_sl_no'];
        }
        if($id_arr) {
            $sql_rf_details = "select call_no, FROM_UNIXTIME(SUBSTR(TIMESTAMP, 1, 10)+19800, '%Y-%m-%d %H:%i:%s') ts, RSRP rsrp, RSRQ rsrq, SINR sinr, PSC_PCI pci, CELL_ID cell_id from black_box_datas where sl_no in (" . implode(",", $id_arr) . ")";
            error_log($sql_rf_details."\n", 3, $log_file);
            $res_rf_details = $con->query($sql_rf_details);
            if ($res_rf_details->num_rows > 0) {
                while ($row=$res_rf_details->fetch_assoc()) {
                    $tmp_call_no = $row['call_no'];
                    $rf_details[$tmp_call_no]['ts'] = $row['ts'];
                    $rf_details[$tmp_call_no]['rsrp'] = $row['rsrp'];
                    $rf_details[$tmp_call_no]['rsrq'] = $row['rsrq'];
                    $rf_details[$tmp_call_no]['sinr'] = $row['sinr'];
                    $rf_details[$tmp_call_no]['pci'] = $row['pci'];
                    $rf_details[$tmp_call_no]['cell_id'] = $row['cell_id'];
                }
            }
        }
    }
    
    $json_array['status'] = 200;
    $json_array['message']['rf_details'] = $rf_details;
    echo json_encode($json_array);
}

// get network details for mute calls
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_rf_details_mute') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = isset($_POST['city']) ? strtolower($_POST['city']) : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = isset($_POST['op']) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = isset($_POST['os']) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = isset($_POST['start_dt']) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = isset($_POST['end_dt']) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts .= " and lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
         
    $sql_rf_mute = "select call_no, mute_no, sum(MUTE_OF)/1000 mute_duration, FROM_UNIXTIME(SUBSTR(max(MUTE_ON), 1, 10)+19800, '%Y-%m-%d %H:%i:%s') ts, avg(RSRP) rsrp, avg(SINR) sinr, avg(RSRQ) rsrq, GROUP_CONCAT(distinct PSC_PCI SEPARATOR ',') pci, GROUP_CONCAT(distinct CELL_ID SEPARATOR ',') cell_id from black_box_datas  where is_mute = 1 and mute_no > 0 and MUTE_OF > 0 and $op $os $ts and $total_calls_bb_ids_cond group by call_no, mute_no order by ts";
    error_log($sql_rf_mute."\n", 3, $log_file);
    $res_rf_mute = $con->query($sql_rf_mute);
    
    $rf_details = false;
    $call_nos = false;
    if ($res_rf_mute->num_rows > 0) {
        while ($row=$res_rf_mute->fetch_assoc()) {
            $rf_details[] = $row;
            if(!$call_nos || !in_array($row['call_no'], $call_nos)) {
                $call_nos[] = $row['call_no'];
            }
        }
    }
    
    if($call_nos) {
        $sql_get_voice_file_ids = "select call_no, file_name from (select call_no, sum(voice_files_id) voice_files_id from black_box_datas  where voice_files_id > 0 and $op $os $ts and $total_calls_bb_ids_cond group by call_no)tbl inner join voice_files on (voice_files.id = tbl.voice_files_id)";
        error_log($sql_get_voice_file_ids."\n", 3, $log_file);
        $res_get_voice_file_ids = $con->query($sql_get_voice_file_ids);
    
        $voice_file_ids = false;
        if ($res_get_voice_file_ids->num_rows > 0) {
            while ($row=$res_get_voice_file_ids->fetch_assoc()) {
                $voice_file_ids[$row['call_no']] = "http://172.104.177.75/call_analyse/wav/voice_uploads/" . $row['file_name'];
            }
        }
    }
    
    $json_array['status'] = 200;
    $json_array['message']['rf_details'] = $rf_details;
    $json_array['message']['voice_file_ids'] = $voice_file_ids;
    echo json_encode($json_array);
}

// get call summary details
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_call_summary') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = isset($_POST['city']) ? strtolower($_POST['city']) : '';
//    $op = isset($_POST['op']) ? "lower(SPN) = '".strtolower($_POST['op'])."'" : '';
    $op = isset($_POST['op']) ? "lower(SPN) like '%".strtolower($_POST['op'])."%'" : '';
    $os = isset($_POST['os']) ? (($_POST['os']=='IOS') ? "lower(MAKE) = 'apple'" : "lower(MAKE) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = isset($_POST['start_dt']) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = isset($_POST['end_dt']) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (TIMESTAMP BETWEEN $start_ts AND $end_ts)";
    }
    
    if(!empty($city)) {
        $city_lat_lng = $con->query("select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if ($city_lat_lng->num_rows > 0) {
            while ($rows = $city_lat_lng->fetch_assoc()) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts .= " and lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
         
    // Total Calls
    $sql_total_calls = "select count(distinct call_no) total_calls from black_box_datas WHERE CALL_STATE = 2 AND $op $os $ts and $total_calls_bb_ids_cond";
    error_log($sql_total_calls."\n", 3, $log_file);
    $res_total_calls = $con->query($sql_total_calls);    
    $total_calls = 0;
    if ($res_total_calls->num_rows > 0) {
        while ($row=$res_total_calls->fetch_assoc()) {
            $total_calls = $row['total_calls'];
        }
    }
    
    // Drop & Block Calls
    if(isset($_POST['os']) && $_POST['os'] == 'IOS') {
        $sql_cdb_calls = "select count(call_no) total_calls, sum(call_block) block_calls, sum(call_drop) drop_calls from (select call_no, call_duration, states, if(FIND_IN_SET('3', states), 0, 1) as call_block, if(FIND_IN_SET('3', states), if(call_duration < $drop_call_max_threshold, 1, 0), 0) as call_drop from (select call_no, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration from black_box_datas  where MAKE = 'Apple' and call_no > 0 and CALL_STATE > 0 and $op $ts and $total_calls_bb_ids_cond group by call_no)tbl)tbl2";
    } else {
        $sql_cdb_calls = "select count(call_no) total_calls, sum(call_block) block_calls, sum(call_drop) drop_calls from (select call_no, call_duration, states, if((setup_time is null or setup_time <= 0) and call_duration < $block_call_max_threshold, 1, 0) as call_block, if(call_duration > $block_call_max_threshold and call_duration < $drop_call_max_threshold, 1, 0) as call_drop from (select call_no, sum(setup_time) setup_time, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration from black_box_datas  where MAKE != 'Apple' and call_no > 0 and CALL_STATE > 0 and $op $ts and $total_calls_bb_ids_cond group by call_no)tbl)tbl2";
    }
    error_log($sql_cdb_calls."\n", 3, $log_file);
    $res_cdb_calls = $con->query($sql_cdb_calls);
    $block_calls = $drop_calls = 0;
    if ($res_cdb_calls->num_rows > 0) {
        while ($row=$res_cdb_calls->fetch_assoc()) {
            $block_calls = $row['block_calls'];
            $drop_calls = $row['drop_calls'];
        }
    }
    $call_drop_rate = number_format($drop_calls/($total_calls-$block_calls) * 100, 1) . ' %';
    $call_block_rate = number_format($block_calls/$total_calls * 100, 1) . ' %';
        
    // Mute Calls
    $sql_mute_calls = "select count(distinct call_no) mute_calls from black_box_datas WHERE MUTE_ON > 0 and BB_ID in ('" . implode("','", $downlink_mute_bb_ids) . "') AND $op $os $ts UNION ALL select count(distinct call_no) mute_calls from black_box_datas WHERE MUTE_ON > 0 and BB_ID in ('" . implode("','", $uplink_mute_bb_ids) . "') AND $op $os $ts";
    error_log($sql_mute_calls."\n", 3, $log_file);
    $res_mute_calls = $con->query($sql_mute_calls);
    $mute_calls_downlink = $mute_calls_uplink = 0;
    if ($res_mute_calls->num_rows > 0) {
        $i = 1;
        while ($row=$res_mute_calls->fetch_assoc()) {
            if($i == 1) {
                $mute_calls_downlink = $row['mute_calls'];
            } elseif($i == 2) {
                $mute_calls_uplink = $row['mute_calls'];
            }
            $i++;
        }
    }    
    $mute_call_rate_downlink = $mute_call_rate_uplink = 0;
    $mute_call_rate_downlink = number_format($mute_calls_downlink/($total_calls-$block_calls) * 100, 1) . ' %';
    $mute_call_rate_uplink = number_format($mute_calls_uplink/($total_calls-$block_calls) * 100, 1) . ' %';
        
    // Call Connect
    $sql_call_connect = "select count(cst) samples, avg(cst)/1000 as avg_cst, min(cst)/1000 min_cst, max(cst)/1000 max_cst from (select call_no, avg(setup_time) as cst from black_box_datas  where setup_time > 0 and $op $os $ts group by call_no)tbl";
    error_log($sql_call_connect."\n", 3, $log_file);
    $res_call_connect = $con->query($sql_call_connect);    
    $call_connect = $call_connect_samples = $min_call_connect = $max_call_connect = $avg_call_connect = 0;
    if ($res_call_connect->num_rows > 0) {
        while ($row=$res_call_connect->fetch_assoc()) {
            $call_connect = number_format($row['avg_cst'], 1) . ' sec';
            $call_connect_samples = $row['samples'];
            $min_call_connect = number_format($row['min_cst'], 1);
            $max_call_connect = number_format($row['max_cst'], 1);
            $avg_call_connect = number_format($row['avg_cst'], 1);
        }
    }
    
    $json_array['status'] = 200;
    $json_array['message']['call_attempt'] = $total_calls;
    $json_array['message']['call_block'] = $block_calls;
    $json_array['message']['call_established'] = $total_calls - $block_calls;
    $json_array['message']['call_drop'] = $drop_calls;
    $json_array['message']['call_block_rate'] = $call_block_rate;
    $json_array['message']['call_drop_rate'] = $call_drop_rate;
    $json_array['message']['call_setup_time'] = $avg_call_connect;
//    $json_array['message']['mute_call_rate_uplink'] = $mute_call_rate_uplink;
//    $json_array['message']['mute_call_rate_downlink'] = $mute_call_rate_downlink;
    $json_array['message']['mute_call_rate'] = '<b>Uplink</b> ' . $mute_call_rate_uplink . ' <b>Downlink</b> ' . $mute_call_rate_downlink;
    echo json_encode($json_array);
}

mysqli_close($con);
exit();
?>
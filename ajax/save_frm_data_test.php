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

$dwnlink_bb_ids_cond = " bb_id in ('" . implode("','", $downlink_mute_bb_ids) . "') ";
//$uplink_bb_ids_cond = " bb_id in ('" . implode("','", $uplink_mute_bb_ids) . "') ";
//$total_calls_bb_ids_cond = " $dwnlink_bb_ids_cond ";

$total_calls_bb_ids_cond = " is_mo = 1";

// Get Country Lat Lng
if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'country_lat_lng') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $json_array['status'] = 200;
    $json_array['message'] = $country_lat_lng[$country];
    echo json_encode($json_array);
}

// Get City Details
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_city_details') {    
    $city = pg_query($con, "select city from city_lat_long order by city");
    
    if (pg_num_rows($city) > 0) {
        $json_array['status'] = 200;
        while ($rows = pg_fetch_assoc($city)) {
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
    $city_lat_lng = pg_query($con, "select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
    if (pg_num_rows($city_lat_lng) > 0) {
        $json_array['status'] = 200;
        while ($rows = pg_fetch_assoc($city_lat_lng)) {
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
        $cond = " and lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lng >= " . $country_lat_lng[$country]['min_lng'] . " and lng <= " . $country_lat_lng[$country]['max_lng'];
    }

//    $op = pg_query($con, "select distinct lower(operator) as operator from voice_test.rf_data where operator is not null and operator != '' and operator != '(null)' $cond");
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
//    $op = isset($_POST['op']) ? "lower(operator) = '".strtolower($_POST['op'])."'" : '';
    $op = isset($_POST['op']) ? "lower(spn) like '%".strtolower($_POST['op'])."%'" : '';
    $os = isset($_POST['os']) ? (($_POST['os']=='IOS') ? "lower(make) = 'apple'" : "lower(make) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = isset($_POST['start_dt']) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = isset($_POST['end_dt']) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (ts BETWEEN $start_ts AND $end_ts)";
    }
    
    if(!empty($city)) {
        $city_lat_lng = pg_query($con, "select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if (pg_num_rows($city_lat_lng) > 0) {
            while ($rows = pg_fetch_assoc($city_lat_lng)) {
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

//    $sql = "SELECT os, MODEL, OS, appVersion,IMEI, ts, LAT, LON, ACCURACY, TECH, SUB_TECH, ASU, rsrp, RSCP, RX_LEVEL, rsrq::double precision, ECIO, RX_QUAL, EARFCN,  UARFCN, ARFCN, sinr, MCC, MNC, LAC_TAC, cell_id, psc_pci, operator, DATA_STATE, SERVICE_STATE, RNC, CQI, FREQ, BAND, TA, call_state, CALL_DURATION, TEST_STATE, RSSI, SS, mute_of, mute_of, bb_id , call_drop, call_block FROM voice_test.rf_data WHERE $op $os $ts";
    $sql = "SELECT * FROM voice_test.rf_data WHERE $op $os $ts order by imei, ts";
    $res = pg_query($con, $sql);

    if (pg_num_rows($res) > 0) {
        $flag = false;
        while ($row=pg_fetch_assoc($res)) {
            $row['Formatted_timestamp'] = (!empty($row['ts'])) ? date('Y-m-d H:i:s', substr_replace($row['ts'], "", -3)) : '';
//            $row['Formatted_mute_on'] = (!empty($row['mute_on']) && $row['mute_on'] > 0) ? date('Y-m-d H:i:s', substr_replace($row['mute_on'], "", -3)) : '';
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
//    $op = isset($_POST['op']) ? "lower(operator) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(operator) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(os) = 'apple'" : "lower(os) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (start_time BETWEEN $start_ts AND $end_ts)";
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
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lng >= " . $country_lat_lng[$country]['min_lng'] . " and lng <= " . $country_lat_lng[$country]['max_lng'];
    }
    
    // Total Calls
    $sql_total_calls = "select call_type, count(call_id) calls from voice_test.call_details where is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $op $os $ts and $total_calls_bb_ids_cond group by call_type";
    //error_log($sql_total_calls."\n", 3, $log_file);
    $res_total_calls = pg_query($con, $sql_total_calls);
    $total_calls = $block_calls = $drop_calls = 0;
    if (pg_num_rows($res_total_calls) > 0) {
        while ($row=pg_fetch_assoc($res_total_calls)) {
            $total_calls += $row['calls'];
            if($row['call_type'] == 1) {
                $block_calls = $row['calls'];
            } elseif($row['call_type'] == 2) {
                $drop_calls = $row['calls'];
            }
        }
    }
    
    $call_drop_rate = $call_block_rate = '0 %';
    if($total_calls > 0) {
        $call_drop_rate = number_format($drop_calls/($total_calls-$block_calls) * 100, 1) . ' %';
        $call_block_rate = number_format($block_calls/$total_calls * 100, 1) . ' %';
    }
        
    // Mute Calls
    $sql_mute_calls = "select is_mo, count(call_id) mute_calls from voice_test.call_details where mute_count > 0 AND $op $os $ts group by is_mo";
    //error_log($sql_mute_calls."\n", 3, $log_file);
    $res_mute_calls = pg_query($con, $sql_mute_calls);
    $mute_calls_downlink = $mute_calls_uplink = 0;
    if (pg_num_rows($res_mute_calls) > 0) {
        while ($row=pg_fetch_assoc($res_mute_calls)) {
            if($row['is_mo'] == 1) {
                $mute_calls_downlink = $row['mute_calls'];
            } elseif($row['is_mo'] == 0) {
                $mute_calls_uplink = $row['mute_calls'];
            }
        }
    }
//    $json_array['message']['qry3'] = $sql_mute_calls;
    
    $mute_call_rate_downlink = $mute_call_rate_uplink = 0;
    if($total_calls > 0) {
        $mute_call_rate_downlink = number_format($mute_calls_downlink/($total_calls-$block_calls) * 100, 1) . ' %';
        $mute_call_rate_uplink = number_format($mute_calls_uplink/($total_calls-$block_calls) * 100, 1) . ' %';
        if($mute_calls_uplink == 0) {
            $mute_call_rate_uplink = '-';
        }
    }
        
    // Call Connect
    $sql_call_connect = "select avg(setup_time)/1000 as avg_cst from voice_test.call_details where setup_time > 0 and is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $op $os $ts and $total_calls_bb_ids_cond";
    //error_log($sql_call_connect."\n", 3, $log_file);
    $res_call_connect = pg_query($con, $sql_call_connect);    
    $call_connect = 0;
    if (pg_num_rows($res_call_connect) > 0) {
        while ($row=pg_fetch_assoc($res_call_connect)) {
            $call_connect = number_format($row['avg_cst'], 1) . ' sec';
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

// get_call_connect_trend_details
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_call_connect_trend_details') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = (isset($_POST['city']) && !empty($_POST['city'])) ? strtolower($_POST['city']) : '';
    $trend = isset($_POST['trend']) ? $_POST['trend'] : '';
//    $op = isset($_POST['op']) ? "lower(operator) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(operator) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(os) = 'apple'" : "lower(os) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (start_time BETWEEN $start_ts AND $end_ts)";
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
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lng >= " . $country_lat_lng[$country]['min_lng'] . " and lng <= " . $country_lat_lng[$country]['max_lng'];
    }
    
    // Get Call Connect Time average
    if($trend == 'day' || $trend == 'custom') {
        $no_of_days_or_hr = 31;
        $sql_call_connect_time_wise = "select extract(day from (TIMESTAMP 'epoch' + substring((start_time+19800000)::varchar,0,11)::bigint * INTERVAL '1 second')) as day, avg(setup_time)/1000 as avg_cst from voice_test.call_details where setup_time > 0 and is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $op $os $ts and $total_calls_bb_ids_cond group by day";
    } elseif($trend == 'hour') {
        $no_of_days_or_hr = 24;
        $sql_call_connect_time_wise = "select extract(hour from (TIMESTAMP 'epoch' + substring((start_time+19800000)::varchar,0,11)::bigint * INTERVAL '1 second')) as hour, avg(setup_time)/1000 as avg_cst from voice_test.call_details where setup_time > 0 and is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $op $os $ts and $total_calls_bb_ids_cond group by hour";
    }
    //error_log($sql_call_connect_time_wise."\n", 3, $log_file);
    $res_call_connect_time_wise = pg_query($con, $sql_call_connect_time_wise);
    $call_connect_time_wise = false;
    if (pg_num_rows($res_call_connect_time_wise) > 0) {
        while ($row=pg_fetch_assoc($res_call_connect_time_wise)) {
            if($trend == 'day' || $trend == 'custom') {
                $call_connect_time_wise[$row['day']] = number_format($row['avg_cst'], 1);
            } elseif($trend == 'hour') {
                $call_connect_time_wise[$row['hour']] = number_format($row['avg_cst'], 1);
            }
        }
    } else {
        $call_connect_time_wise = [];
    }
    
    if(!is_array($call_connect_time_wise)) {
        $call_connect_time_wise = [];
    }
    
    for($i=0; $i<$no_of_days_or_hr; $i++) {
        if(!array_key_exists($i, $call_connect_time_wise)) {
            $call_connect_time_wise[$i] = null;
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
//    $op = isset($_POST['op']) ? "lower(operator) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(operator) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(os) = 'apple'" : "lower(os) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (start_time BETWEEN $start_ts AND $end_ts)";
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
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lng >= " . $country_lat_lng[$country]['min_lng'] . " and lng <= " . $country_lat_lng[$country]['max_lng'];
    }
            
    // Get Block Call Count
    if($trend == 'day' || $trend == 'custom') {
        $no_of_days_or_hr = 31;
        $sql_block_time_wise = "select count(case when (call_type = 1) then call_id end) as block_calls, count(call_id) as total_calls, extract(day from (TIMESTAMP 'epoch' + substring((start_time+19800000)::varchar,0,11)::bigint * INTERVAL '1 second')) as day from voice_test.call_details WHERE is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $total_calls_bb_ids_cond and $op $os $ts group by day";
    } elseif($trend == 'hour') {
        $no_of_days_or_hr = 24;
        $sql_block_time_wise = "select count(case when (call_type = 1) then call_id end) as block_calls, count(call_id) as total_calls, extract(hour from (TIMESTAMP 'epoch' + substring((start_time+19800000)::varchar,0,11)::bigint * INTERVAL '1 second')) as hour from voice_test.call_details WHERE is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $total_calls_bb_ids_cond and $op $os $ts group by hour";
    }
    //error_log($sql_block_time_wise."\n", 3, $log_file);
    $res_block_time_wise = pg_query($con, $sql_block_time_wise);
    $block_time_wise = false;
    if (pg_num_rows($res_block_time_wise) > 0) {
        while ($row=pg_fetch_assoc($res_block_time_wise)) {
            if($row['block_calls'] > 0) {
                if($trend == 'day' || $trend == 'custom') {
                    $block_time_wise[$row['day']] = number_format(($row['block_calls']/$row['total_calls']) * 100, 1);
                } elseif($trend == 'hour') {
                    $block_time_wise[$row['hour']] = number_format(($row['block_calls']/$row['total_calls']) * 100, 1);
                }
            }
        }
    } else {
        $block_time_wise = [];
    }
    
    if(!is_array($block_time_wise)) {
        $block_time_wise = [];
    }
    
    for($i=0; $i<$no_of_days_or_hr; $i++) {
        if(!array_key_exists($i, $block_time_wise)) {
            $block_time_wise[$i] = null;
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
//    $op = isset($_POST['op']) ? "lower(operator) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(operator) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(os) = 'apple'" : "lower(os) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (start_time BETWEEN $start_ts AND $end_ts)";
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
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lng >= " . $country_lat_lng[$country]['min_lng'] . " and lng <= " . $country_lat_lng[$country]['max_lng'];
    }
    
    // Get Drop Call Count
    if($trend == 'day' || $trend == 'custom') {
        $no_of_days_or_hr = 31;
        $sql_drop_time_wise = "select count(case when (call_type = 1) then call_id end) as block_calls, count(case when (call_type = 2) then call_id end) as drop_calls, count(call_id) as total_calls, extract(day from (TIMESTAMP 'epoch' + substring((start_time+19800000)::varchar,0,11)::bigint * INTERVAL '1 second')) as day from voice_test.call_details WHERE is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $total_calls_bb_ids_cond and $op $os $ts group by day";
    } elseif($trend == 'hour') {
        $no_of_days_or_hr = 24;
        $sql_drop_time_wise = "select count(case when (call_type = 1) then call_id end) as block_calls, count(case when (call_type = 2) then call_id end) as drop_calls, count(call_id) as total_calls, extract(hour from (TIMESTAMP 'epoch' + substring((start_time+19800000)::varchar,0,11)::bigint * INTERVAL '1 second')) as hour from voice_test.call_details WHERE is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $total_calls_bb_ids_cond and $op $os $ts group by hour";
    }
    //error_log($sql_drop_time_wise."\n", 3, $log_file);
    $res_drop_time_wise = pg_query($con, $sql_drop_time_wise);
    $drop_time_wise = false;
    if (pg_num_rows($res_drop_time_wise) > 0) {
        while ($row=pg_fetch_assoc($res_drop_time_wise)) {
            if($row['drop_calls'] > 0) {
                if($trend == 'day' || $trend == 'custom') {
                    $drop_time_wise[$row['day']] = number_format(($row['drop_calls']/($row['total_calls']-$row['block_calls'])) * 100, 1);
                } elseif($trend == 'hour') {
                    $drop_time_wise[$row['hour']] = number_format(($row['drop_calls']/($row['total_calls']-$row['block_calls'])) * 100, 1);
                }
            }
        }
    } else {
        $drop_time_wise = [];
    }
    
    if(!is_array($drop_time_wise)) {
        $drop_time_wise = [];
    }
    
    for($i=0; $i<$no_of_days_or_hr; $i++) {
        if(!is_array($drop_time_wise) || !array_key_exists($i, $drop_time_wise)) {
            $drop_time_wise[$i] = null;
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
//    $op = isset($_POST['op']) ? "lower(operator) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(operator) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(os) = 'apple'" : "lower(os) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (start_time BETWEEN $start_ts AND $end_ts)";
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
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lng >= " . $country_lat_lng[$country]['min_lng'] . " and lng <= " . $country_lat_lng[$country]['max_lng'];
    }
    
    // Total Calls
    $sql_total_calls = "select call_type, count(call_id) calls from voice_test.call_details where is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $op $os $ts and $total_calls_bb_ids_cond group by call_type";
    //error_log($sql_total_calls."\n", 3, $log_file);
    $res_total_calls = pg_query($con, $sql_total_calls);
    $total_calls = $block_calls = $drop_calls = 0;
    if (pg_num_rows($res_total_calls) > 0) {
        while ($row=pg_fetch_assoc($res_total_calls)) {
            $total_calls += $row['calls'];
            if($row['call_type'] == 1) {
                $block_calls = $row['calls'];
            } elseif($row['call_type'] == 2) {
                $drop_calls = $row['calls'];
            }
        }
    }
    
    // Get Block Call Count
    if($trend == 'day' || $trend == 'custom') {
        $no_of_days_or_hr = 31;
        $sql_block_time_wise = "select extract(day from (TIMESTAMP 'epoch' + substring((start_time+19800000)::varchar,0,11)::bigint * INTERVAL '1 second')) as day, count(call_id) block_calls from voice_test.call_details where call_type = 1 and is_mapped = 1 and $op $os $ts and $total_calls_bb_ids_cond group by day";
    } elseif($trend == 'hour') {
        $no_of_days_or_hr = 24;
        $sql_block_time_wise = "select extract(hour from (TIMESTAMP 'epoch' + substring((start_time+19800000)::varchar,0,11)::bigint * INTERVAL '1 second')) as hour, count(call_id) block_calls from voice_test.call_details where call_type = 1 and is_mapped = 1 and $op $os $ts and $total_calls_bb_ids_cond group by hour";
    }
    //error_log($sql_block_time_wise."\n", 3, $log_file);
    $res_block_time_wise = pg_query($con, $sql_block_time_wise);
    $block_time_wise = false;
    if (pg_num_rows($res_block_time_wise) > 0) {
        while ($row=pg_fetch_assoc($res_block_time_wise)) {
            if($trend == 'day' || $trend == 'custom') {
                $block_time_wise[$row['day']] = $row['block_calls'];
            } elseif($trend == 'hour') {
                $block_time_wise[$row['hour']] = $row['block_calls'];
            }
        }
    } else {
        $block_time_wise = [];
    }
    
    for($i=0; $i<$no_of_days_or_hr; $i++) {
        if(!array_key_exists($i, $block_time_wise)) {
            $block_time_wise[$i] = 0;
        }
    }
    
    // Mute Calls
    $sql_mute_calls = "select is_mo, count(call_id) mute_calls, min(mute_duration/1000) min_mute_sec, max(mute_duration/1000) max_mute_sec, avg(mute_duration/1000) avg_mute_sec from voice_test.call_details cd inner join voice_test.voice_mute vm on (cd.voice_file_id=vm.voice_file_id) where mute_count > 0 AND $op $os $ts group by is_mo";
    //error_log($sql_mute_calls."\n", 3, $log_file);
    $res_mute_calls = pg_query($con, $sql_mute_calls);
    $mute_calls_downlink = $mute_calls_uplink = $min_mute_sec_downlink = $max_mute_sec_downlink = $avg_mute_sec_downlink = $min_mute_sec_uplink = $max_mute_sec_uplink = $avg_mute_sec_uplink = 0;
    if (pg_num_rows($res_mute_calls) > 0) {
        while ($row=pg_fetch_assoc($res_mute_calls)) {
            if($row['is_mo'] == 1) {
                $mute_calls_downlink = $row['mute_calls'];
                $min_mute_sec_downlink = number_format($row['min_mute_sec'], 1);
                $max_mute_sec_downlink = number_format($row['max_mute_sec'], 1);
                $avg_mute_sec_downlink = number_format($row['avg_mute_sec'], 1);
            } elseif($row['is_mo'] == 0) {
                $mute_calls_uplink = $row['mute_calls'];
                $min_mute_sec_uplink = number_format($row['min_mute_sec'], 1);
                $max_mute_sec_uplink = number_format($row['max_mute_sec'], 1);
                $avg_mute_sec_uplink = number_format($row['avg_mute_sec'], 1);
                if($avg_mute_sec_uplink == 0) {
                    $mute_calls_uplink = $min_mute_sec_uplink = $max_mute_sec_uplink = $avg_mute_sec_uplink = '-';
                }
            }
        }
    }
    
    // Get Mute count Downlink
    if($trend == 'day' || $trend == 'custom') {
        $no_of_days_or_hr = 31;
        $sql_mute_calls_time_wise = "select count(case when (mute_count > 0) then call_id end) as mute_calls, count(call_id) as total_calls, extract(day from (TIMESTAMP 'epoch' + substring((start_time+19800000)::varchar,0,11)::bigint * INTERVAL '1 second')) as day from voice_test.call_details WHERE is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $total_calls_bb_ids_cond and $op $os $ts group by day";
    } elseif($trend == 'hour') {
        $no_of_days_or_hr = 24;
        $sql_mute_calls_time_wise = "select count(case when (mute_count > 0) then call_id end) as mute_calls, count(call_id) as total_calls, extract(hour from (TIMESTAMP 'epoch' + substring((start_time+19800000)::varchar,0,11)::bigint * INTERVAL '1 second')) as hour from voice_test.call_details WHERE is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $total_calls_bb_ids_cond and $op $os $ts group by hour";
    }
    //error_log($sql_mute_calls_time_wise."\n", 3, $log_file);
    $res_mute_calls_time_wise = pg_query($con, $sql_mute_calls_time_wise);
    $mute_calls_time_wise_downlink = false;
    if (pg_num_rows($res_mute_calls_time_wise) > 0) {
        while ($row=pg_fetch_assoc($res_mute_calls_time_wise)) {
            if($row['total_calls'] > 0 && $row['mute_calls'] > 0) {
                if($trend == 'day' || $trend == 'custom') {
                    $mute_calls_time_wise_downlink[$row['day']] = number_format($row['mute_calls']/($row['total_calls']-$block_time_wise[$row['day']]) * 100, 1);
                } elseif($trend == 'hour') {
                    $mute_calls_time_wise_downlink[$row['hour']] = number_format($row['mute_calls']/($row['total_calls']-$block_time_wise[$row['hour']]) * 100, 1);
                }
            }
        }
    } else {
        $mute_calls_time_wise_downlink = [];
    }
    
    for($i=0; $i<$no_of_days_or_hr; $i++) {
        if(!is_array($mute_calls_time_wise_downlink) || !array_key_exists($i, $mute_calls_time_wise_downlink)) {
            $mute_calls_time_wise_downlink[$i] = null;
        }
    }
    
    // Get Mute count Uplink
    if($trend == 'day' || $trend == 'custom') {
        $no_of_days_or_hr = 31;
        $sql_mute_calls_time_wise = "select count(case when (mute_count > 0) then call_id end) as mute_calls, count(call_id) as total_calls, extract(day from (TIMESTAMP 'epoch' + substring((start_time+19800000)::varchar,0,11)::bigint * INTERVAL '1 second')) as day from voice_test.call_details WHERE is_mo = 0 and is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $op $os $ts group by day";
    } elseif($trend == 'hour') {
        $no_of_days_or_hr = 24;
        $sql_mute_calls_time_wise = "select count(case when (mute_count > 0) then call_id end) as mute_calls, count(call_id) as total_calls, extract(hour from (TIMESTAMP 'epoch' + substring((start_time+19800000)::varchar,0,11)::bigint * INTERVAL '1 second')) as hour from voice_test.call_details WHERE is_mo = 0 and is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $op $os $ts group by hour";
    }
    //error_log($sql_mute_calls_time_wise."\n", 3, $log_file);
    $res_mute_calls_time_wise = pg_query($con, $sql_mute_calls_time_wise);
    $mute_calls_time_wise_uplink = false;
    if (pg_num_rows($res_mute_calls_time_wise) > 0) {
        while ($row=pg_fetch_assoc($res_mute_calls_time_wise)) {
            if($row['total_calls'] > 0 && $row['mute_calls'] > 0) {
                if($trend == 'day' || $trend == 'custom') {
                    $mute_calls_time_wise_uplink[$row['day']] = number_format($row['mute_calls']/($row['total_calls']-$block_time_wise[$row['day']]) * 100, 1);
                } elseif($trend == 'hour') {
                    $mute_calls_time_wise_uplink[$row['hour']] = number_format($row['mute_calls']/($row['total_calls']-$block_time_wise[$row['hour']]) * 100, 1);
                }
            }
        }
    } else {
        $mute_calls_time_wise_uplink = [];
    }
    
    for($i=0; $i<$no_of_days_or_hr; $i++) {
        if(!is_array($mute_calls_time_wise_uplink) || !array_key_exists($i, $mute_calls_time_wise_uplink)) {
            $mute_calls_time_wise_uplink[$i] = null;
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
//    $op = isset($_POST['op']) ? "lower(operator) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(operator) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(os) = 'apple'" : "lower(os) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (start_time BETWEEN $start_ts AND $end_ts)";
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
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lng >= " . $country_lat_lng[$country]['min_lng'] . " and lng <= " . $country_lat_lng[$country]['max_lng'];
    }
    
    // Total Calls
    $sql_total_calls = "select call_type, count(call_id) calls from voice_test.call_details where is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $op $os $ts and $total_calls_bb_ids_cond group by call_type";
    //error_log($sql_total_calls."\n", 3, $log_file);
    $res_total_calls = pg_query($con, $sql_total_calls);
    $total_calls = $block_calls = $drop_calls = 0;
    if (pg_num_rows($res_total_calls) > 0) {
        while ($row=pg_fetch_assoc($res_total_calls)) {
            $total_calls += $row['calls'];
            if($row['call_type'] == 1) {
                $block_calls = $row['calls'];
            } elseif($row['call_type'] == 2) {
                $drop_calls = $row['calls'];
            }
        }
    }
    
    // Mute Calls
    $sql_mute_calls = "select is_mo, count(call_id) mute_calls, min(mute_duration/1000) min_mute_sec, max(mute_duration/1000) max_mute_sec, avg(mute_duration/1000) avg_mute_sec from voice_test.call_details cd inner join voice_test.voice_mute vm on (cd.voice_file_id=vm.voice_file_id) where mute_count > 0 AND $op $os $ts group by is_mo";
    //error_log($sql_mute_calls."\n", 3, $log_file);
    $res_mute_calls = pg_query($con, $sql_mute_calls);
    $mute_calls_downlink = $mute_calls_uplink = $min_mute_sec_downlink = $max_mute_sec_downlink = $avg_mute_sec_downlink = $min_mute_sec_uplink = $max_mute_sec_uplink = $avg_mute_sec_uplink = 0;
    if (pg_num_rows($res_mute_calls) > 0) {
        $i = 1;
        while ($row=pg_fetch_assoc($res_mute_calls)) {
            if($row['is_mo'] == 1) {
                $mute_calls_downlink = $row['mute_calls'];
                $min_mute_sec_downlink = number_format($row['min_mute_sec'], 1);
                $max_mute_sec_downlink = number_format($row['max_mute_sec'], 1);
                $avg_mute_sec_downlink = number_format($row['avg_mute_sec'], 1);
            } elseif($row['is_mo'] == 0) {
                $mute_calls_uplink = $row['mute_calls'];
                $min_mute_sec_uplink = number_format($row['min_mute_sec'], 1);
                $max_mute_sec_uplink = number_format($row['max_mute_sec'], 1);
                $avg_mute_sec_uplink = number_format($row['avg_mute_sec'], 1);
            }
            $i++;
        }
    }
    
    // Mute Duration Histogram Downlink
    $sql_mute_duration_histogram = "select min(total_mute_duration/1000::float) min_mute_duration, max(total_mute_duration/1000::float) max_mute_duration, avg(total_mute_duration/1000::float) avg_mute_duration, sum(case when (total_mute_duration/1000::float >= 0 and total_mute_duration/1000::float < 2) then 1 else 0 end) AS range_1, sum(case when (total_mute_duration/1000::float >= 2 and total_mute_duration/1000::float < 4) then 1 else 0 end) AS range_2, sum(case when (total_mute_duration/1000::float >= 4 and total_mute_duration/1000::float < 6) then 1 else 0 end) AS range_3, sum(case when (total_mute_duration/1000::float >= 6 and total_mute_duration/1000::float < 8) then 1 else 0 end) AS range_4, sum(case when (total_mute_duration/1000::float >= 8 and total_mute_duration/1000::float < 10) then 1 else 0 end) AS range_5, sum(case when (total_mute_duration/1000::float >= 10 and total_mute_duration/1000::float < 12) then 1 else 0 end) AS range_6, sum(case when (total_mute_duration/1000::float >= 12 and total_mute_duration/1000::float < 14) then 1 else 0 end) AS range_7, sum(case when (total_mute_duration/1000::float >= 14 and total_mute_duration/1000::float < 16) then 1 else 0 end) AS range_8, sum(case when (total_mute_duration/1000::float >= 16) then 1 else 0 end) AS range_9 from voice_test.call_details where mute_count > 0 and $total_calls_bb_ids_cond AND $op $os $ts";
    //error_log($sql_mute_duration_histogram."\n", 3, $log_file);
    $res_mute_duration_histogram = pg_query($con, $sql_mute_duration_histogram);    
    $total_mute_calls_downlink = $min_mute_duration_downlink = $max_mute_duration_downlink = $avg_mute_duration_downlink = 0;
    if (pg_num_rows($res_mute_duration_histogram) > 0) {
        while ($row=pg_fetch_assoc($res_mute_duration_histogram)) {
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
    $sql_mute_duration_histogram = "select min(total_mute_duration/1000::float) min_mute_duration, max(total_mute_duration/1000::float) max_mute_duration, avg(total_mute_duration/1000::float) avg_mute_duration, sum(case when (total_mute_duration/1000::float >= 0 and total_mute_duration/1000::float < 2) then 1 else 0 end) AS range_1, sum(case when (total_mute_duration/1000::float >= 2 and total_mute_duration/1000::float < 4) then 1 else 0 end) AS range_2, sum(case when (total_mute_duration/1000::float >= 4 and total_mute_duration/1000::float < 6) then 1 else 0 end) AS range_3, sum(case when (total_mute_duration/1000::float >= 6 and total_mute_duration/1000::float < 8) then 1 else 0 end) AS range_4, sum(case when (total_mute_duration/1000::float >= 8 and total_mute_duration/1000::float < 10) then 1 else 0 end) AS range_5, sum(case when (total_mute_duration/1000::float >= 10 and total_mute_duration/1000::float < 12) then 1 else 0 end) AS range_6, sum(case when (total_mute_duration/1000::float >= 12 and total_mute_duration/1000::float < 14) then 1 else 0 end) AS range_7, sum(case when (total_mute_duration/1000::float >= 14 and total_mute_duration/1000::float < 16) then 1 else 0 end) AS range_8, sum(case when (total_mute_duration/1000::float >= 16) then 1 else 0 end) AS range_9 from voice_test.call_details where mute_count > 0 and is_mo = 0 AND $op $os $ts";
    //error_log($sql_mute_duration_histogram."\n", 3, $log_file);
    $res_mute_duration_histogram = pg_query($con, $sql_mute_duration_histogram);    
    $total_mute_calls_uplink = $min_mute_duration_uplink = $max_mute_duration_uplink = $avg_mute_duration_uplink = 0;
    if (pg_num_rows($res_mute_duration_histogram) > 0) {
        while ($row=pg_fetch_assoc($res_mute_duration_histogram)) {
            for($range=1; $range<=9; $range++) {
                $mute_duration_histogram_uplink[] = $row['range_'.$range];
            }
            $min_mute_duration_uplink = number_format($row['min_mute_duration'], 1);
            $max_mute_duration_uplink = number_format($row['max_mute_duration'], 1);
            $avg_mute_duration_uplink = number_format($row['avg_mute_duration'], 1);
            if($avg_mute_duration_uplink == 0) {
                $total_mute_calls_uplink = $min_mute_duration_uplink = $max_mute_duration_uplink = $avg_mute_duration_uplink = '-';
            }
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
        
    // Coverage V/S Quality V/S Mute
//    $sql_cov_qua_mute = "select avg(rsrp::double precision) rsrp, avg(sinr::double precision) sinr, (max(ts)-min(ts))/1000::float as mute_duration, mute_no from (select *, sum(ms) filter(where is_mute = 1) over (order by imei, ts asc rows between unbounded preceding and current row) mute_no from (select imei, ts, lat, lon, rsrp, rsrq, sinr, psc_pci, cell_id, is_mute, case when is_mute = lag(is_mute) over (partition by imei order by ts) then 0 else 1 end ms from voice_test.rf_data where rsrp is not null and sinr is not null AND $op_rf $os_rf $ts_rf)tbl)tbl2 where is_mute = 1 group by mute_no";
    $sql_cov_qua_mute = "select avg(rsrp::double precision) rsrp, avg(sinr::double precision) sinr, max(mute_duration)/1000::float as mute_duration, rd.voice_mute_id from voice_test.rf_data rd inner join voice_test.voice_mute vm on (rd.voice_mute_id = vm.voice_mute_id) where rsrp is not null and sinr is not null and is_mute = 1 AND $op_rf $os_rf $ts_rf group by rd.voice_mute_id";
    //error_log($sql_cov_qua_mute."\n", 3, $log_file);
    $res_cov_qua_mute = pg_query($con, $sql_cov_qua_mute);
    $mute_samples = $min_mute_duration_cov = $max_mute_duration_cov = $avg_mute_duration_cov = 0;
    if (pg_num_rows($res_cov_qua_mute) > 0) {
        while ($row=pg_fetch_assoc($res_cov_qua_mute)) {
            if($row['mute_duration'] >= 0 and $row['mute_duration'] <= 2) {
                $cov_qua[0][] = $row['rsrp'] . ',' . $row['sinr'];
            } elseif($row['mute_duration'] > 2 and $row['mute_duration'] <= 5) {
                $cov_qua[1][] = $row['rsrp'] . ',' . $row['sinr'];
            } elseif($row['mute_duration'] > 5 and $row['mute_duration'] <= 10) {
                $cov_qua[2][] = $row['rsrp'] . ',' . $row['sinr'];
            } elseif($row['mute_duration'] > 10 and $row['mute_duration'] <= 15) {
                $cov_qua[3][] = $row['rsrp'] . ',' . $row['sinr'];
            } elseif($row['mute_duration'] > 15) {
                $cov_qua[4][] = $row['rsrp'] . ',' . $row['sinr'];
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
    
//    $sql_cov_qua_mute = "select count(1) as mute_samples, min(mute_duration/1000) min_mute_duration, max(mute_duration/1000) max_mute_duration, avg(mute_duration/1000) avg_mute_duration from (select (max(ts)-min(ts))::float as mute_duration, mute_no from (select *, sum(ms) filter(where is_mute = 1) over (order by imei, ts asc rows between unbounded preceding and current row) mute_no from (select imei, ts, lat, lon, rsrp, rsrq, sinr, psc_pci, cell_id, is_mute, case when is_mute = lag(is_mute) over (partition by imei order by ts) then 0 else 1 end ms from voice_test.rf_data where rsrp is not null and sinr is not null AND $op_rf $os_rf $ts_rf)tbl)tbl2 where is_mute = 1 group by mute_no)tbl";
    $sql_cov_qua_mute = "select count(1) as mute_samples, min(mute_duration/1000) min_mute_duration, max(mute_duration/1000) max_mute_duration, avg(mute_duration/1000) avg_mute_duration from voice_test.voice_mute where voice_mute_id in (select voice_mute_id from voice_test.rf_data where rsrp is not null and sinr is not null and is_mute = 1 AND $op_rf $os_rf $ts_rf)";
    //error_log($sql_cov_qua_mute."\n", 3, $log_file);
    $res_cov_qua_mute = pg_query($con, $sql_cov_qua_mute);    
    $mute_samples = $min_mute_duration_cov = $max_mute_duration_cov = $avg_mute_duration_cov = 0;
    if (pg_num_rows($res_cov_qua_mute) > 0) {
        while ($row=pg_fetch_assoc($res_cov_qua_mute)) {
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
//    $op = isset($_POST['op']) ? "lower(operator) = '".strtolower($_POST['op'])."'" : '';
    $op = (isset($_POST['op']) && !empty($_POST['op'])) ? "lower(operator) like '%".strtolower($_POST['op'])."%'" : '';
    $os = (isset($_POST['os']) && !empty($_POST['start_dt'])) ? (($_POST['os']=='IOS') ? "lower(os) = 'apple'" : "lower(os) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = (isset($_POST['start_dt']) && !empty($_POST['start_dt'])) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = (isset($_POST['end_dt']) && !empty($_POST['end_dt'])) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (start_time BETWEEN $start_ts AND $end_ts)";
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
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts = (empty($ts)) ? '' : $ts . ' AND ';
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lng >= " . $country_lat_lng[$country]['min_lng'] . " and lng <= " . $country_lat_lng[$country]['max_lng'];
    }
        
    // Call Connect
    $sql_call_connect = "select count(setup_time) samples, avg(setup_time)/1000 as avg_cst, min(setup_time)/1000 min_cst, max(setup_time)/1000 max_cst from voice_test.call_details where setup_time > 0 and is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $op $os $ts and $total_calls_bb_ids_cond";
    //error_log($sql_call_connect."\n", 3, $log_file);
    $res_call_connect = pg_query($con, $sql_call_connect);    
    $call_connect = $call_connect_samples = $min_call_connect = $max_call_connect = $avg_call_connect = 0;
    if (pg_num_rows($res_call_connect) > 0) {
        while ($row=pg_fetch_assoc($res_call_connect)) {
            $call_connect = number_format($row['avg_cst'], 1) . ' sec';
            $call_connect_samples = $row['samples'];
            $min_call_connect = number_format($row['min_cst'], 1);
            $max_call_connect = number_format($row['max_cst'], 1);
            $avg_call_connect = number_format($row['avg_cst'], 1);
        }
    }
    
    // Call Connect Histogram
    $sql_call_connect_histogram = "select sum(case when (cst >= 0 and cst < 2) then 1 else 0 end)::float/count(call_id)::float * 100 AS range_1, sum(case when (cst >= 2 and cst < 4) then 1 else 0 end)::float/count(call_id)::float * 100 AS range_2, sum(case when (cst >= 4 and cst < 6) then 1 else 0 end)::float/count(call_id)::float * 100 AS range_3, sum(case when (cst >= 6 and cst < 8) then 1 else 0 end)::float/count(call_id)::float * 100 AS range_4, sum(case when (cst >= 8 and cst < 10) then 1 else 0 end)::float/count(call_id)::float * 100 AS range_5, sum(case when (cst >= 10 and cst < 12) then 1 else 0 end)::float/count(call_id)::float * 100 AS range_6, sum(case when (cst >= 12 and cst < 14) then 1 else 0 end)::float/count(call_id)::float * 100 AS range_7, sum(case when (cst >= 14 and cst < 16) then 1 else 0 end)::float/count(call_id)::float * 100 AS range_8, sum(case when (cst >= 16) then 1 else 0 end)::float/count(call_id)::float * 100 AS range_9 from (select call_id, setup_time/1000 as cst from voice_test.call_details where setup_time > 0 and is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $op $os $ts and $total_calls_bb_ids_cond)tbl";
    //error_log($sql_call_connect_histogram."\n", 3, $log_file);
    $res_call_connect_histogram = pg_query($con, $sql_call_connect_histogram);
    if (pg_num_rows($res_call_connect_histogram) > 0) {
        while ($row=pg_fetch_assoc($res_call_connect_histogram)) {
            foreach($row as $k => $v) {
                $row[$k] = number_format($v, 1);
            }
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

// get network details for drop & block calls
elseif (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && isset($_POST['type']) && $_POST['type'] == 'get_rf_details_drop_block') {
    $country = isset($_POST['country']) ? strtolower($_POST['country']) : 'india';
    $city = isset($_POST['city']) ? strtolower($_POST['city']) : '';
//    $op = isset($_POST['op']) ? "lower(operator) = '".strtolower($_POST['op'])."'" : '';
    $op = isset($_POST['op']) ? "lower(operator) like '%".strtolower($_POST['op'])."%'" : '';
    $os = isset($_POST['os']) ? (($_POST['os']=='IOS') ? "lower(cd.os) = 'apple'" : "lower(cd.os) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = isset($_POST['start_dt']) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = isset($_POST['end_dt']) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (start_time BETWEEN $start_ts AND $end_ts)";
    }
    
    if(!empty($city)) {
        $city_lat_lng = pg_query($con, "select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if (pg_num_rows($city_lat_lng) > 0) {
            while ($rows = pg_fetch_assoc($city_lat_lng)) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( cd.lat ) ) * cos( radians( lng ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( cd.lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts .= " and cd.lat >= " . $country_lat_lng[$country]['min_lat'] . " and cd.lat <= " . $country_lat_lng[$country]['max_lat'] . " and lng >= " . $country_lat_lng[$country]['min_lng'] . " and lng <= " . $country_lat_lng[$country]['max_lng'];
    }
    
    $sql_rf_drop_block = "select TIMESTAMP 'epoch' + substring((ts+19800000)::varchar,0,11)::bigint * INTERVAL '1 second' as ts, rsrp, rsrq, sinr, psc_pci pci, cell_id, call_type from voice_test.rf_data rf inner join voice_test.call_details cd on (rf.ts=cd.end_time and rf.bb_id=cd.bb_id) where $total_calls_bb_ids_cond and is_mapped = 1 and ((call_type = 2 and voice_file_id is not null) or (call_type = 1)) and $op $os $ts";
    //error_log($sql_rf_drop_block."\n", 3, $log_file);
    $res_rf_drop_block = pg_query($con, $sql_rf_drop_block);
    
    $rf_details = false;
    if (pg_num_rows($res_rf_drop_block) > 0) {
        while ($row=pg_fetch_assoc($res_rf_drop_block)) {
            $call_drop = $call_block = 0;
            if($row['call_type'] == 1) {
                $call_block = 1;
            } elseif($row['call_type'] == 2) {
                $call_drop = 1;
            }
                        
            $rsrp = ($row['rsrp'] != null) ? number_format($row['rsrp'], 1) : '-';
            $rsrq = ($row['rsrq'] != null) ? number_format($row['rsrq'], 1) : '-';
            $sinr = ($row['sinr'] != null) ? number_format($row['sinr'], 1) : '-';
            
            foreach($row as $k => $v) {
                if($v == null) {
                    $row[$k] = '-';
                }
            }
            
            $rf_details[] = array('ts' => $row['ts'], 'drop' => $call_drop, 'block' => $call_block, 'pci' => $row['pci'], 'cell_id' => $row['cell_id'], 'rsrp' => $rsrp, 'rsrq' => $rsrq, 'sinr' => $sinr);
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
//    $op = isset($_POST['op']) ? "lower(operator) = '".strtolower($_POST['op'])."'" : '';
    $op = isset($_POST['op']) ? "lower(operator) like '%".strtolower($_POST['op'])."%'" : '';
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
        $ts .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lng >= " . $country_lat_lng[$country]['min_lng'] . " and lng <= " . $country_lat_lng[$country]['max_lng'];
        $ts_rf = (empty($ts_rf)) ? '' : $ts_rf . ' AND ';
        $ts_rf .= " lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lon >= " . $country_lat_lng[$country]['min_lng'] . " and lon <= " . $country_lat_lng[$country]['max_lng'];
    }
        
//    $sql_rf_mute = "select file_name, round(mute_duration/1000,1) as mute_duration, TIMESTAMP 'epoch' + substring((max(ts)+19800000)::varchar,0,11)::bigint * INTERVAL '1 second' as ts, round(CAST(avg(rsrp) as numeric),1) rsrp, round(CAST(avg(sinr) as numeric),1) sinr, round(CAST(avg(rsrq) as numeric),1) rsrq, string_agg(DISTINCT psc_pci::varchar, ',') as pci, string_agg(DISTINCT cell_id::varchar, ',') as cell_id, round(mute_location/1000,1) as mute_on from voice_test.rf_data rd inner join voice_test.voice_mute vm on (rd.voice_mute_id=vm.voice_mute_id) inner join voice_test.voice_files vf on (vm.voice_file_id=vf.voice_file_id) where is_mute = 1 and $op $os $ts and $total_calls_bb_ids_cond order by ts";
    
    $sql_rf_mute = "select rd.voice_mute_id, max(vf.file_name) file_name, round(max(mute_duration)/1000,1) as mute_duration, TIMESTAMP 'epoch' + substring((min(ts)+19800000)::varchar,0,11)::bigint * INTERVAL '1 second' as ts, round(CAST(avg(rsrp) as numeric),1) rsrp, round(CAST(avg(sinr) as numeric),1) sinr, round(CAST(avg(rsrq) as numeric),1) rsrq, string_agg(DISTINCT psc_pci::varchar, ',') as pci, string_agg(DISTINCT cell_id::varchar, ',') as cell_id, round(max(mute_location)/1000,1) as mute_on from voice_test.rf_data rd inner join voice_test.voice_mute vm on (rd.voice_mute_id=vm.voice_mute_id) inner join voice_test.voice_files vf on (vm.voice_file_id=vf.voice_file_id) where is_mute = 1 and $op_rf $os_rf $ts_rf and $total_calls_bb_ids_cond group by rd.voice_mute_id";
    //error_log($sql_rf_mute."\n", 3, $log_file);
    $res_rf_mute = pg_query($con, $sql_rf_mute);
    
    $rf_details = false;
    $voice_file_ids = false;
    if (pg_num_rows($res_rf_mute) > 0) {
        while ($row=pg_fetch_assoc($res_rf_mute)) {            
            foreach($row as $k => $v) {
                if($v == null) {
                    $row[$k] = '-';
                }
            }
            $row['file_name'] = "http://172.104.177.75/call_analyse/wav/voice_uploads/" . $row['file_name'];
            $rf_details[] = $row;
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
//    $op = isset($_POST['op']) ? "lower(operator) = '".strtolower($_POST['op'])."'" : '';
    $op = isset($_POST['op']) ? "lower(operator) like '%".strtolower($_POST['op'])."%'" : '';
    $os = isset($_POST['os']) ? (($_POST['os']=='IOS') ? "lower(os) = 'apple'" : "lower(os) != 'apple'") : '';
    $os = (empty($op)) ? $os : 'AND ' . $os;
    
    $start_ts = isset($_POST['start_dt']) ? strtotime($_POST['start_dt'].' 00:00:01').'000' : '';
    $end_ts = isset($_POST['end_dt']) ? strtotime($_POST['end_dt'].' 23:59:59').'999' : '';
    
    $ts = '';
    if(!empty($start_ts) && !empty($end_ts)) {
        $ts = " AND (start_time BETWEEN $start_ts AND $end_ts)";
    }
    
    if(!empty($city)) {
        $city_lat_lng = pg_query($con, "select latitude, longitude from city_lat_long where lower(city) = '" . strtolower($city) . "'");
        if (pg_num_rows($city_lat_lng) > 0) {
            while ($rows = pg_fetch_assoc($city_lat_lng)) {
                $lat = $rows['latitude'];
                $lng = $rows['longitude'];
            }
            $ts .= " and (3959 * acos (cos ( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($lng) ) + sin ( radians($lat) ) * sin( radians( lat ) ))) < 30";
        }
    }
    
    if(isset($country_lat_lng[$country])) {
        $ts .= " and lat >= " . $country_lat_lng[$country]['min_lat'] . " and lat <= " . $country_lat_lng[$country]['max_lat'] . " and lng >= " . $country_lat_lng[$country]['min_lng'] . " and lng <= " . $country_lat_lng[$country]['max_lng'];
    }
         
    // Total Calls
    $sql_total_calls = "select call_type, count(call_id) calls from voice_test.call_details where is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $op $os $ts and $total_calls_bb_ids_cond group by call_type";
    //error_log($sql_total_calls."\n", 3, $log_file);
    $res_total_calls = pg_query($con, $sql_total_calls);
    $total_calls = $block_calls = $drop_calls = 0;
    if (pg_num_rows($res_total_calls) > 0) {
        while ($row=pg_fetch_assoc($res_total_calls)) {
            $total_calls += $row['calls'];
            if($row['call_type'] == 1) {
                $block_calls = $row['calls'];
            } elseif($row['call_type'] == 2) {
                $drop_calls = $row['calls'];
            }
        }
    }
    
    $call_drop_rate = $call_block_rate = '0 %';
    if($total_calls > 0) {
        $call_drop_rate = number_format($drop_calls/($total_calls-$block_calls) * 100, 1) . ' %';
        $call_block_rate = number_format($block_calls/$total_calls * 100, 1) . ' %';
    }
        
    // Mute Calls
    $sql_mute_calls = "select is_mo, count(call_id) mute_calls from voice_test.call_details where mute_count > 0 AND $op $os $ts group by is_mo";
    //error_log($sql_mute_calls."\n", 3, $log_file);
    $res_mute_calls = pg_query($con, $sql_mute_calls);
    $mute_calls_downlink = $mute_calls_uplink = 0;
    if (pg_num_rows($res_mute_calls) > 0) {
        while ($row=pg_fetch_assoc($res_mute_calls)) {
            if($row['is_mo'] == 1) {
                $mute_calls_downlink = $row['mute_calls'];
            } elseif($row['is_mo'] == 0) {
                $mute_calls_uplink = $row['mute_calls'];
            }
        }
    }
    
    $mute_call_rate_downlink = $mute_call_rate_uplink = 0;
    if($total_calls > 0) {
        $mute_call_rate_downlink = number_format($mute_calls_downlink/($total_calls-$block_calls) * 100, 1) . ' %';
        $mute_call_rate_uplink = number_format($mute_calls_uplink/($total_calls-$block_calls) * 100, 1) . ' %';
        if($mute_calls_uplink == 0) {
            $mute_call_rate_uplink = '-';
        }
    }
        
    // Call Connect
    $sql_call_connect = "select avg(setup_time)/1000 as avg_cst from voice_test.call_details where setup_time > 0 and is_mapped = 1 and ((call_type in (0,2) and voice_file_id is not null) or (call_type = 1)) and $op $os $ts and $total_calls_bb_ids_cond";
    //error_log($sql_call_connect."\n", 3, $log_file);
    $res_call_connect = pg_query($con, $sql_call_connect);    
    $call_connect = 0;
    if (pg_num_rows($res_call_connect) > 0) {
        while ($row=pg_fetch_assoc($res_call_connect)) {
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
    $json_array['message']['qry'] = $sql_total_calls;
    echo json_encode($json_array);
}

pg_close($con);
exit();
?>
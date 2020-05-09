<?php
date_default_timezone_set('Asia/Kolkata');

include_once("ajax/obj_connection.php");

// filename for download
$filename = "call_drop_block_".date("Y-m-d-H-i-s").".csv";

header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: text/csv");

$out = fopen("php://output", 'w');

$sql = "select call_no, if((setup_time is null or setup_time <= 0) and call_duration < 10, 1, 0) as call_block, if(call_duration > 10 and call_duration < 180, 1, 0) as call_drop from (select call_no, sum(setup_time) setup_time, GROUP_CONCAT(distinct CALL_STATE SEPARATOR ',') states, TIME_TO_SEC(TIMEDIFF(FROM_UNIXTIME(SUBSTR(max(TIMESTAMP), 1, 10)), FROM_UNIXTIME(SUBSTR(min(TIMESTAMP), 1, 10)))) call_duration from black_box_datas where MAKE != 'Apple' and call_no > 0 and CALL_STATE > 0 and BB_ID in ('1234', '5285') group by call_no)tbl";
$res = $con->query($sql);

if ($res->num_rows > 0) {
    $flag = false;
    while ($row=$res->fetch_assoc()) {
        if(!$flag) {
            fputcsv($out, array_keys($row), ',', '"');
            $flag = true;
        }

        fputcsv($out, array_values($row), ',', '"');
    }
}
fclose($out);
?>
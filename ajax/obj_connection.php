<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$ip = '103.209.145.223';          // 'localhost';
$host        = "host = ".$ip;
$port        = "port = 30001";
$dbname      = "dbname = call_analyzer";
//$credentials = "user = postgres";
$credentials = "user = call_analyzer password = Hdyctdutst";

$con = pg_connect( "$host $port $dbname $credentials"  );
if (!$con) {
    echo('We are having some technical upgradation going on. please login try later.');
    echo pg_last_error($con);
    exit(1);
}
?>
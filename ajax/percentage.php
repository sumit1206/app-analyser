<?php
include("../../connection/con.php");
date_default_timezone_set('Asia/Kolkata');
//ini_set('display_errors', 1);

//echo $updateQuery;


$sessionId = 'eEqL0t7sBqY3';
$q = getPercentageQuery($con, $sessionId);
echo "<br><br><br>q= ".$q;

function getPercentageQuery($con, $session){
	$first_time = 0;
	$second_time = 0;
	$last_time = 0;
	$count = 0;
	$times = array();
	$statuses = array();
	$total_interval = 0;
			
	$sqlTime = 
	"select timestamp, status
	from rf_data_with_buffer
	where recording = 'true' and session = '$session' order by timestamp asc";
	//echo $sqlTime."<br>";
	$timeResult = pg_query($con,$sqlTime);
	if($timeResult){
		$timeResultCount = pg_num_rows($timeResult);
		if($timeResultCount > 0 ){
			while($rowTimeResult = pg_fetch_assoc($timeResult)){
				$t = $rowTimeResult['timestamp'];
				$s = $rowTimeResult['status'];
				array_push($times, $t);
				array_push($statuses, $s);
				if($count == 0){
					$first_time = $t;
				}/*else if($count == 1){
					$second_time = $t;
				}*/else if($count == $timeResultCount - 1){
					$last_time = $t;
				}
				if($count != 0){
					$total_interval = $total_interval + ($times[$count] - $times[$count - 1]);
				}
				$count = $count + 1;
			}
			//echo $first_time."-".$second_time."-".$last_time;
		}else return "";
	}else return "";
	$playtime = $last_time - $first_time;
	$interval = $total_interval / $timeResultCount;
	//$interval = $second_time - $first_time;
	//echo "playtime ".$playtime;
	//echo "interval ".$interval;

	$percent = ($interval / $playtime) * 100;
	//echo "percent ".$percent;

	$noOfData = count($times);
	$percentage = 0;
	$UPDATE_QUERY = "update rf_data_with_buffer set percentage = case ";
	for($i = 0; $i < $noOfData; $i++){
		$time = $times[$i];
		$status = $statuses[$i];
		$PERCENTAGE_CASE = "when timestamp = '$time' then $percentage ";
		if($status == 0){
			$percentage = $percentage + $percent;
		}
		$UPDATE_QUERY = $UPDATE_QUERY.$PERCENTAGE_CASE;
	}
	$UPDATE_QUERY = $UPDATE_QUERY."else 0 end where session = '$session'";

	//echo "<br>UPDATE_QUERY ".$UPDATE_QUERY;
	return $UPDATE_QUERY;
}
?>


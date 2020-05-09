<?php
ini_set("display_errors",0);
error_reporting(E_ALL);

include_once("obj_connection.php");
$newarray 	= array();
$lat = '';
$long = '';

	  $sql = "select timezone('Asia/Kolkata',to_timestamp(ts/1000)) as time,
	               lat,
				   lon,
				  timezone('Asia/Kolkata',to_timestamp(ts/1000))  - interval '2' second as t1,
				  timezone('Asia/Kolkata',to_timestamp(ts/1000))  + interval '2' second as t2,
				  ts
				  from voice_test.rf_data where bb_id  = 5285 and lat is null order by ts ";


		$res  = pg_query($con,$sql);
		    while ($row = pg_fetch_row($res)) {
			
 		 	 $sql1 = "select * from ( select timezone('Asia/Kolkata',to_timestamp(ts/1000)) as time  ,lat,lon,ts  from voice_test.rf_data where bb_id  = 1234 and lat is not null ) tbl where  time between '".$row[0]."'  and '".$row[4]."' order by time desc limit 1";
		
				$res1  = pg_query($con,$sql1);
				while ($row1 = pg_fetch_row($res1)) {
					$lat = $row1[1];
					$long = $row1[2];

		
		}
		
		 if($lat !=''){
				$sql2 = "update voice_test.rf_data set lat = ".$lat." , lon = ".$long."  where ts = ".$row[5]." and bb_id = 5285 ";
				$res2 = pg_query($con,$sql2);
			}
			
			//$newarray = array($row[0],$lat,$long,$row[5]);     
			//echo implode(",", $newarray)."\n";

			}
?>
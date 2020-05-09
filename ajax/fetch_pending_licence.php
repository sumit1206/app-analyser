<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$sql = "SELECT imei,inserted_by FROM imei_info WHERE is_pending=1";
//echo $sql;
$res = pg_query($con,$sql);
$response= array();
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){ 
	    $response['success']=1; 
        $response['data']= array();	
	    while ($row=pg_fetch_assoc($res)) {
			$details['imei'] = $row['imei'];
			$details['inserted_by'] = $row['inserted_by'];
		    array_push($response['data'], $details);
		}		 		
	}
}else{
		$response['success']= 0 ;
}
echo json_encode ($response);
?>
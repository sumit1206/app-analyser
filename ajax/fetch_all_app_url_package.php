<?php
include("../../connection/con.php");
$sql = "SELECT * FROM public.app_url ORDER BY sl_no ASC";
$res = pg_query($con,$sql);
$response= array();
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){ 
	    $response['success']=1; 
        $response['data']= array();	
	    while ($row=pg_fetch_assoc($res)) {
			$details['sl_no'] = $row['sl_no'];
			$details['app_name'] = $row['app_name'];
			$details['package_name'] = $row['package_name'];
			$details['url'] = $row['url'];
		    array_push($response['data'], $details);
		}		 		
	}
}else{
		$response['success']= 0 ;
}
echo json_encode ($response);
?>
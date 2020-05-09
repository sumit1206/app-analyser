<?php 
include("../../connection/con.php");
date_default_timezone_set("Asia/Kolkata");
//ini_set("display_errors",1);
include("../../connection/con.php");
$from_timestamp =$_GET['from_date'] ;
$to_timestamp =$_GET['to_date'] ;
if($to_timestamp == ""){
	$to_timestamp = $from_timestamp."23:59:59";
}else{
	$to_timestamp = $to_timestamp."23:59:59";
}
$from_timestamp = $from_timestamp."00:00:01";
$application =$_GET['application'];
$unx_to = strtotime($to_timestamp) ;
$unx_frm = strtotime($from_timestamp) ;
$operator = $_GET['operator'];
$os = $_GET['operatingSystem'] ;
$city = $_GET['city'] ;
$sql="SELECT DISTINCT  app_name
	FROM public.performance where timestamp between '$unx_frm' and '$unx_to' and os = '$os'  and wifi_state != '1' and operator='$operator' ;";
if($operator == 'WIFI'){
	$sql = "SELECT DISTINCT  app_name
	FROM public.performance where timestamp between '$unx_frm' and '$unx_to' and os = '$os' and wifi_state = '1' and wifi_state != '-';";
}
//echo $sql."</br>";
$response= array();
$res = pg_query($con,$sql);
$response['details']= array();
if($res){
	
	$numRows=pg_num_rows($res);
	if($numRows>0){ 
		while ($row=pg_fetch_assoc($res)) {
		  $app_name = $row['app_name'];
		  $response['success']=1;
		  $sql1= "SELECT SUM(point) FROM public.performance where app_name = '$app_name' and (timestamp between '$unx_frm' and '$unx_to') and os = '$os' and wifi_state != '1' and operator='$operator';";
		  if($operator == 'WIFI'){
		  	$sql1= "SELECT SUM(point) FROM public.performance where app_name = '$app_name' and (timestamp between '$unx_frm' and '$unx_to') and os = '$os' and wifi_state = '1' and wifi_state != '-';";
		  }
		  //echo $sql1."</br>";
		  $res1 = pg_query($con,$sql1);
		  if($res1){
		       $details=array();
	           $numRows1=pg_num_rows($res1);
	           if($numRows1>0){ 
                  while ($row1=pg_fetch_assoc($res1)) {
                      $details['point']=ceil($row1['sum']);
                      $details['app_name']=$app_name;
                      array_push($response['details'], $details);  
                  }
	           }
	        }
		 } 
	}
}
echo json_encode($response);
?>
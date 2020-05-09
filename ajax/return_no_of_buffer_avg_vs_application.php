<?php
include("../connection/con.php");
//ini_set("display_errors",1);
$from_timestamp =$_GET['from_timestamp'] ;
$to_timestamp =$_GET['to_timestamp'] ;
if($to_timestamp == ""){
	$to_timestamp = $from_timestamp."23:59:59";
}
$from_timestamp = $from_timestamp."00:00:01";
$unx_to = strtotime($to_timestamp);
$unx_frm = strtotime($from_timestamp);
$sql = "SELECT DISTINCT `running_app` FROM `rf_data_with_buffer` WHERE (`timestamp` BETWEEN '$unx_frm' AND '$unx_to')";
$response= array();
$res = $con->query($sql);
//echo $sql;
$response= array();
if($res){
	$numRows=$res->num_rows;
	if($numRows>0){ 
		while ($row=$res->fetch_assoc()) {
		  $running_app = $row['running_app'];
		  $sql1="SELECT COUNT(`session`) ,  SUM(`no_of_buffer_per_session`) FROM `rf_data_with_buffer` WHERE `running_app` = '$running_app' AND (`timestamp` BETWEEN '$unx_frm' AND '$unx_to') AND `no_of_buffer_per_session` != '-'";
		  $res1 = $con->query($sql1);
		  if($res1){
	            $numRows=$res1->num_rows;
	            if($numRows>0){ 
		             while ($row1=$res1->fetch_assoc()) {
						 $total_buffer = $row1['SUM(`no_of_buffer_per_session`)'];
						 $count_session = $row1['COUNT(`session`)'];
						 $average = $total_buffer / $count_session ;
						 if($average == false)
						 {
							 $average = 0;
						 }
                         $details['running_app']=$running_app;
                         $details['average']=$average;	
                         array_push($response, $details);						 
						 
					 }
			    }else{
				          $details['running_app']=$running_app;
                          $details['average']=0;	
                          array_push($response, $details);
			         }
		    }
        }
	}
}
echo json_encode ($response);
?>
<?php
include("../../connection/con.php");
//ini_set("display_errors",1);
$sql = "SELECT number FROM number";
$res = pg_query($con,$sql);
if($res){
	$numRows=pg_num_rows($res);
	if($numRows>0){ 
		while ($row=pg_fetch_assoc($res)) {
			echo $row['number'];
		}
	}else{
		echo "No number found.";
	}
}else{
	echo "Failed to execute query.";
}
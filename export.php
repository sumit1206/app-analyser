<?php

include_once("ajax/obj_connection.php");
error_reporting(E_ALL);
ini_set('display_errors',1);
//ini_set('max_execution_time',-1);
ini_set('max_execution_time',-1);
pg_query($con,"SET statement_timeout TO '80000s'");


if(isset($_POST['submit'])){


	$qry = $_POST['query'];
	if(strpos($qry,"select") === false){
		echo "Query Not Allowed";
		die;
	}else{
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=data.csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		$res  = pg_query($con,$qry);
		$i = pg_num_fields($res);
		$header_arr = array();
		for ($j = 0; $j < $i; $j++) {
			  array_push($header_arr,pg_field_name($res, $j));
		}
		echo('"'.implode("\",\"",$header_arr)."\"\n");
		while($row = pg_fetch_row($res)){
			echo('"'.implode("\",\"",$row)."\"\n");
			//echo implode("@",$row)."\n";
		}
		
	}
	pg_close($con);
	exit;
}
?>

<form action="export.php" method="post">
<textarea rows="4" cols="50" name="query" style="width:100%" >
<?php echo isset($_POST['submit']) ? $_POST['query'] : "" ?>
</textarea>
<input type="submit" name="submit" value="Query">
</form>
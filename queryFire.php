<?php

//session_start();

//$user_id = isset($_SESSION['user_id']) && $_SESSION['user_id'] ? $_SESSION['user_id'] : 0;
// 2-Dinesh,6-Ashish,3-bhushan,4-dhirendra
/* if($user_id == 2 || $user_id == 6 || $user_id == 3 || $user_id == 55 || $user_id == 60 || $user_id == 1){

}else{
	echo "You are Not Allowed";
	die;	
} */
error_reporting(E_ALL);
//ini_set('display_errors',1);
//pg_query($con,"SET statement_timeout TO '8000000000s'");
//pg_query($con,"SET statement_timeout TO 8000000000");  

$result = array();
?>
<form action="queryFire.php" method="post" >
<textarea rows="4" cols="50" name="query" style="width:100%" >
<?php echo isset($_POST['submit']) ? $_POST['query'] : "" ?>
</textarea>
<input type="submit" name="submit" value="Query">
</form>
<table border='1' cellspacing='0' colspan='0' cellpadding ='2' style="font-size:12px">
<?php  
//print_r($result);
if(isset($_POST['submit'])){
	include_once("ajax/obj_connection.php");

	$qry = $_POST['query'];
	/* if(strpos($qry,"select") === false){
		echo "Query Not Allowed";
		die;
	} */  
	/* if(strpos($qry,"delete") != -1 || strpos($qry,"truncate") != -1 || strpos($qry,"empty") != -1){
		echo "Not allowed to run this query";
	}else{
		echo "in"; */
		$res  = pg_query($con, $qry);
                
                $i = pg_num_fields($res);
		echo "<tr>";
		for ($j = 0; $j < $i; $j++) {
			  echo "<th>".pg_field_name($res, $j)."</th>";
		}
		echo "</tr>";
		while($row = pg_fetch_row($res)){
			//array_push($result,$row);
			echo "<tr><td>".implode("</td><td>",$row)."</td></tr>";
		}
		pg_close($con);
	//}
}
?>
</table>
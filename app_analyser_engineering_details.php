<?php
include("../connection/con.php");
//ini_set("display_errors",1);
$sql_state="SELECT DISTINCT state FROM rf_data_with_buffer WHERE state != ''  and state != '-' ";
$res_s = pg_query($con,$sql_state);
if($res_s){
  $i=0;
  $numRows_s=pg_num_rows($res_s);
  if($numRows_s>0){
    $city = array();
    while($row_s=pg_fetch_assoc($res_s)){
             $city[$i] = $row_s['state'];
             $i=$i+1;      
    } 
  } 
}
//echo json_encode($response);
?>
<style>
.modal {
	display: none; /* Hidden by default */
	position: fixed; /* Stay in place */
	z-index: 1; /* Sit on top */
	padding-top: 100px; /* Location of the box */
	left: 0;
	top: 0;
	width: 100%; /* Full width */
	height: 100%; /* Full height */
	overflow: auto; /* Enable scroll if needed */
	background-color: rgb(0,0,0); /* Fallback color */
	background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
  }
  
  /* Modal Content */
  .modal-content {
	position: relative;
	background-color: #fefefe;
	margin: auto;
	padding: 0;
	max-height: calc(100vh - 210px);
    overflow-y: auto;
	border: 1px solid #888;
	width: 80%;
	box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
	-webkit-animation-name: animatetop;
	-webkit-animation-duration: 0.4s;
	animation-name: animatetop;
	animation-duration: 0.4s
  }
  /* Add Animation */
  @-webkit-keyframes animatetop {
	from {top:-300px; opacity:0} 
	to {top:0; opacity:1}
  }
  
  @keyframes animatetop {
	from {top:-300px; opacity:0}
	to {top:0; opacity:1}
  }
  
  /* The Close Button */
  .close {
	color: #fff;
	float: right;
	
	font-size: 15px;
	font-weight: bold;
  }
  
  .close:hover,
  .close:focus {
	color: #fff;
	text-decoration: none;
	cursor: pointer;
  }
  
  .modal-header {
	padding:5px;
	background-color: #404556;
	color: white;
	border-bottom: 1px solid #dddddd;
	border-top-left-radius: 3px;
    border-top-right-radius: 3px;
  }
  
  .modal-body {padding: 2px 16px;}
  
  .modal-footer {
	padding: 2px 16px;
	background-color: #5cb85c;
	color: white;
  }
</style>
<?php include('header_new.php'); ?>

<section id="page-content">
    <div class="body-content animated fadeIn">
		<div class= "row">
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
              <div class="panel rounded shadow">
                <!-- Card Header - Dropdown -->
				<div class="panel-heading">
                      <div class="pull-left">
                          <h3 class="panel-title" style="color: #fff">Filters</h3>
                       </div>
                    <div class="clearfix"></div>
                 </div>
				<div class="panel-body">
                        <!--<form class="form-inline" >-->
                            <div class="form-body"> 
                                <div class="col-lg-2 col-md-2 col-sm-12">
                                    <select class="form-control select_width" id="city_drop_down">
                                        <option value="">City</option>
										<?php
											foreach($city as $item){ 
										?>
										<option value="<?php echo $item;?>"><?php echo $item;?></option>
										<?php
										} 
										?>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-12">
                                    <select class="form-control select_width" id="os_drop_down">
                                        <option value="">Operating System</option>
                                    </select>
                                </div>
								<div class="col-lg-2 col-md-2 col-sm-12">
                                    <select class="form-control select_width" id="operator">
                                        <option value="">Operator</option>
										<option value="All application">All Application</option>
                                    </select>
                                </div>
								<div class="col-lg-2 col-md-2 col-sm-12">
                                    <select class="form-control select_width" id="application_selection">
                                    <option value="">Select Application</option>
                                    </select>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-12">
                                    <select class="form-control select_width" id="trend_drop_down"> 
                                       <option value="">Trend</option>
                                       <option value="day">Day-wise</option>
                                       <option value="custom">Custom</option>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-12">
								 <input type="data" name="yearmonth" id="single_day" autocomplete="off" value="" class="form-control select_width"  style="display: none;" placeholder="Date">
                                    <div class="input-group input-daterange"  style="display: none;">
                                        <input class="form-control" id="start_dt" value=""  autocomplete="off" placeholder="Date">
                                        <div id= "to_text" class="input-group-addon">TO</div>
                                        <input class="form-control" id="end_dt" value=""  autocomplete="off" placeholder="Date">
                                    </div>
                                </div> 
								<div class="clearfix"></div>
							<div class="form-body">
								<div class="col-md-12 text-center">
									<button type="button" name="btn_submit" id="btn_submit_network_details" class="btn btn-info">Submit</button>
                                    <button type="button" name="btn_export" id="btn_export" class="btn btn-info">Export</button>
                                </div>
                            </div>   
                         </div>
                       <!-- </form>   -->                     
				</div>
              </div>
            </div>
		</div>
          <!-- Content Row -->
          <div class="row">
            <!-- Content Column -->
		   <div class="col-lg-12 col-md-12 col-sm-12">
              <!-- Project Card Example -->
              <div class="panel">
					<div class="panel-heading">
					<div class="pull-left loader" id ="kpi_details_plot"></div> 
                        <div class="pull-left">
                            <h3 class="panel-title">KPI details</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                <div class="panel-body table-responsive-sm">
				<!--here is table content table form-body table-striped table-bordered nowrap no-footer dataTable table-responsive-sm-->
				<!--<div class="col-lg-12 col-md-12 col-sm-12" id="kpi_details_plot" style="display: none; width:200px;background-color: black; position: absolute; left: 40%; ;color: #fff; padding: 0 10px;">Loading KPI details...</div>-->
				<table id="table_data" class="table table-striped table-bordered"><!--class="table table-condensed table-bordered" -->
				</table>				
                </div>
              </div>
           </div>
		   <!--Network details
		     <div class="col-lg-12 col-md-12 col-sm-12">
              <div class="panel">
					<div class="panel-heading">
					<div class="pull-left loader" id ="network_details_plot"></div> 
						<div class="pull-left">
							<h3 class="panel-title">Network details</h3>
						</div>
						<div class="clearfix"></div>
					</div>
                <div class="panel-body table-responsive-sm">
					<div id ="network_details_panel" class="col-lg-12 col-md-12 col-sm-12">
					<table id="network_data" class="table table-condensed table-striped table-bordered" ></table> 
					</div>
					<div id ="wifi_details_panel" class="col-lg-12 col-md-12 col-sm-12">
					<table id="wifi_network_data" class="table table-condensed table-striped table-bordered" ></table>
					</div>
                </div>
              </div>
           </div>-->
		</div><!--end of the row-->
	</div>	
	
<div id="myModal" class="modal">
 
  <div class="modal-content ">
    <div class=" modal-header">
      <span class=" panel-title close">&times;</span>
      <h3 class="panel-title">Network details</h3>
    </div>
    <div class="modal-body">
		<div id ="network_details_panel">
			<table id="rf_data_table" class="table table-condensed table-striped table-bordered" ></table> 
		</div>
		<div id ="wifi_details_panel"  id="scroll-wrap">
			<table id="rf_wifi_network_data" class="table table-condensed table-striped table-bordered" ></table>
		</div>
    </div>
  </div>

</div>
  <!-- End of Page Wrapper -->
  <?php include('footer_new.php')?>
<script>
    $( "#start_dt" ).datepicker({
	todayHighlight: true,
	autoclose: true
	}).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        var newdate = new Date(minDate);
        newdate.setDate(minDate.getDate()+1);
        $('#end_dt').datepicker('setStartDate', newdate);
    });
	
    $("#end_dt").datepicker({
	todayHighlight: true,
	autoclose: true
	});
	$("#single_day").datepicker({
	todayHighlight: true,
	autoclose: true
	});
</script>
<script>
var fromDate='',toDate='',singleDate='',selApplication='',operatingSystem='',operator='',trend ='',city='';
var network_loading,kpi_loading;
var sessionIdValue;
var type='';
var network_dtls_btn;

var submit_btn,submit_btn_val; 
var total_ajax_calls = 2;
var total_ajax_complete = 0;
$( document ).ready(function(){
	
	function loadDatePicker(type) {
		type = fetchData('trend_cookie');   
		if(type =='') {
			//$('#start_dt').show();
			$('#single_day').show();
			$('.input-daterange').hide();
			$('#start_dt').val("");		
			$('#end_dt').val("");
			$('#single_day').val("");
		}
		else if(type == 'custom') {
			$('.input-daterange').show();
			$('#single_day').hide();
			$('#start_dt:text').val("");
			$('#end_dt:text').val("");
			$('#single_day:text').val("");
		}
		else if(type == 'day') {
			$('#single_day').show();
			$('.input-daterange').hide();
			$('#start_dt:text').val(""); 
			$('#end_dt:text').val(""); 
			$('#single_day:text').val("");
		}
	}
	
	loadDatePicker(type);
	network_loading = $('#network_details_plot');
	kpi_loading = $('#kpi_details_plot');
	//setCookieData(); 
	//callAjax();
	city = fetchData('city_cookie');
	$("#city_drop_down").val(city);
	fetchFilterDataWithCity(city);
	setCookieData();
	dateValidation();
  
	$("#start_dt").change(function(){
	fromDate='';
	toDate='';
	fromDate = $("#start_dt").val();
	saveData('fromDate',fromDate);	
    saveMainData();
	//callAjax();
	}); 
	$("#single_day").change(function(){
	fromDate='';
	toDate='';
	singleDate = $("#single_day").val();
	saveData('singleDate',singleDate); 
	fromDate = fetchData('singleDate');
	//callAjax();
	});
	
	$("#end_dt").change(function(){
	fromDate='';
	toDate='';	
    saveMainData();
	//setCookieData();
	//dataFetch();
	//networkDetailsData
	//callAjax();
	});
	$("#application_selection").change(function(){
    saveMainData();
	//setCookieData();
	//callAjax();
	});
	$("#os_drop_down").change(function(){
    saveMainData();
	//setCookieData();
	//dataFetch();
	//networkDetailsData();
	//callAjax();
	});
	$("#operator").change(function(){
    saveMainData();
	//setCookieData();
		//dataFetch();
		//networkDetailsData();
		//callAjax();
	}); 
	$("#btn_export").click(function(){
    saveMainData();
	setCookieData();
	export_btn = $(this);
	export_btn_val = $(this).html();
	if(fetchData('trend_cookie')==''){
	//$('#error_text').show();
	//$('#error_text').html("Please select trend*");
	}
	else if(fetchData('trend_cookie')=='day'){
	singleDate = fetchData('singleDate');
	console.log("chat date single and from"+singleDate);
	fromDate=singleDate;
	downloadData();
	}
	else(fetchData('trend_cookie')=='custom')
	{
		downloadData();
	}
	downloadData();
	}); 
	$('#trend_drop_down').change(function() {
		type = $(this).val();
		saveData('trend_cookie',type);
		loadDatePicker(type);
		saveMainData();
		setCookieData();
		//callAjax();
	});
	$("#city_drop_down").change(function(){
		saveMainData();
		setCookieData();
		fetchFilterDataWithCity(city);
		//callAjax();
	});
	$("#btn_submit_network_details").click(function(){
		saveMainData();
		setCookieData();
		dateValidation();
	});
});
function dateValidation(){
	
	total_ajax_complete = 0;
	submit_btn = $('#btn_submit_network_details');
	submit_btn_val = $('#btn_submit_network_details').html();	
	disableElement(submit_btn,"Please wait...");
	
	if(fetchData('trend_cookie')==''){
		$('#error_text').show();
		$('#error_text').html("Please select trend*");
	}
	if(fetchData('trend_cookie')=='day'){
		singleDate = fetchData('singleDate');
		//console.log("chat date single and from"+singleDate);
		fromDate=singleDate;
		saveData('fromDate',fromDate); 
		callAjax();
	}
	else(fetchData('trend_cookie')=='custom')
	{		
		callAjax();
	}
}
function saveData(key, data){
  $.cookie(key, data);
}

function fetchData(key){
  var data = $.cookie(key);
  return data;
}

function setCookieData(){
  singleDate = fetchData('singleDate');
  $("#single_day").val(singleDate);
  console.log("single date"+fromDate);  
  fromDate = fetchData('fromDate');
  $("#start_dt").val(fromDate);
  toDate = fetchData('toDate');
  $("#end_dt").val(toDate);
  selApplication = fetchData('selApplication');
  $("#application_selection").val(selApplication);
  operatingSystem = fetchData('operatingSystem');
  $("#os_drop_down").val(operatingSystem);
  operator = fetchData('operator');
  $("#operator").val(operator); 
  trend = fetchData('trend_cookie');
  $("#trend_drop_down").val(trend);
  city = fetchData('city_cookie');
  $("#city_drop_down").val(city);
}
// saving data to cookie
function saveMainData(){  
  fromDate = $("#start_dt").val();
  saveData('fromDate',fromDate);
  toDate = $("#end_dt").val(); 
  saveData('toDate',toDate);
  singleDate = $("#single_day").val();
  console.log("single"+singleDate);
  saveData('singleDate',singleDate);
  selApplication = $("#application_selection").val();
  saveData('selApplication',selApplication);
  operatingSystem = $("#os_drop_down").val();
  saveData('operatingSystem',operatingSystem);
  operator = $("#operator").val();
  saveData('operator',operator);
  console.log("trend saving"+trend);
  trend =  $("#trend_drop_down").val();
  saveData('trend_cookie',trend);
  city =  $("#city_drop_down").val();
  saveData('city_cookie',city);
}

function callAjax(){
  $('#network_details_panel').hide();
  $('#wifi_details_panel').hide();  
  $("#table_data").html('');
  $("#rf_data_table").html('');
  $('#rf_wifi_network_data').html('');
  console.log("app"+selApplication);
  if(operator == 'WIFI')
  {
	$('#wifi_details_panel').show();
	//$('#rf_data_table').hide();   
	fetchWifiKpiWithFilter(fromDate,toDate,operatingSystem,selApplication,city);
  }else
  {
	$('#network_details_panel').show();   
	dataFetch(fromDate,toDate,operatingSystem,selApplication,city);  
  }
}
//not used currently
function networkDetailsData(){
  inProgress(network_loading);
	$.ajax({
        url: "ajax/fetch_rf_data_with_filter.php",
        type: "GET",
        data:{from_timestamp:fromDate,to_timestamp:toDate,operating_system:operatingSystem,operator:operator,application:selApplication},
		error: function(jqXHR, textStatus, errorThrown)
		{
			console.log(textStatus, errorThrown);
        },
		success:function(result) 
		{ 
		stopProgress(network_loading);
		   sessions = [];
		   date_times = [];
		   spns = [];
		   techs = [];
		   sub_techs = [];
		   rsrps = [];
		   rsrqs = [];
		   sinrs = [];
		   cell_ids = [];
		   psc_pcis = [];
		   cqis = [];
		   result = jQuery.parseJSON(result)
		   var arr = result.details;
		   for (var i = 0; i < arr.length; i++)
		{
			sessions.push(arr[i].session);
			date_times.push(arr[i].date_time);
			spns.push(arr[i].spn);
			techs.push(arr[i].tech);
			sub_techs.push(arr[i].sub_tech);
			rsrps.push(arr[i].rsrp);
			rsrqs.push(arr[i].rsrq);
			sinrs.push(arr[i].sinr);
			cell_ids.push(arr[i].cell_id);
			psc_pcis.push(arr[i].psc_pci);
			cqis.push(arr[i].cqi);
		}
		   
		   var table_str = "<thead><tr>";
		   table_str += "<th>Sl.No</th>";
		   table_str += "<th>SESSION</th>";
		   table_str += "<th>Date/Time</th>";
		   table_str += "<th>SPN</th>";
		   table_str += "<th>Tech</th>";
		   table_str += "<th>Sub Tech</th>";
		   table_str += "<th>RSRP</th>";
		   table_str += "<th>RSRQ</th>";
		   table_str += "<th>SINR</th>";
		   table_str += "<th>PCI</th>";
		   table_str += "<th>CELL ID</th>";
		   table_str += "<th>CQI</th>";
		   table_str += "</tr></thead>";
		   //table_str += "<tbody>";
		   //table_str += "<td>" + result + "</td>";
		   //table_str += "</tbody>";
		 
		 for (var i=0;i<sessions.length;i++){
         var pos = parseInt(1)+parseInt(i);
			table_str += "<tr>";
            table_str += "<td>" + pos + "</td>";
            table_str += "<td>" + sessions[i] + "</td>";
			table_str += "<td>" + date_times[i] + "</td>";
            table_str += "<td>" + spns[i] + "</td>";
            table_str += "<td>" + techs[i] + "</td>";
			table_str += "<td>" + sub_techs[i] + "</td>";
			table_str += "<td>" + rsrps[i] + "</td>";
			table_str += "<td>" + rsrqs[i] + "</td>";
			table_str += "<td>" + sinrs[i] + "</td>";
			table_str += "<td>" + cell_ids[i] + "</td>";
			table_str += "<td>" + psc_pcis[i] + "</td>";
			table_str += "<td>" + cqis[i] + "</td>";
            table_str += "</tr>";
		}
		   $("#network_data").html(table_str);
		   $("#network_data").DataTable().destroy();
		   $("#network_data").DataTable({
			"paging":   true,
			"ordering": true,
			"info":     true,
			"order": [[ 1, "asc" ]]
			});
		}
    });
}
	
function downloadData(){
	disableElement(export_btn,"Exporting...");
//	console.log("Download started"+fromDate+"=="+toDate+"==="+operatingSystem+"===="+operator+"===="+selApplication);
	if(fromDate == ''||operatingSystem==''||operator==''||selApplication==''){
		alert("please fill all details");
	}else{
	$.ajax({
        url: "ajax/download_csv_data.php", 
        type: "GET",
        data:{from_timestamp:fromDate,to_timestamp:toDate,operating_system:operatingSystem,operator:operator,application:selApplication,city:city},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
        },
		success:function (data)
		{
			enableElement(export_btn, export_btn_val);
            var downloadLink = document.createElement("a");
            var fileData = ['\ufeff'+data];
            var blobObject = new Blob(fileData,{
            type: "text/csv;charset=utf-8;"
            });
            var url = URL.createObjectURL(blobObject);
            downloadLink.href = url;
            downloadLink.download = "app_analyser_raw_details.csv";
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
		}
    });
	}
}
//net used cuurently
function loadWifiParameter(){
  //$("#network_data").empty();
  inProgress(network_loading);
  $.ajax({
        url: "ajax/fetch_wifi_parameters.php",
        type: "GET",
        data:{from_timestamp:fromDate,to_timestamp:toDate,operating_system:operatingSystem,application:selApplication},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
        },
 		success:function(result) 
		{
      stopProgress(network_loading);
      wifi_states = [];
      wifi_rssis = [];
      wifi_ips = [];
      wifi_freqs = [];
      wifi_link_speeds = [];
      wifi_ssids = [];
      result = jQuery.parseJSON(result)
	 var arr = result.data;
	 for (var i = 0; i < arr.length; i++)
		{
		wifi_states.push(arr[i].wifi_state);
		wifi_rssis.push(arr[i].wifi_rssi);
		wifi_ips.push(arr[i].wifi_ip);
		wifi_link_speeds.push(arr[i].wifi_link_speed);
		wifi_freqs.push(arr[i].wifi_freq);
		wifi_ssids.push(arr[i].wifi_ssid);
		}
       var table_str = "<thead><tr>";
	   table_str += "<th>SL.NO</th>";
       table_str += "<th>STATES</th>";
       table_str += "<th>SSID</th>";
       table_str += "<th>RSSI</th>";
       table_str += "<th>IP</th>";
       table_str += "<th>FREQUENCY</th>";
	   table_str += "<th>LINK SPEED</th>";
       table_str += "</tr></thead>";
	   
       for (var i=0;i<wifi_states.length;i++){
         var pos = parseInt(1)+parseInt(i);
			table_str += "<tr>";
            table_str += "<td>" + pos + "</td>";
            table_str += "<td>" + wifi_states[i] + "</td>";
			table_str += "<td>" + wifi_ssids[i] + "</td>";
            table_str += "<td>" + wifi_rssis[i] + "</td>";
            table_str += "<td>" + wifi_ips[i] + "</td>";
			table_str += "<td>" + wifi_freqs[i] + "</td>";
			table_str += "<td>" + wifi_link_speeds[i] + "</td>";
            table_str += "</tr>";
		}
			$("#rf_wifi_network_data").html(table_str);
			$("#rf_wifi_network_data").DataTable().destroy();
			$("#rf_wifi_network_data").DataTable({
			"paging":   true,
			"ordering": true,
			"info":     true,
			"order": [[ 1, "asc" ]]
			});
		}
    });
}
function fetchWifiKpiWithFilter(fromDate,toDate,operatingSystem,selApplication,city){
  $.ajax({
        url: "ajax/fetch_wifi_data_with_filter2.php",
        type: "GET",
        data:{from_timestamp:fromDate,to_timestamp:toDate,operating_system:operatingSystem,application:selApplication,city:city},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
        }, 
 		success:function(result) 
		{
		stopProgress(kpi_loading);	
		total_ajax_complete++;
            if(total_ajax_complete == total_ajax_calls)
			{
				enableElement(submit_btn,submit_btn_val);
            }
		date_times = [];
		running_apps = [];
		sessions = [];
		session_throughputs = [];
		initial_buffr_times = [];
		no_of_buffer_per_sessions = [];
		total_buffer_times = [];
		package_load_times = [];
		percentages = [];
		total_bit_reciveds = [];
		
		result = jQuery.parseJSON(result)
		var arr = result.details;
		for (var i = 0; i < arr.length; i++)
		{
			date_times.push(arr[i].date_time);
			running_apps.push(arr[i].running_app);
			sessions.push(arr[i].session);
			session_throughputs.push(arr[i].session_throughput);
			initial_buffr_times.push(arr[i].initial_buffr_time);
			no_of_buffer_per_sessions.push(arr[i].no_of_buffer_per_session);
			total_buffer_times.push(arr[i].total_buffer_time);
			package_load_times.push(arr[i].package_load_time);
			percentages.push(arr[i].percentage);
			total_bit_reciveds.push(arr[i].total_bits_received);
		}
		var table_str = "<thead><tr>";
		table_str += "<th>SL.NO</th>";
		table_str += "<th>Date/Time</th>";
		table_str += "<th>Application</th>";
		table_str += "<th>Session</th>";
		table_str += "<th>Session Throughput(Kbps)</th>";
		table_str += "<th>Innitial buffer time (ms)</th>";
		table_str += "<th>Total no of buffer</th>";
		table_str += "<th>Total buffer time (ms)</th>";
		table_str += "<th>Video Load time (ms)</th>";
		table_str += "<th>Video Load(%)</th>";
		 table_str += "<th>Total bit recived</th>";
		table_str += "<th>Rf details</th>";
		table_str += "</tr></thead>";
		   
		for (var i=0;i<date_times.length;i++){
			var view = "<button>View</button>";
            var pos = parseInt(1)+parseInt(i);
			table_str += "<tr>";
            table_str += "<td>" + pos + "</td>";
            table_str += "<td>" + date_times[i] + "</td>";
			table_str += "<td>" + running_apps[i] + "</td>";
            table_str += "<td>" + sessions[i] + "</td>";
            table_str += "<td>" + session_throughputs[i] + "</td>";
			table_str += "<td>" + initial_buffr_times[i] + "</td>";
			table_str += "<td>" + no_of_buffer_per_sessions[i] + "</td>";
			table_str += "<td>" + total_buffer_times[i] + "</td>";
			table_str += "<td>" + package_load_times[i] + "</td>";
			table_str += "<td>" + percentages[i] + "</td>";
			table_str += "<td>" + total_bit_reciveds[i] + "</td>";
			table_str += "<td>"+view+ "</td>";
            table_str += "</tr>";
		}
		
      $('#table_data').on( 'click', 'button', function (e) {
			e.preventDefault();
				var $data= [];
				var x = $(this).closest("tr");;
				var cells = x.find('td');
				$.each(cells, function(){
					var $sessionId = ($(this).text());
					$data.push($sessionId);
				});
				if ($data[1] == "N/A") {
				}
				sessionIdValue = $data[3];
				console.log("data==="+$data[3]);
				fetchWifiRfDataSessionId(); 
		});
		
	    $("#table_data").html(table_str);
		$("#table_data").DataTable().destroy();
        $("#table_data").DataTable({
        "paging":   true,
        "ordering": true,
        "info":     true,
        "order": [[ 1, "asc" ]]
        });
		}
    });
}

function dataFetch(){
  //$("#table_data").empty();
  inProgress(kpi_loading);
  $.ajax({
        url: "ajax/fetch_data_with_filter2.php",
        type: "GET",
        data:{from_timestamp:fromDate,to_timestamp:toDate,operating_system:operatingSystem,operator:operator,application:selApplication,city:city},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
		},
 		success:function(result) 
		{
		total_ajax_complete++;
        if(total_ajax_complete == total_ajax_calls)
		{
			enableElement(submit_btn,submit_btn_val);
        }
		stopProgress(kpi_loading);	
		dateTimes = [];
		applications = [];
		sessions = [];
		sessionThroughputs = [];
		innitialBufferTimes = [];
		totalNoBuffers = [];
		totalBufferTimes = [];
		videoLoadTimes = [];
		videoLoadPercents = [];
		total_bit_reciveds = [];
		result = jQuery.parseJSON(result)
		var arr = result.details;
		var view = "<button>View</button>";
		for (var i = 0; i <arr.length; i++)
		{
			dateTimes.push(arr[i].date_time);
			applications.push(arr[i].running_app);
			sessions.push(arr[i].session);
			sessionThroughputs.push(arr[i].session_throughput);
			innitialBufferTimes.push(arr[i].initial_buffr_time);
			totalNoBuffers.push(arr[i].no_of_buffer_per_session);
			totalBufferTimes.push(arr[i].total_buffer_time);
			videoLoadTimes.push(arr[i].package_load_time);
			videoLoadPercents.push(arr[i].percentage);
			total_bit_reciveds.push(arr[i].total_bits_received);
		}
		   var table_str = "<thead><tr>";
		   table_str += "<th>SL.NO</th>";
		   table_str += "<th>Date/Time</th>";
		   table_str += "<th>Application</th>";
		   table_str += "<th>Session</th>";
		   table_str += "<th>Session Throughput(Kbps)</th>";
		   table_str += "<th>Innitial buffer time (ms)</th>";
		   table_str += "<th>Total no of buffer</th>";
		   table_str += "<th>Total buffer time (ms)</th>";
		   table_str += "<th>Video Load time (ms)</th>";
		   table_str += "<th>Video Load(%)</th>";
		   table_str += "<th>Total bit recived</th>";
		   table_str += "<th>RF details</th>";
		   table_str += "</tr></thead>";
		   
		for (var i=0;i<dateTimes.length;i++)
		{
            var pos = parseInt(1)+parseInt(i); 
			table_str += "<tr>";
            table_str += "<td>" + pos + "</td>";
            table_str += "<td>" + dateTimes[i] + "</td>";
			table_str += "<td>" + applications[i] + "</td>";
            table_str += "<td>" + sessions[i] + "</td>";
            table_str += "<td>" + sessionThroughputs[i] + "</td>";
			table_str += "<td>" + innitialBufferTimes[i] + "</td>";
			table_str += "<td>" + totalNoBuffers[i] + "</td>";
			table_str += "<td>" + totalBufferTimes[i] + "</td>";
			table_str += "<td>" + videoLoadTimes[i] + "</td>";
			table_str += "<td>" + videoLoadPercents[i] + "</td>";
			table_str += "<td>" + total_bit_reciveds[i] + "</td>";
			table_str += "<td>"+view+"</td>";
            table_str += "</tr>";
		}
			$('#table_data').on( 'click', 'button', function (e) {
			e.preventDefault();
				var $data= [];
				var x = $(this).closest("tr");;
				var cells = x.find('td');
				$.each(cells, function(){
					var $sessionId = ($(this).text());
					$data.push($sessionId);
				});
				if ($data[1] == "N/A") {
				}
				sessionIdValue = $data[3];
				console.log("data==="+$data[3]);
				fetchDataWithSessionId(); 
			});		
			$("#table_data").html(table_str);
			$("#table_data").DataTable().destroy();
            $("#table_data").DataTable({
            "paging":   true,
            "ordering": true,
            "info":     true,
            "order": [[ 1, "asc" ]]
            });
		}
    });
}
function fetchWifiRfDataSessionId(){
	openPopUp();
	$.ajax({
        url: "ajax/fetch_rf_respect_to_session_id_wifi.php",
        type: "GET",
        data:{session:sessionIdValue},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
        },
 		success:function(result) 
		{
      wifi_states = [];
      wifi_rssis = [];
      wifi_ips = [];
      wifi_freqs = [];
      wifi_link_speeds = [];
      wifi_ssids = [];
      result = jQuery.parseJSON(result)
	 var arr = result.details;
	 for (var i = 0; i < arr.length; i++)
		{
		wifi_states.push(arr[i].wifi_state);
		wifi_rssis.push(arr[i].wifi_rssi);
		wifi_ips.push(arr[i].wifi_ip);
		wifi_link_speeds.push(arr[i].wifi_link_speed);
		wifi_freqs.push(arr[i].wifi_freq);
		wifi_ssids.push(arr[i].wifi_ssid);
		}
       var table_str = "<thead><tr>";
	   table_str += "<th>SL.NO</th>";
       table_str += "<th>STATES</th>";
       table_str += "<th>SSID</th>";
       table_str += "<th>RSSI</th>";
       table_str += "<th>IP</th>";
       table_str += "<th>FREQUENCY</th>";
	   table_str += "<th>LINK SPEED</th>";
       table_str += "</tr></thead>";
	   
       for (var i=0;i<wifi_states.length;i++){
         var pos = parseInt(1)+parseInt(i);
			table_str += "<tr>";
            table_str += "<td>" + pos + "</td>";
            table_str += "<td>" + wifi_states[i] + "</td>";
			table_str += "<td>" + wifi_ssids[i] + "</td>";
            table_str += "<td>" + wifi_rssis[i] + "</td>";
            table_str += "<td>" + wifi_ips[i] + "</td>";
			table_str += "<td>" + wifi_freqs[i] + "</td>";
			table_str += "<td>" + wifi_link_speeds[i] + "</td>";
            table_str += "</tr>";
		}
			$("#rf_wifi_network_data").html(table_str);
			$("#rf_wifi_network_data").DataTable().destroy();
			$("#rf_wifi_network_data").DataTable({
			"paging":   true,
			"ordering": true,
			"info":     true,
			"order": [[ 1, "asc" ]]
			});
		}
    });
}
function fetchDataWithSessionId(){
	//inProgress(network_loading);
	openPopUp();
	console.log("fetch data with sessionId");
	$.ajax({
        url: "ajax/fetch_rf_respect_to_session_id_spn.php",
        type: "GET",
        data:{session:sessionIdValue},
		error: function(jqXHR, textStatus, errorThrown)
		{
			console.log(textStatus, errorThrown);
        },
		success:function(result) 
		{ 
		//stopProgress(network_loading);
		   sessions = [];
		   date_times = [];
		   spns = [];
		   techs = [];
		   sub_techs = [];
		   rsrps = [];
		   rsrqs = [];
		   sinrs = [];
		   cell_ids = [];
		   psc_pcis = [];
		   cqis = [];
		   total_bit_reciveds=[];
		   result = jQuery.parseJSON(result)
		   var arr = result.details;
		   for (var i = 0; i < arr.length; i++)
		{
			sessions.push(arr[i].session);
			date_times.push(arr[i].date_time);
			spns.push(arr[i].spn);
			techs.push(arr[i].tech);
			sub_techs.push(arr[i].sub_tech);
			rsrps.push(arr[i].rsrp);
			rsrqs.push(arr[i].rsrq);
			sinrs.push(arr[i].sinr);
			cell_ids.push(arr[i].cell_id);
			psc_pcis.push(arr[i].psc_pci);
			cqis.push(arr[i].cqi);
			total_bit_reciveds.push(arr[i].total_bits_received);
		}
		   
		   var table_str = "<thead><tr>";
		   table_str += "<th>Sl.No</th>";
		   table_str += "<th>SESSION</th>";
		   table_str += "<th>Date/Time</th>";
		   table_str += "<th>SPN</th>";
		   table_str += "<th>Tech</th>";
		   table_str += "<th>Sub Tech</th>";
		   table_str += "<th>RSRP</th>";
		   table_str += "<th>RSRQ</th>";
		   table_str += "<th>SINR</th>";
		   table_str += "<th>PCI</th>";
		   table_str += "<th>CELL ID</th>";
		   table_str += "<th>CQI</th>";
		   table_str += "<th>Total bit Recived</th>";
		   table_str += "</tr></thead>";
		   //table_str += "<tbody>";
		   //table_str += "<td>" + result + "</td>";
		   //table_str += "</tbody>";
		 
		 for (var i=0;i<sessions.length;i++){
         var pos = parseInt(1)+parseInt(i);
			table_str += "<tr>";
            table_str += "<td>" + pos + "</td>";
            table_str += "<td>" + sessions[i] + "</td>";
			table_str += "<td>" + date_times[i] + "</td>";
            table_str += "<td>" + spns[i] + "</td>";
            table_str += "<td>" + techs[i] + "</td>";
			table_str += "<td>" + sub_techs[i] + "</td>";
			table_str += "<td>" + rsrps[i] + "</td>";
			table_str += "<td>" + rsrqs[i] + "</td>";
			table_str += "<td>" + sinrs[i] + "</td>";
			table_str += "<td>" + cell_ids[i] + "</td>";
			table_str += "<td>" + psc_pcis[i] + "</td>";
			table_str += "<td>" + cqis[i] + "</td>";
			table_str += "<td>" + total_bit_reciveds[i] + "</td>";
            table_str += "</tr>";
		}
		   
		   $("#rf_data_table").html(table_str);
		   $("#rf_data_table").DataTable().destroy();
		   $("#rf_data_table").DataTable({
			"paging":   true,
			"ordering": true,
			"info":     true,
			"order": [[ 1, "asc" ]]
			});
		}
    });
	
}
 function fetchFilterDataWithCity(city){
	  $('#os_drop_down').empty();
	  $('#application_selection').empty(); 
	  $('#operator').empty();
	  $.ajax({
		  url: "ajax/city_filtered_params_shi.php",
		  type: "GET", 
		  data:{city:city},
		  error: function(jqXHR, textStatus, errorThrown){
		  console.log(textStatus, errorThrown);
		  },
		   success:function(result) 
		  {
			  result = jQuery.parseJSON(result)
			  var operatingSystem='';
			  var application_select='';
			  var operator_selection='';
			  var spn_arr = result.spn;
			  var operating_arr = result.os;
			  var app_arr = result.app;
			  var message = result.success;
			  if(message==0){ 
				  alert("Opps! No Drive on this city");
			  }else{
			  //operatingSystem += '<option value="">Select Operating system</option>';  
			  for (var i=0;i<operating_arr.length;i++){	
			  operatingSystem += '<option value="'+ operating_arr[i] + '">' + operating_arr[i] + '</option>';
			  }
			  $('#os_drop_down').append(operatingSystem);
			 // application_select += '<option value="">Select Application</option>';
			  application_select += '<option value="All Application">All application</option>';
			  for (var i=0;i<app_arr.length;i++){	
			  application_select += '<option value="'+ app_arr[i] + '">' + app_arr[i] + '</option>';
			  }
			  $('#application_selection').append(application_select);
			 // operator_selection += '<option value="">Select Operator</option>';
			  for (var i=0;i<spn_arr.length;i++){	
			  operator_selection += '<option value="'+ spn_arr[i] + '">' + spn_arr[i] + '</option>';
			  }
			  $('#operator').append(operator_selection);
			  }
			  ///////////////////---additional--/////////////////////////
			  	sel_application = fetchData('selApplication');
				$("#application_selection").val(sel_application);
				operatingSystem = fetchData('operatingSystem');
				$("#os_drop_down").val(operatingSystem);
				operator = fetchData('operator');
				$("#operator").val(operator);
				console.log("in filter fetch respect to city"+sel_application+operatingSystem+operator);
			 ///////////////////////////////////////////////////////		 
		  }
	  });
  }

function openPopUp(){
console.log("open pop up called");
//$("#myModal").modal('show');	
var modal = document.getElementById("myModal");
// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 

  modal.style.display = "block";

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "block";
  }
}
}
	
function disableElement(elem,msg) {
    $(elem).html(msg);
    $(elem).attr("disabled", "disabled");
}

function enableElement(elem, display_value) {
    $(elem).html(display_value);
    $(elem).removeAttr("disabled");
}			
function inProgress(elem) {
  $(elem).show();
}

function stopProgress(elem) {
  $(elem).hide();
}
</script>
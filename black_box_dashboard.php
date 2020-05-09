 <style>
  .dot {
		height: 25px;
		width: 25px;
		background-color:#BDBDBD;
		border-radius: 50%;
		border: 1px solid #fff;
		display: inline-block; 
	  }
	  .green_led{
		height: 25px;
		width: 25px;
		background-color: rgb(66, 245, 81);
		border-radius: 50%;
		display: inline-block;
    }
    
.red_led{
		height: 25px;
		width: 25px;
		background-color: rgb(255, 0, 0);
		border-radius: 50%;
		display: inline-block;
		}
	.blue_led{
			height: 25px;
			width: 25px;
			background-color: rgb(0, 247, 255);
			border-radius: 50%;
			display: inline-block;
		}
		
		.yellow_led{
			height: 25px;
			width: 25px;
			background-color: rgb(255, 217, 0);
			border-radius: 50%;
			display: inline-block;
    }
    
    .remote_btn{
		background-color: #4CAF50; /* Green */
		border: none;
		color: white;
		padding: 12px 24px;
		text-align: center;
		text-decoration: none;
		display: inline-block;
		font-size: 18px;
		margin: 10px 5px;
		transition-duration: 0.4s;
		cursor: pointer;
  }
  .power_btn {
	background-color: white; 
  color: black; 
  width: 100%;
  padding-left:20px;
  margin-top: 10px;
	border: 2px solid #FF6E40;
	border-radius: 12px;
  }
  
  .power_btn:hover {
	background-color: #FF6E40;
  color: white;
  box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.19);
  }
  .start_btn {
	background-color: white; 
  color: black; 
  margin-top: 20px;
  padding-left: 10px;
  width: 100%;
	border: 2px solid #4CAF50;
	border-radius: 12px;
  }
  
  .start_btn:hover {
	background-color: #4CAF50;
  color: white;
  box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.19);
  }

  .Stop_btn {
	background-color: white; 
  color: black; 
  width: 100%;
  margin-top: 10px;
	border: 2px solid #EF5350;
	border-radius: 12px;
  }
  
  .Stop_btn:hover {
	background-color: #EF5350;
  color: white;
  box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.19);
  }

  
  .reboot_btn {
	background-color: white; 
  color: black; 
  width: 100%;
  margin-top: 10px;
	border: 2px solid #FDD835;
	border-radius: 12px;
  }
  
  .reboot_btn:hover {
	background-color: #FDD835;
  color: white;
  box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.19);
  }
   
  .upload_btn {
	background-color: white; 
  color: black; 
  width: 100%;
  margin-top: 10px;
	border: 2px solid #F9A825;
	border-radius: 12px;
  }
  
  .upload_btn:hover {
	background-color: #F9A825;
  color: white;
  box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.19);
  }
</style>
<?php include('header_new.php'); ?>
<section id="page-content">
    <div class="body-content animated fadeIn">
		<div class= "row">
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
              <div class="panel rounded shadow">
			   <div class="panel-heading">
                    <div class="pull-left">
                         <h3 class="panel-title" style="color: #fff">Filter</h3>
                     </div>
                    <div class="clearfix"></div>
                  </div>
				<div class="panel-body">
                        <form class="form-inline" >
                            <div class="form-body">
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    <select class="form-control select_width" id="select_box_drop">
                                        <option value="">Select Device Id</option>
                                    </select>
                                </div>
                               <!-- <div class="col-lg-4 col-md-4 col-sm-12">
                                     <input type="text" class="form-control select_width" id="android_id" value="" autocomplete="off" placeholder="android id">
                                </div>-->
                                <div>
                                    <button type="button" name="btn_chart" id="btn_track" class="btn btn-info">Track</button>
                                </div>
                            </div>
                        </form>                         
					</div>
				</div>
            </div>
		</div>
          <!--Content Row-->
          <div class="row">
           <!--Active kit-->
            <div class="col-xl-4 col-md-4 mb-4">
			  <div class="mini-stat clearfix bg-facebook rounded">
                    <div class="mini-stat-info">
						Active device
                        <span id ="active_kit">0</span>
                    </div>
                </div>
            </div>
            <!-- Inactive kit -->
            <div class="col-xl-4 col-md-4 mb-4">
			   <div class="mini-stat clearfix bg-facebook rounded">
                    <div class="mini-stat-info">
						Inactive kit
                        <span id ="inactive_kit">0</span>
                    </div>
                </div>
            </div>
            <!-- Innitial buffer time Card Example -->
            <div class="col-xl-4 col-md-4 mb-4">
			  <div class="mini-stat clearfix bg-facebook rounded">
                    <div class="mini-stat-info">
						Total Device
                        <span id ="uploaded_log">0</span>
                    </div>
                </div>
            </div>
          </div>
          <!-- Content Row -->
          <div class="row">
            <!-- Content Column -->
            <div class="col-lg-10 col-md-10 col-sm-12">
			   <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">Map</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div id="plotingProgress" style="display: none; width:200px; height:20px;background-color: black; position: absolute; top:-5px; left: 40%; z-index: 100;color: #fff; padding: 0 10px;">Plotting Data.Please Wait...</div>
                        <div id="map" style="width:100%; height:500px;"></div>
                    </div>
                </div>
			</div>
            <div class="col-lg-2 col-md-2 col-sm-12">
              <div class="panel">
                 <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">Control device</h3>
                        </div>
                        <div class="clearfix"></div>
                 </div>
                <div class="panel-body"> 
                <div style="width:100%; height:500px;">
                <div class ="row"> 
                <div class="col-lg-12 col-md-12 col-sm-12"  style="text-align:center">
                <span id="red_dot" data-placement="top" data-toggle="tooltip" title="Power" class= "dot tooltiptext"></span>
                  <span id="blue_dot" data-placement="top"  data-toggle="tooltip" title="Ready" class="dot tooltiptext"></span>
                  <span id="green_dot" data-placement="top"  data-toggle="tooltip" title="Internet Status" class="dot tooltiptext"></span> 
                  <span id="yellow_dot" data-placement="top"  data-toggle="tooltip" title="Upload"  class="dot tooltiptext"></span>
                </div>
					  <div class="col-lg-12 col-md-12 col-sm-12">
                      <button type="button" class="remote_btn start_btn" id="start_remote_button"></i>START</button>
                      </div>
                      <div class="col-lg-12 col-md-12 col-sm-12">
                      <button type="button" class="remote_btn Stop_btn" id="stop_remote_button">STOP</button>           
                      </div>
                      <div class="col-lg-12 col-md-12 col-sm-12">
                      <button type="button" class="remote_btn upload_btn " id="upload_remote_button">UPLOAD</button>           
                      </div>
                      <div class="col-lg-12 col-md-12 col-sm-12">
                      <button type="button" class="remote_btn power_btn" id="power_remote_button">POWER</button>       
                      </div>
                      <div class="col-lg-12 col-md-12 col-sm-12">
                      <button type="button" class="remote_btn reboot_btn" id="reboot_remote_button">REBOOT</button>    
                      </div>
                  </div>         
                </div>
                </div>
              </div>
           </div> 
		   <!-- End of row -->
		</div>
		<!--starting of table-->
		 <div class="row">
		   <div class="col-lg-12 col-md-12 col-sm-12">
              <div class="panel">
                 <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">KPI</h3>
                        </div>
                        <div class="clearfix"></div>
                 </div>
                <div  class="panel-body table-responsive-sm">
			      	<table id="kpi_table" class="table table-striped table-bordered nowrap">  <!--class="table table-condensed table-bordered" >-->
				  	  </table>				
                </div>
              </div>
           </div>
		  </div> 
        </div>
	</div>
      <!-- Footer -->
	  
 <?php include('footer_new.php')?>
<script>

    var mymap;
	var latitude=[],longitude=[];
	mymap = L.map('map').setView([20.5937, 78.9629],5);
    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
    maxZoom: 20,
	attribution: '',
    id: 'mapbox/streets-v11'
    }).addTo(mymap);

$(function(){
   $( "#start_dt" ).datepicker();
});
$(function(){
    $("#end_dt").datepicker();
});
loadDatePicker($('#trend_drop_down').val());  
$('#trend_drop_down').change(function() {
        var type = $(this).val();
        if(type){
          loadDatePicker(type);
		}
 });
function loadDatePicker(type) {
    if(type =='') {
    $('#start_dt').show();
		$('#to_text').hide();
    $('#end_dt').hide();
		$('#start_dt:text').val("");
		$('#to_text:text').val("");
		$('#end_dt:text').val("");
    }
    else if(type == 'custom') {
        $('#end_dt').show();
		$('#to_text').show();
        $('#start_dt').show();
    }
	else if(type == 'day') {
    $('#start_dt').show();
		$('#to_text').hide();
    $('#end_dt').hide();
		$('#end_dt:text').val("");
    }
}
var box_id,android_id,fromDate,toDate,hash_id;
// set up led
var internet_status=0,red_led=0,green_led=0,yellow_led=0,blue_led=0;
var start_remote_button = 0,stop_remote_button = 0,upload_remote_button = 0,power_remote_button= 0,rebot_remote_button = 0;
var remote_control_time_out = 10000,live_map_refresh_time_out= 10000,device_activity_refresh_time=10000;
var remote_control_box_loading;

$( document ).ready(function(){
  $('[data-toggle="tooltip"]').tooltip(); 
  remote_control_box_loading = $('#remote_control_box_loading');
	fetchActiveKitDetails();
  loadTopCardData();
  //loadLiveMap();
 
  // remote configuration
  remoteControlBox();
  sendButtonClcikAction();
	$("#select_box_drop").click(function(){
  $('#select_box_drop').empty();
	fetchActiveKitDetails();
	});
	$('#btn_track').click(function(){
		loadTopCardData();
		loadMap();
		//loadLiveMap()
  });
});	

function fetchActiveKitDetails(){
	$.ajax({
        url: "apis/fetch_current_active_black_box.php",
        type: "GET", 
		data:{},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
        },
 		success:function(result) 
		{
   
			var hash_ids = [];	
			var box_option = '';
      var android_option = '';
			result = jQuery.parseJSON(result)
			var arr = result.data;
			var message = result.success;
			if(message==0){
				//alert("Opps! No active devices");
			}else{
			for (var i = 0; i < arr.length; i++) {
				hash_ids.push(arr[i].hash_id);
			} 
			for (var i=0;i<hash_ids.length;i++){
			   box_option += '<option value="'+ hash_ids[i] + '">' + hash_ids[i] + '</option>';
			}
      $('#select_box_drop').append(box_option);
      // var hashIdIndex = $("#select_box_drop option:selected").index() ;//$("#select_box_drop").prop('selectedIndex');
      // console.log("index:"+ hashIdIndex); 
      // console.log("hash_ids:"+ hash_ids);
      // console.log("android_ids:"+ android_ids); 
      // $('#android_id').val(android_ids[hashIdIndex]);
			}
		}
    });
}
function loadMap(){
	var hash_id = $('#select_box_drop').val();
	if (hash_id =="null"){
		alert("Please select a device");
	}else{
	console.log("load map"+hash_id);
	$.ajax({
        url: "apis/fetch_lat_lon_based_on_device_id.php",
        type: "GET",
        data:{hash_id:hash_id},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
        },
 		success:function(result) 
		{
      var progress_map = $('#plotingProgress').show();
			result = jQuery.parseJSON(result)
			var arr = result.data;
			var tbl_str = "<thead><tr>";
				tbl_str += "<th>SINR</th>";
                tbl_str += "<th>RSRP</th>";
                tbl_str += "<th>RSRQ</th>";
                tbl_str += "</tr></thead>";
				
				tbl_str += "<tr>";
                tbl_str += "<td>" + arr[0].avg_sinr + "</td>";
                tbl_str += "<td>" + arr[0].avg_rsrp + "</td>";
                tbl_str += "<td>" + arr[0].avg_rsrq + "</td>";
                tbl_str += "</tr>";					
				$('#kpi_table').html(tbl_str);
				
			latitude = arr[0].lat;
      longitude = arr[0].lan;
      if(latitude=='-'||longitude=='-'){
        progress_map = $('#plotingProgress').html("Can't start yet");
        progress_map = $('#plotingProgress').show();
      }
      else{
			console.log(latitude+""+longitude);
			var marker = L.marker([latitude, longitude]).addTo(mymap);
      mymap.flyTo([latitude, longitude], 12);
      progress_map = $('#plotingProgress').hide();
      }
      
      setTimeout(function(){
        loadMap();}, live_map_refresh_time_out);
		}
		
    });
	}
}
function loadLiveMap(){
//	console.log("load live map called");
	$.ajax({
        url: "ajax/fetch_lat_lon_rsrp_rsrq_sinr_for_plotting_on_map.php",
        type: "GET",
        data:{},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
        },
 		success:function(result) 
		{
			var marker,circle;
			var lat, lon;
			var greenIcon=L.icon({
				iconUrl:'https://upload.wikimedia.org/wikipedia/commons/2/2d/Basic_green_dot.png',
				iconSize:[5,5],
				iconAnchor:[5,5],
			});
			result = jQuery.parseJSON(result)
			
			if(result.success==1||result.success==0){
				//$('#plotingProgress').show();
				var arr = result.data;
				var tbl_str = "<thead><tr>";
                        tbl_str += "<th>SINR</th>";
                        tbl_str += "<th>RSRP</th>";
                        tbl_str += "<th>RSRQ</th>";
                        tbl_str += "</tr></thead>";
				
						tbl_str += "<tr>";
                        tbl_str += "<td>" + result.avq_sinr + "</td>";
                        tbl_str += "<td>" + result.avq_rsrp + "</td>";
                        tbl_str += "<td>" + result.avq_rsrq + "</td>";
                        tbl_str += "</tr>";
						
				$('#kpi_table').html(tbl_str);
				var markersGroup = new L.layerGroup();
				$.each( arr, function( i,item ){
					//console.log("inloop",item);
					lat = item.lat;
					lon = item.lon;  
					marker = L.marker([lat, lon],{icon:greenIcon}).addTo(markersGroup);
					//marker.bindPopup("This is the Transamerica Pyramid").openPopup();
				});


				mymap.addLayer(markersGroup);
				mymap.flyTo([lat, lon], 12);
				//$('#plotingProgress').hide();
        setTimeout(function(){
          loadLiveMap();}, 5000);
		    }
			}	
    });
}
function loadTopCardData(){
//	console.log("card data");
		fromDate = $("#start_dt").val();
		toDate = $("#end_dt").val();
		$.ajax({
        url: "apis/fetch_current_active_inactive_no_of_log_uploaded.php",
        type: "GET",
        data:{from_timestamp:fromDate,to_timestamp:toDate},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
        },
 		success:function(result) 
		{
      result = jQuery.parseJSON(result)
      console.log("top card"+result);
			var uploaded_log = result.number_of_log_uploaded;
			var active_device = result.active_device;
			var inactive_device = result.inactive_device;
			var total_device = parseInt(active_device) + parseInt(inactive_device);//($('#active_kit').val())+($('#inactive_kit').val());
			$("#active_kit").text(active_device);
			$("#inactive_kit").text(inactive_device);
			$("#uploaded_log").text(total_device);

      setTimeout(function(){
        loadTopCardData();}, device_activity_refresh_time);
		}
		
    });
}
function remoteControlBox(){
  // hash_id = $('#select_box_drop').val();
  // android_id = $('#android_id').val();
  inProgress(remote_control_box_loading);
  hash_id = $('#select_box_drop').val();
  console.log("remote controle box"+hash_id);
	if (hash_id =="null"){
		alert("Please select a device");
	}else{
	$.ajax({
        url: "ajax/fetch_remote_status.php",
        type: "GET",
        data:{hash_id:hash_id},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
        },
 		success:function(result) 
		{
     // stopProgress(remote_control_box_loading);
      result = jQuery.parseJSON(result)
      var arr = result.data;
			var message = result.success;
			if(message==0){

			}else{
         internet_status = arr[0].internet_status;
         red_led = arr[0].red_led;
         green_led = arr [0].green_led;
         yellow_led = arr[0].yellow_led;
         blue_led = arr[0].blue_led;   
       //  console.log("red led"+red_led+"yellow"+yellow_led);      

      }
      if(red_led == 1){
        $("#red_dot").addClass("spinner-grow red_led");//$("#red_led").addClass("spinner-grow red_led");
        console.log("red led true");
      }else{
        $("#red_dot").addClass("dot");
        console.log("red led false");
      }
      if(green_led == 1){
        $("#green_dot").addClass("spinner-grow green_led");
      }else{
        $("#green_dot").addClass("dot");
      }
      if(blue_led == 1){
        $("#blue_dot").addClass("spinner-grow blue_led");
      }else{
        //console.log("blue led false");
        $("#blue_dot").addClass("dot");
      }
      if(yellow_led == 1){
        $("#yellow_dot").addClass("spinner-grow yellow_led");
        console.log("yellow led checked");
      }else{
        $("#yellow_dot").addClass("dot");
        console.log("yellow led false");
      }
      setTimeout(function(){
        remoteControlBox();}, remote_control_time_out);
    }
     });
  }
}
function sendButtonClcikAction(){
    resetAllValueBtn();
  $('#start_remote_button').click(function(){
    resetAllValueBtn();
    start_remote_button=1;
    remoteActionOnButtonClick();
  });
  $('#stop_remote_button').click(function(){
    resetAllValueBtn();
    stop_remote_button=1;
    console.log("start btn click");
    remoteActionOnButtonClick();
  });
  $('#upload_remote_button').click(function(){
    resetAllValueBtn();
    upload_remote_button=1;
    console.log("start btn click");
    remoteActionOnButtonClick();
  });
  $('#power_remote_button').click(function(){
    resetAllValueBtn();
    power_remote_button=1;
    console.log("start btn click");
    remoteActionOnButtonClick();
  });
  $('#reboot_remote_button').click(function(){
    resetAllValueBtn();
    rebot_remote_button=1;
    console.log("start btn click");
    remoteActionOnButtonClick();
  });
  
}
function resetAllValueBtn(){
  start_remote_button = 0,
  stop_remote_button = 0,
  upload_remote_button = 0,
  power_remote_button= 0,
  rebot_remote_button = 0;
}
function remoteActionOnButtonClick(){
  hash_id = $('#select_box_drop').val();
  android_id = $('#android_id').val();
  $.ajax({
        url: "ajax/update_remote_status.php",
        type: "GET",
        data:{hash_id:hash_id,start_btn:start_remote_button,stop_btn:stop_remote_button,upload_btn:upload_remote_button,power_btn:power_remote_button,reboot_btn:rebot_remote_button},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
        },
 		success:function(result) 
		{
      result = jQuery.parseJSON(result)
			var message = result.success;
			if(message==0){
				alert("Opps! Something error occured");
			}else{
      remoteControlBox();
    }
  }
    });
}

function inProgress(elem) {
  $(elem).show();
}

function stopProgress(elem) {
  $(elem).hide();
}
</script>
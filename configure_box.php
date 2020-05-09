<?php include('header_new.php'); ?>
<section id="page-content">
    <div class="body-content animated fadeIn">
		<div class= "row">
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
              <div class="panel rounded shadow">
                <!-- Card Header - Dropdown -->
                <div class="panel-heading">
                    <div class="pull-left">
                         <h3 class="panel-title" style="color: #fff">Configure box</h3>
                     </div>
                    <div class="clearfix"></div>
                  </div>
				<div class="panel-body">
                       <form class="form-inline">
                            <div class="form-body">
                                <div class="col-lg-2 col-md-2 col-sm-12">
                                    <select class="form-control select_width" id="select_box_drop">
                                        <option value="">Select Device</option>
                                    </select>  
                                </div>
								<div class="col-lg-2 col-md-2 col-sm-12">
                                    <select class="form-control select_width" id="select_mobile">
                                        <option value="">Select Mobile</option>
                                    </select>
                                </div>
								<div class="col-lg-2 col-md-2 col-sm-12">
                                    <input class="form-control select_width" id="mo_id" value=""  autocomplete="off" placeholder="MO...">
                                </div>
								<div class="col-lg-2 col-md-2 col-sm-12">
                                    <input class="form-control select_width" id="mt_id" value=""  autocomplete="off" placeholder="MT...">
                                </div>
								<div class="col-lg-2 col-md-2 col-sm-12">
                                    <input class="form-control select_width" id="calling_time" value=""  autocomplete="off" placeholder="Calling time">
                                </div>
								<div class="col-lg-2 col-md-2 col-sm-12">
                                    <input class="form-control select_width" id="call_wait_time" value=""  autocomplete="off" placeholder="Call wait time">
                                </div>
								 <div class="clearfix"></div>
								<div class="form-body">
								<div class="col-lg-12 col-md-12 col-sm-12 text-center">
                                    <button type="button" id="btn_save" class="btn btn-info">Save</button>
                                </div>
								</div>
                            </div>
						</form>	
				</div>
              </div>
            </div>
		</div>
          <!-- Content Row -->
          <div class="row">
            <!-- Content Column -->
		   <div class="col-lg-12 col-md-12 col-sm-12">
              <!-- Project Card Example -->
              <div class="panel rounded shadow">
                <div class="panel-heading">
                    <div class="pull-left">
                         <h3 class="panel-title" style="color: #fff">Configured box</h3>
                     </div>
                    <div class="clearfix"></div>
                  </div>
                <div  class="panel-body table-responsive-sm">
				<!--here is table content -->
				<table id="table_data_configured" class="table table-condensed table-striped table-bordered nowrap" >
				  <!--<thead>
					<tr>
					<th class ="th-sm">Black box id</th>
					<th class ="th-sm">Mobile id</th>
					<th class ="th-sm">Call Originator</th>
					<th class ="th-sm">Call Terminator</th>
					</tr>
				  </thead>
				  <tbody >

				  </tbody>-->
				</table>				
                </div>
              </div>
           </div>
		</div><!--end of the row-->
        </div><!--end of container fluid-->
      </div>
<?php include('footer_new.php')?> 
<Script>
var box_id,mo_id,mt_id,android_id,call_time,call_wait_time;
$( document ).ready(function(){
	fetchKitDetails();
	updateTableData();
	updateBoxInformation();
	$("#select_box_drop").click(function(){
		console.log("box drop down changed")
		loadAllData();
	});
	$('#btn_save').click(function(){
		updateBoxInformation();
		
	});
});
function fetchKitDetails(){
	//console.log("in fech function")
	$('#select_box_drop').empty();
	$.ajax({
        url: "apis/fetch_hash_ids.php",
        type: "POST", 
		data:{},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
        },
 		success:function(result) 
		{
			var box_option = '';
			var android_id_options ='';
			
			result = jQuery.parseJSON(result)
			
			var optionArr = result.data_hash_id;
			var androidIdArr = result.data_android_id;
			
			var message = result.success;
			console.log(optionArr);
			
		for (var i=0;i<optionArr.length;i++)
		{
		   box_option += '<option value="'+ optionArr[i] + '">' + optionArr[i] + '</option>';
		}
		$('#select_box_drop').append(box_option);
		
		for (var i=0;i<androidIdArr.length;i++)
		{
		   android_id_options += '<option value="'+ androidIdArr[i] + '">' + androidIdArr[i] + '</option>';
		}
		$('#select_mobile').append(android_id_options);
		}
    });
}

/*function fetchMoMtWithBoxId(){
	
	box_id = $("#select_box_drop").val();
	console.log(box_id);
	$.ajax({
        url: "apis/fetch_mo_mt_hash_id.php",
        type: "GET", 
		data:{hash_id:box_id},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
        },
 		success:function(result) 
		{
			result = jQuery.parseJSON(result)
			console.log(result);
			var arr = result.data;
			console.log(arr);
			var mo_ids = arr[0].mo_id;
			var mt_ids = arr[0].mt_id;
			var android_ids = arr[0].android_id;
			var success = result.success;
			if(success == 0||success==1){
			$("#mo_id").val(mo_ids);
			$("#mt_id").val(mt_ids);
			$("#select_mobile").val(android_ids);
			}
			//console.log(success+"==="+mo_ids+"==="+mt_ids+"==="+android_ids);
		}
    });
}*/
function updateBoxInformation(){
	mo_id = $("#mo_id").val();
	mt_id = $("#mt_id").val();
	call_time =$("#calling_time").val();
	android_id = $("#select_mobile").val();
	call_wait_time = $("#call_wait_time").val(); 
	box_id = $('#select_box_drop').val();

	$.ajax({
        url: "apis/update_mo_mt_respect_to_hash_id.php",
        type: "GET", 
		data:{hash_id:box_id,mo_id:mo_id,mt_id:mt_id,android_id:android_id,call_time:call_time,wait_time:call_wait_time},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
        },
 		success:function(result) 
		{
			result = jQuery.parseJSON(result)
			console.log(result);
			var success = result.success;
			if(success == 0||success==1)
			{
			updateTableData();
			}
		}
    });
}
function updateTableData(){
	//$('#select_box_drop').empty();
	$('#table_data_configured').empty();
	$.ajax({
        url: "apis/fetch_data_to_show_on_table.php",
        type: "GET", 
		data:{},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
        },
 		success:function(result) 
		{
			var hash_ids =[];
			var mo_ids = [];
			var mt_ids = [];
			var wait_times = [];
			var call_times = [];
			var lastActives = [];
			var ip_addresss = [];
			var android_ids = [];
			result = jQuery.parseJSON(result)
			var arr = result.data;
			var message = result.success;
			for (var i = 0; i < arr.length; i++)
		{
			hash_ids.push(arr[i].hash_id);
			mo_ids.push(arr[i].mo_id);
			mt_ids.push(arr[i].mt_id);
			android_ids.push(arr[i].android_id);
			wait_times.push(arr[i].wait_time);
			call_times.push(arr[i].call_time);
			ip_addresss.push(arr[i].ip_address);
			lastActives.push(arr[i].last_active);
		}
			
			
		var tbl_str = "<thead><tr>";
		        tbl_str += "<th>Sl.no</th>";
            tbl_str += "<th>Black Box Id</th>";
            tbl_str += "<th>Mobile Id</th>";
            tbl_str += "<th>MO</th>";
            tbl_str += "<th>MT</th>";
						tbl_str += "<th>Call time</th>";
						tbl_str += "<th>Wait time</th>";
						tbl_str += "<th>Last active</th>";
            tbl_str += "</tr></thead>";
						
		for (var i=0;i<hash_ids.length;i++){
			var pos = parseInt(1)+parseInt(i);
			tbl_str += "<tr>";
            tbl_str += "<td>" + pos + "</td>";
            tbl_str += "<td>" + hash_ids[i] + "</td>";
			tbl_str += "<td>" + android_ids[i] + "</td>";
            tbl_str += "<td>" + mo_ids[i] + "</td>";
            tbl_str += "<td>" + mt_ids[i] + "</td>";
			tbl_str += "<td>" + call_times[i] + "</td>";
			tbl_str += "<td>" + wait_times[i] + "</td>";
			tbl_str += "<td>" + lastActives[i] + "</td>";
            tbl_str += "</tr>";
		}
		$('#table_data_configured').html(tbl_str);	
		$('#table_data_configured').DataTable().destroy();
		$('#table_data_configured').DataTable({
		"paging":   true,
		"ordering": true,
		"info":     true,
		"order": [[ 1, "asc" ]]
		});
		}
    });
}

function loadAllData(){
	
	/*$("#calling_time").empty();
	$("#mo_id").empty();
	$("#mt_id").empty();
	$("#select_mobile").empty();
	$("#call_wait_time").empty();*/
// 	$("#select_box_drop").empty();         	 		
	var hash_id = $('#select_box_drop').val();
		console.log("in load data"+hash_id);
	$.ajax({
        url: "apis/fetch_all_data_based_on_hash_id.php",
        type: "GET",
        data:{hash_id:hash_id},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
        },
 		success:function(result) 
		{
			result = jQuery.parseJSON(result)
			var success = result.success;
			var arr = result.data;
			if(success==1){
			var call_times = arr[0].call_time;	
			var call_wait_times = arr[0].wait_time;
			var mo_ids = arr[0].mo_id;
			var mt_ids = arr[0].mt_id;
			var hash_ids = arr [0].hash_id;
			var android_ids = arr[0].android_id;
			
			$("#calling_time").val(call_times);
			$("#mo_id").val(mo_ids);
			$("#mt_id").val(mt_ids);
			$("#select_mobile").val(android_ids);
			$("#call_wait_time").val(call_wait_times);
			$("#select_box_drop").val(hash_ids);
			}
			else
			{
				alert("No Details Found for this device");
				fetchKitDetails();
				$("#calling_time").empty();
				$("#mo_id").empty();
				$("#mt_id").empty();
				$("#select_mobile").empty();
				$("#call_wait_time").empty();
				$("#select_box_drop").empty();
			}
		}		
    });
}


/*function fetchMobileList()
{
	$.ajax({
        url: "apis/fetch_android_ids.php",
        type: "GET", 
		data:{},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
        },
 		success:function(result) 
		{	
			result = jQuery.parseJSON(result)
			var arr = result.data;
			var android_id_options ='';
			var android_ids = [];
			for (var i = 0; i < arr.length; i++) {
				android_ids.push(arr[i].android_id);
			} 
			for (var i=0;i<android_ids.length;i++){
			android_id_options += '<option value="'+ android_ids[i] + '">' + android_ids[i] + '</option>';
		}
		console.log(android_id_options);
		$('#select_mobile').append(android_id_options);	
		}
    });
}*/
</script>
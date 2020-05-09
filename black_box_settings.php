<?php include('header_new.php'); ?>
<section id="page-content">
    <div class="body-content animated fadeIn">
          <!-- Content Row -->
          <div class="row">
            <!-- Content Column -->
		   <div class="col-lg-12 col-md-12 col-sm-12">
              <!-- Project Card Example -->
              <div class="panel rounded shadow">
				<div class="pull-left loader" id ="pending_licence_list_loading"></div> 
                <div class="panel-heading">
                    <div class="pull-left">
                         <h3 class="panel-title" style="color: #fff">Pending List</h3>
                     </div>
                    <div class="clearfix"></div>
                  </div>
                <div  class="panel-body table-responsive-sm"> 
				<!--here is table content -->
				<table id="pending_licence_table" class="table table-striped table-bordered" >
				</table>				
                </div>
              </div>
           </div>
		   <div class="col-lg-12 col-md-12 col-sm-12">
              <!-- Project Card Example -->
              <div class="panel rounded shadow">
				<div class="pull-left loader" id ="device_list_loading"></div> 
                <div class="panel-heading">
                    <div class="pull-left">
                         <h3 class="panel-title" style="color: #fff">Device List</h3>
                     </div>
                    <div class="clearfix"></div>
                  </div>
                <div  class="panel-body table-responsive-sm"> 
				<!--here is table content -->
				<table id="device_list_table" class="table table-striped table-bordered" >
				</table>				
                </div>
              </div>
           </div>
		</div><!--end of the row-->
        </div><!--end of container fluid-->
      </div>
<?php include('footer_new.php')?> 
<script>
 var imeiNo;
$(document).ready(function(){
	loadLicenseTable();
	loadPendingTable();
});

	function loadLicenseTable(){         	 		
	var deviceLicenceListLoading =$('#device_list_loading');
	inProgress(deviceLicenceListLoading);
		$.ajax({
			url: "ajax/fetch_all_licence.php",
			type: "POST", 
			data:{},
			error: function(jqXHR, textStatus, errorThrown){
			console.log(textStatus, errorThrown);
			},
			success:function(result) 
			{
				stopProgress(deviceLicenceListLoading);
				var imeis =[];
				var inserted_bys = [];
				result = jQuery.parseJSON(result)
				var arr = result.data;
				var message = result.success;
				for (var i = 0; i < arr.length; i++)
			{
				imeis.push(arr[i].imei);
				inserted_bys.push(arr[i].inserted_by);
			}
			var tbl_str = "<thead><tr>";
				tbl_str += "<th>Sl.no</th>";
				tbl_str += "<th>IMEI</th>";
				tbl_str += "<th>Added by</th>";
				tbl_str += "</tr></thead>";
							
			for (var i=0;i<imeis.length;i++){
				var pos = parseInt(1)+parseInt(i);
				tbl_str += "<tr>";
				tbl_str += "<td>" + pos + "</td>";
				tbl_str += "<td>" + imeis[i] + "</td>";
				tbl_str += "<td>" + inserted_bys[i] + "</td>";
				tbl_str += "</tr>";
			}
			$('#device_list_table').html(tbl_str);	
			$('#device_list_table').DataTable().destroy();
			$('#device_list_table').DataTable({
			"paging":   true,
			"ordering": true,
			"info":     true,
			"order": [[ 1, "asc" ]]
			});
			}
		});
	} 
	function loadPendingTable(){         	 		
	var pendingLicenceListLoading =$('#pending_licence_list_loading');
	inProgress(pendingLicenceListLoading);
		$.ajax({
			url: "ajax/fetch_pending_licence.php",
			type: "POST", 
			data:{},
			error: function(jqXHR, textStatus, errorThrown){
			console.log(textStatus, errorThrown);
			},
			success:function(result) 
			{
				stopProgress(pendingLicenceListLoading);
				var action = "<div class='col-md-12 text-center select_width'><button id='accept_btn' class='btn btn-success'>Accept</button>";
				    action+= "<button id='reject_btn' class='btn btn-danger btn-info'>Reject</button></div>";
				var imeis =[];
				var inserted_bys = [];
				result = jQuery.parseJSON(result)
				var arr = result.data;
				var message = result.success;
				for (var i = 0; i < arr.length; i++)
			{
				imeis.push(arr[i].imei);
				inserted_bys.push(arr[i].inserted_by);
			}
			var tbl_str = "<thead><tr>";
				tbl_str += "<th>Sl.no</th>";
				tbl_str += "<th>IMEI</th>";
				tbl_str += "<th>Added by</th>";
				tbl_str += "<th>Action</th>";
				tbl_str += "</tr></thead>";
							
			for (var i=0;i<imeis.length;i++){
				var pos = parseInt(1)+parseInt(i);
				tbl_str += "<tr>";
				tbl_str += "<td>" + pos + "</td>";
				tbl_str += "<td>" + imeis[i] + "</td>";
				tbl_str += "<td>" + inserted_bys[i] + "</td>";
				tbl_str += "<td>" + action + "</td>";
				tbl_str += "</tr>";
			}
			$('#pending_licence_table').on( 'click', '#accept_btn', function (e) {
			e.preventDefault();
			var $data= [];
			var x = $(this).closest("tr");;
			var cells = x.find('td');
			$.each(cells, function(){
				var $imei = ($(this).text());
				$data.push($imei);
			});
			if ($data[1] == "N/A") {
			}
			imeiNo = $data[1];
			acceptLicence();
			console.log("accept imei"+$data[1]);
		});
		
		$('#pending_licence_table').on( 'click', '#reject_btn', function (e) {
			e.preventDefault();
			var $data= [];
			var x = $(this).closest("tr");;
			var cells = x.find('td');
			$.each(cells, function(){
				var $imei = ($(this).text());
				$data.push($imei);
			});
			if ($data[1] == "N/A") {
			}
			imeiNo = $data[1];
			rejectLicence();
			console.log("reject imei"+$data[1]);
		});
		
			$('#pending_licence_table').html(tbl_str);	
			$('#pending_licence_table').DataTable().destroy();
			$('#pending_licence_table').DataTable({
			"paging":   true,
			"ordering": true,
			"info":     true,
			"order": [[ 1, "asc" ]]
			});
			}
		});
	}
	
  function acceptLicence(){
		$.ajax({
			url: "ajax/accept_request_licence.php",
			type: "POST", 
			data:{imei:imeiNo},
			error: function(jqXHR, textStatus, errorThrown){
			console.log(textStatus, errorThrown);
			},
			success:function(result) 
			{
				location.reload();
				result = jQuery.parseJSON(result)
				var arr = result.success;
				console.log("accept msg"+arr);
				loadLicenseTable();
			}
		});
    }
  
    function rejectLicence(){
		$.ajax({
			url: "ajax/reject_request_licence.php",
			type: "POST", 
			data:{imei:imeiNo},
			error: function(jqXHR, textStatus, errorThrown){
			console.log(textStatus, errorThrown);
			},
			success:function(result) 
			{
				location.reload();
				result = jQuery.parseJSON(result)
				var arr = result.success;
				console.log("accept msg"+arr); 
				loadLicenseTable();
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
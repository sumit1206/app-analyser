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
	background-color: #fff;
	color: white;
  }
</style>
<?php include('header_new.php'); ?>

<section id="page-content">
    <div class="body-content container-fluid animated fadeIn">
          <!-- Content Row -->
          <div class="row">
            <!-- Content Column -->
		   <div class="col-lg-12 col-md-12 col-sm-12">
              <!-- Project Card Example -->
              <div class="panel rounded shadow">
				<div class="pull-left loader" id ="application_list_loading"></div> 
                <div class="panel-heading">
				<div class="row">
                    <div class="col-sm-12 col-md-10 col-lg-10 pull-left">
                         <h3 class="panel-title" style="color: #fff">Application list<span></span></h3>
                     </div>
					  <div class="col-sm-12 col-md-2 col-lg-2">
						 <button id="add_new_btn" style="float:right; height: auto;" type="button" class="btn btn-light btn-info">Add Application</button>
                     </div>
				</div>	 
                    <div class="clearfix"></div>
                  </div>
				<div  class="panel-body table-responsive-sm">
                </div>
                <div  class="panel-body table-responsive-sm">
				<!--here is table content -->
				<table id="application_table" class="table table-striped table-bordered"></table>				
                </div>
              </div>
           </div>
		</div><!--end of the row-->
        </div><!--end of container fluid-->
      </div>
 <div id="myModal" class="modal">
  <div class="modal-content ">
    <div class=" modal-header">
      <span class=" panel-title close">&times;</span>
      <h3 class="panel-title">Edit details</h3>
    </div>
    <div class="modal-body">
		<div class="modal-body">
        <form>
          <div class="form-group">
            <label class="col-form-label">App name:</label>
            <input type="text" class="form-control" id="app-name">
          </div>
          <div class="form-group">
            <label  class="col-form-label">Package name:</label>
            <input type="text" class="form-control" id="package_name">
          </div>
		   <div class="form-group">
            <label class="col-form-label">URL:</label>
            <input type="text" class="form-control" id="app-url">
          </div>
        </form>
      </div>
	  <div class="form-body"> 
			<div class="col-md-12 text-center">
				<button type="button" name="btn_save" id="btn_save" class="btn btn-info">Save</button>
		    </div>
	  </div>
	   <div class="clearfix"></div>
    </div>
  </div>
</div>
<div id="delete-modal" class="modal">
  <div class="modal-content ">
    <div class=" modal-header">
      <span class=" panel-title close" id="close_btn">&times;</span>
      <h3 class="panel-title">Delete details</h3>
    </div>
    <div class="modal-body">
		<div class="modal-body">
		   <div class="form-group">
            <label class="col-form-label">Do you want to delete?</label>
          </div>
      </div>
	   <div class="modal-footer">
        <button type="button" id="dlt_btn" class="btn btn-danger">Delete</button>
      </div>
	   <div class="clearfix"></div>
    </div>
  </div>
</div>
<?php include('footer_new.php')?> 
<script>
$(document).ready(function(){
	loadApplicationTable();
	$("#add_new_btn").click(function(){
			openPopUp();
	});		
});
var serialNo;
	var appName = $("#app-name").val();
	var packageName = $("#package_name").val();
	var appUrl = $("#app-url").val();

	function loadApplicationTable(){ 
	var application_list_loading =$('#application_list_loading');
	inProgress(application_list_loading);
		$.ajax({
			url: "ajax/fetch_all_app_url_package.php",
			type: "POST", 
			data:{},
			error: function(jqXHR, textStatus, errorThrown){
			console.log(textStatus, errorThrown);
			},
			success:function(result) 
			{
				var action = "<div class='col-md-12 select_width'><button id='edit_btn' data-toggle='modal' data-target='#add_new_popup_modal' class='btn btn-success btn-info'>Edit</button>";
				    action+= "<button id='delete_btn' class='btn btn-danger btn-info'>Delete</button></div>";
				stopProgress(application_list_loading);
				var app_names =[];
				var package_names = [];
				var urls = [];
				var slNo = [];
				result = jQuery.parseJSON(result)
				var arr = result.data;
				var message = result.success;
				for (var i = 0; i < arr.length; i++)
			{
				app_names.push(arr[i].app_name);
				package_names.push(arr[i].package_name);
				urls.push(arr[i].url);
				slNo.push(arr[i].sl_no);
			}
			var tbl_str = "<thead><tr>";
				tbl_str += "<th>Sl.no</th>";
				tbl_str += "<th>App name</th>";
				tbl_str += "<th>Package name</th>";
				tbl_str += "<th>Video URL</th>";
				tbl_str += "<th>Action</th>";
				tbl_str += "</tr></thead>";
							
			for(var i=0;i<app_names.length;i++)
			{
				var pos = parseInt(1)+parseInt(i);
				tbl_str += "<tr>";
				tbl_str += "<td>" + slNo[i] + "</td>";
				tbl_str += "<td>" + app_names[i] + "</td>";
				tbl_str += "<td>" + package_names[i] + "</td>";
				tbl_str += "<td>" + urls[i] + "</td>";
				tbl_str += "<td>" + action + "</td>";
				tbl_str += "</tr>";
			}	
			$('#application_table').html(tbl_str);	
			$('#application_table').DataTable().destroy();
			$('#application_table').DataTable({
			"paging":   true,
			"ordering": true,
			"info":     true,
			"order": [[ 1, "asc" ]]
			});
			$('#application_table').on( 'click', '#edit_btn', function (e) {
			e.preventDefault();
			openPopUp();
			var $data= [];
			var x = $(this).closest("tr");;
			var cells = x.find('td');
			$.each(cells, function(){
				var $appName = ($(this).text());
				$data.push($appName);
			});
			if ($data[1] == "N/A") {
			}
			serialNo = $data[0];
			appName = $data[1];
			packageName = $data[2];
			appUrl = $data[3];
			console.log("serial"+slNo);
			$("#app-name").val(appName);
			$("#package_name").val(packageName);
			$("#app-url").val(appUrl);
			});
			$('#application_table').on( 'click', '#delete_btn', function (e) {
			e.preventDefault();
			openDeletePopUp();
			var $data= [];
			var x = $(this).closest("tr");;
			var cells = x.find('td');
			$.each(cells, function(){
				appName = ($(this).text());
				$data.push(appName);
			});
			if ($data[1] == "N/A") {
			}
			serialNo = $data[0];
			appName = $data[1];
			packageName = $data[2];
			appUrl = $data[3];
			});
			}
		});
	}
	
	function addData(){
		appName = $("#app-name").val();
		packageName = $("#package_name").val();
		appUrl = $("#app-url").val();

		console.log("Add data called");
		$.ajax({
			url: "ajax/add_app_data.php",
			type: "GET",
			dataType: "json",			
			data:{app_name:appName, package_name:packageName, url:appUrl},
			error: function(jqXHR, textStatus, errorThrown){
			console.log(textStatus, errorThrown);
			},
			success:function(result) 
			{
				//location.reload();
				result = jQuery.parseJSON(result)
				var arr = result.success;
				console.log("accept msg"+arr);
				loadApplicationTable();
			}
		});
	}
	
	
	function editData(){
		appName = $("#app-name").val();
		packageName = $("#package_name").val();
		appUrl = $("#app-url").val();

		console.log("in edit data "+serialNo);
		$.ajax({
			url: "ajax/edit_app_url.php",
			type: "GET",
			dataType: "json",			
			data:{sl_no:serialNo, app_name:appName, package_name:packageName, url:appUrl},
			error: function(jqXHR, textStatus, errorThrown){
			console.log(textStatus, errorThrown);
			},
			success:function(result) 
			{
				location.reload();
				result = jQuery.parseJSON(result)
				var arr = result.success;
				console.log("accept msg"+arr);
				loadApplicationTable();
			}
		});
	}
	function deleteData(){
		console.log("in edit data "+serialNo);
		$.ajax({
			url: "ajax/delete_app_url_data.php",
			type: "GET",
			dataType: "json",			
			data:{sl_no:serialNo},
			error: function(jqXHR, textStatus, errorThrown){
			console.log(textStatus, errorThrown);
			},
			success:function(result) 
			{
				location.reload(); 
				result = jQuery.parseJSON(result)
				var arr = result.success;
				console.log("accept msg"+arr);
				loadApplicationTable();
			}
		});
	}
	function openPopUp(){
	console.log("open pop up called");
	var modal = document.getElementById("myModal");
	var span = document.getElementsByClassName("close")[0];

	$("#btn_save").click(function(){
		console.log("in btn click data "+serialNo+appName+packageName+appUrl);
		editData();
		addData();
	});
	
	modal.style.display = "block";
	span.onclick = function() {
	  modal.style.display = "none";
	}
	window.onclick = function(event) {
	  if (event.target == modal) {
		modal.style.display = "block";
	  }
	}
	}

	function openDeletePopUp(){
	console.log("open pop up called");
	var modal = document.getElementById("delete-modal");
	var span = document.getElementById("close_btn");

	$("#dlt_btn").click(function(){
	  console.log("in delete btn click data "+serialNo+appName+packageName+appUrl);
	  deleteData();
	});
	modal.style.display = "block";
	span.onclick = function() {
	  modal.style.display = "none";
	}
	window.onclick = function(event) {
	  if (event.target == modal) {
		modal.style.display = "block";
	  }
	}
	}

  function inProgress(elem) {
	$(elem).show(); 
  }
  
  function stopProgress(elem) {
	$(elem).hide();
  }
</script>
<?php include('header_new.php');?>
<section id="page-content">
    <div class="body-content animated fadeIn">
		<div class= "row">
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
              <div class="panel rounded shadow">
                <!-- Card Header - Dropdown -->
				<div class="panel-heading">
                      <div class="pull-left">
                          <h3 class="panel-title" style="color: #fff">Back</h3>
                       </div>
                    <div class="clearfix"></div>
                 </div>
              </div>
            </div>
		</div>
          <!-- Content Row -->
          <div class="row">
		   <!--Network details-->
			<div class="col-lg-12 col-md-12 col-sm-12">
              <div class="panel">
				<div class="panel-heading">
					<div class="pull-left loader"></div> 
						<div class="pull-left">
							<h3 class="panel-title">Map</h3>
						</div>
					<div class="clearfix"></div>
				</div>
                <div class="panel-body table-responsive-sm">
					<div id="plotingProgress" style="display: none; width:200px; height:20px;background-color: black; position: absolute; top:-5px; left: 40%; z-index: 100;color: #fff; padding: 0 10px;">Plotting Data.Please Wait...</div>
                    <div id="map" style="width:100%; height:500px;"></div>
                </div>
              </div>
           </div>
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
           </div>
		</div><!--end of the row-->
	</div>	
  <!-- End of Page Wrapper -->
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
</script>
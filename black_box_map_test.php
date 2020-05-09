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
		<!--<div class= "row">
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
                                </div>
                                <div>
                                    <button type="button" name="btn_chart" id="btn_track" class="btn btn-info">Track</button>
                                </div>
                            </div>
                        </form>                         
					</div>
				</div>
            </div>
		</div>-->
		 <div>
            <button type="button" name="btn_chart" id="btn_track" class="btn btn-info">Choose a file</button>
         </div>
          <!-- Content Row -->
          <div class="row">
            <!-- Content Column -->
            <div class="col-lg-12 col-md-12 col-sm-12">
			   <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">Map</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div id="plotingProgress" style="display: none; width:500px; height:20px;background-color: black; position: absolute; top:-5px; left: 40%; z-index: 100;color: #fff; padding: 0 10px;">Plotting Data.Please Wait...</div>
                        <div id="map" style="width:100%; height:500px;"></div>
                    </div>
                </div>
			</div>
		   <!-- End of row -->
		</div>
		<!--starting of table
        </div>-->
	</div>
      <!-- Footer -->
	  
 <?php include('footer_new.php')?>
<script>

var map = L.map("map");
L.tileLayer("http://{s}.tile.osm.org/{z}/{x}/{y}.png").addTo(map);
map.setView([20.5937, 78.9629],5);
var map = L.mapbox.map('map', 'mapbox.streets') 
     .setView([20.5937, 78.9629], 5);
//var myRenderer = L.canvas({ color: '#3388ff'});
//for (var i = 0; i < 100000; i += 1) { // 100k points
//	L.circleMarker(getRandomLatLng(), {
//  	renderer: myRenderer 
//  }).addTo(map).bindPopup('marker ' + i);
//}

function getRandomLatLng() {
	return [
  	-90 + 180 * Math.random(),
    -180 + 360 * Math.random()
  ];
}
function inProgress(elem) {
  $(elem).show();
}

function stopProgress(elem) {
  $(elem).hide();
}
</script>
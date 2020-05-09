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
?>
<?php include('header_new.php'); ?>
<script src="js/dashboard.js"></script>
<section id="page-content">
    <div class="body-content animated fadeIn">
		<!--filtrer section-->
		<div class= "row">
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
              <div class="panel rounded shadow">
                <div class="panel-heading">
                    <div class="pull-left">
                         <h3 class="panel-title" style="color: #fff">Filters</h3>
                     </div>
                    <div class="clearfix"></div>
                  </div>
				<div class="panel-body">
                        <form class="form-inline" >
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
                                    <div class="input-group input-daterange">
                                        <input class="form-control" id="start_dt" value="" autocomplete="off" placeholder="Date">
                                        <div id= "to_text" class="input-group-addon">TO</div>
                                        <input class="form-control" id="end_dt" value="" autocomplete="off" placeholder="Date">
                                    </div>
                                </div> 
							   <div class="clearfix"></div>
								<label class="col-md-12 text-center" style="display: none; color: red;" id="error_text" >error msg</label>
                               <div class="clearfix"></div>
								<div class="form-body"> 
									<div class="col-md-12 text-center">
										<button type="button" name="btn_submit" id="btn_submit" class="btn btn-info">Submit</button>
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
           <!--testing today-->
			<div class="col-xl-3 col-md-3 mb-4">
                <div class="mini-stat clearfix bg-facebook rounded">
                    <div class="mini-stat-info">
						Total test
                        <span id ="total_test">0</span>
                    </div>
                </div>
            </div>
            <!-- Maximum no of buffer Card Example -->
			<div class="col-xl-3 col-md-3 mb-4">
                <div class="mini-stat clearfix bg-facebook rounded">
                    <div class="mini-stat-info">
						Total play time (Sec)
                        <span id ="max_initial_buffer">0</span>
                    </div>
                </div>
            </div>
			<div class="col-xl-3 col-md-3 mb-4">
                <div class="mini-stat clearfix bg-facebook rounded">
                    <div class="mini-stat-info">
						Total buffer time (Sec)
                        <span id ="average_buffer_time">0</span>
                    </div>
                </div>
            </div>
            <!-- Session throughput Card Example -->
			<div class="col-xl-3 col-md-3 mb-4">
                <div class="mini-stat clearfix bg-facebook rounded">
                    <div class="mini-stat-info">
						Total buffers
                        <span id="max_session_throughput">0</span>
                    </div>
                </div>
            </div>
          </div>
          <!-- Content Row -->
          <div class="row">
            <div class="col-xl-12 col-lg-12 col-sm-12">
              <div class="panel">
                <!-- Card Header - Dropdown -->
                <div class="panel-heading">
                    <div class="pull-left loader" id ="performance_loading"></div> 
					<div class="pull-left">
                         <h3 class="panel-title">Performance Overview</h3>
                    </div> 
                    <div class="clearfix"></div>
                </div>
                <!-- Card Body -->
                <div class="panel-body">
                  <div class="col-lg-12 col-sm-12 col-md-12" id="performanceOverViewChart" style="width:100%; height:400px;"> <!--chart-pie pt-4 pb-2-->
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Content Row -->
          <!--<div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
              <div class="panel">
				 <div class="panel-heading">
					<div class="pull-left loader" id ="fst_bit_loading"></div> 
                    <div class="pull-left">
                        <h3 class="panel-title">1st bit recived time</h3>
                    </div>
                        <div class="clearfix"></div>
                 </div>
                <div class="panel-body">
				<!-- here is graph content
				<div class="col-lg-12 col-sm-12 col-md-12" id="chart5" style="width:100%; height:400px;"></div>
				<div class="col-lg-12 col-sm-12 col-md-12" id="application_vs_first_bit_rcv_time" style="width:100%; height:400px;"></div>
					<div class="row" id="first_bit_rcv_time_max_min_layout">
						<div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Min: <span id="min_recived_time" style="display: inherit;">0</span>ms</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Max: <span id="max_recived_time" style="display: inherit;">0</span>ms</label>
                            </div>
                        </div>
						<div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Average : <span id="average_recived_time" style="display: inherit;">0</span>ms</label>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
           </div>-->
		   <div class="col-lg-12 col-md-12 col-sm-12">
              <div class="panel">
				 <div class="panel-heading">
					<div class="pull-left loader" id ="innitial_buffer_time_loading"></div> 
                    <div class="pull-left">
                        <h3 class="panel-title">Average Innitial buffer time</h3>
                    </div>
                        <div class="clearfix"></div>
                 </div>
                <div class="panel-body">
				<!-- here is graph content -->
				<div class="col-lg-12 col-sm-12 col-md-12" id="chart3" style="width:100%; height:400px;"></div>
				<div class="col-lg-12 col-sm-12 col-md-12" id="application_innitial_buffer_time_chart" style="width:100%; height:400px;"></div>
					<div class="row" id="innitial_buffer_time_max_min_layout">
						<div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Min: <span id="min_innitial_buffer_time_chart" style="display: inherit;">0</span>ms</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Max: <span id="max_innitial_buffer_time_chart" style="display: inherit;">0</span>ms</label>
                            </div>
                        </div>
						<div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Average : <span id="average_innitial_buffer_time_chart" style="display: inherit;">0</span>ms</label>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
           </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
              <!-- Project Card Example -->
              <div class="panel shadow mb-4">
				<div class="panel-heading">
					<div class="pull-left loader" id ="session_throughput_loading"></div>
                    <div class="pull-left">
                        <h3 class="panel-title">Average Session Throughput</h3>
                    </div>
                        <div class="clearfix"></div>
                 </div>
                <div class="panel-body">
				<!-- here is graph content-->
				 <div class="col-lg-12 col-sm-12 col-md-12" id="chart2" style="width:100%; height:400px;"></div>
				 <div class="col-lg-12 col-sm-12 col-md-12" id="sessionid_vs_session_throughput_chart" style="width:100%; height:400px;"></div>
				 
				 <div class="row" id="session_throughput_max_min_layout">
						<div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Min: <span id="min_session_throughput_chart" style="display: inherit;">0</span>kbps</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Max: <span id="max_session_throughput_chart" style="display: inherit;">0</span>kbps</label>
                            </div>
                        </div>
						<div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Average: <span id="average_session_throughput_chart" style="display: inherit;">0</span>kbps</label>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
           </div>
		              <div id ="total_no_of_buffer_body" class="col-lg-12 col-md-12 col-sm-12">
              <div class="panel">
				<div class="panel-heading">
					<div class="pull-left loader" id ="total_buffer_count_loading"></div>
                    <div class="pull-left">
                        <h3 class="panel-title">Average buffer count</h3>
                    </div>
                        <div class="clearfix"></div>
                 </div>
                <div class="panel-body">
				<!-- here is graph content-->
        <!--<div class="col-lg-12 col-sm-12 col-md-12" id="application_vs_total_no_of_buffer_chart"></div>-->
        <div class="col-lg-12 col-sm-12 col-md-12" id="application_vs_total_no_of_buffer_chart" style="width:100%; height:400px;"></div>
				<div class="row" id="total_no_of_buffer_max_min_layout"style="width:100%; height:400px;">
						 <div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Min:<span id="min_value_total_no_of_buffer" style="display: inherit;">0</span></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Max:<span id="max_value_total_no_of_buffer" style="display: inherit;">0</span></label>
                            </div>
                        </div>
						<div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Average: <span id="average_value_total_no_of_buffer" style="display: inherit;">0</span></label>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
           </div> 
		   <div class="col-lg-12 col-md-12 col-sm-12">
              <!-- Project Card Example -->
              <div class="panel">
				<div class="panel-heading">
					<div class="pull-left loader" id ="total_buffer_time_loading"></div>
                    <div class="pull-left">
                        <h3 class="panel-title">Average buffer time</h3>
                    </div>
                        <div class="clearfix"></div>
                 </div>
                <div class="panel-body">
				<!-- here is graph content-->
				<div class="col-lg-12 col-sm-12 col-md-12" id="chart" style="width:100%;height:400px;;"></div>
				<div class="col-lg-12 col-sm-12 col-md-12" id="application_vs_total_buffer_time_chart" style="width:100%; height:400px;"></div>
				<div id="total_buffer_time_chart_max_min_layout" class="row">
						<div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Min: <span id="min_value_total_buffer_time" style="display: inherit;">0</span>ms</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Max: <span id="max_value_total_buffer_time" style="display: inherit;">0</span>ms</label>
                            </div>
                        </div>
						<div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Average: <span id="average_value_total_buffer_time" style="display: inherit;">0</span>ms</label>
                            </div>
                        </div>
                   </div>
                </div>
              </div>
           </div>
		    <!--<div id="throughput_division" class="col-lg-12 col-md-12 col-sm-12">
              <div class="panel">
				<div class="panel-heading">
					<div class="pull-left loader" id="throughput_loading"></div>
                    <div class="pull-left">
                        <h3 class="panel-title">Throughput</h3>
                    </div>
                        <div class="clearfix"></div>
                 </div>
                <div class="panel-body">
				<div class="col-lg-12 col-sm-12 col-md-12" id="chart4" style="width:100%; height:400px;"></div>
				<div class="col-lg-12 col-sm-12 col-md-12" id="single_application_vs_throughput_chart" style="width:100%; height:400px;"></div>
				<div class="row" id="throughput_max_min_layout">
						<div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Min: <span id="min_value_throughput" style="display: inherit;">0</span>ms</label>
                            </div>
                        </div>
						<div class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Max: <span id="max_value_throughput" style="display: inherit;">0</span>ms</label>
                            </div>
                        </div>
						< class="col-md-4">
                            <div class="mini-stat-info">
                                <label class="counter">Average: <span id="average_value_throughput" style="display: inherit;">0</span>ms</label>
                            </div>
                        </div>
                  </div>
                </div>-->
              </div>
           </div>
	</div>
</div>
<!-- Footer -->
<?php include('footer_new.php')?>
<!-- end of Footer-->

<?php include('header_s.php'); ?>
<section id="page-content">
    <div class="body-content animated fadeIn">
        <div class="row">
            <div class="col-md-12">
                <div class="panel rounded shadow">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title" style="color: #fff">Filters</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <form class="form-inline" id="frm_chart" name="frm_chart" method="POST">
                            <div class="form-body">
                                <div class="col-md-2">
                                    <select class="form-control select_width" id="city_drop_down">
                                        <option value="">City</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control select_width" id="os_drop_down">
                                        <option value="">Operating System</option>
                                        <option value="Android">Android</option>
                                        <option value="IOS">IOS</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control select_width" id="op_drop_down">
                                        <option value="">Operator</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control select_width" id="trend_drop_down">
                                        <option value="">Trend</option>
                                        <option value="day">Day-wise</option>
                                        <option value="hour">Hour-wise</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="data" name="yearmonth" id="yearmonth" autocomplete="off" value="" class="form-control select_width" placeholder="Date">
                                    <input type="data" name="dayyearmonth" id="dayyearmonth" autocomplete="off" value="" class="form-control select_width" style="display: none;" placeholder="Date">
                                    <div class="input-group input-daterange" style="display: none;">
                                        <input type="text" class="form-control" id="start_dt" value="" autocomplete="off" placeholder="Date">
                                        <div class="input-group-addon">to</div>
                                        <input type="text" class="form-control" id="end_dt" value="" autocomplete="off" placeholder="Date">
                                    </div>
                                </div>                                
                                <div class="col-md-1">
                                    <input type="hidden" name="country" id="country" value="<?php echo $_SESSION['login_details']['country']; ?>">
                                    <button type="button" name="btn_chart" id="btn_chart" class="btn btn-info">Submit</button>
                                </div>
                            </div>
                        </form>                        
                    </div>
                </div>
            </div>
        </div>
        <div id="tour-12" class="row">
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="mini-stat clearfix bg-facebook rounded">
                    <div class="mini-stat-info">
                        CALL CONNECT TIME
                        <span id="call_connect">0</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="mini-stat clearfix bg-twitter rounded">
                    <div class="mini-stat-info">
                        CALL BLOCK RATE
                        <span id="call_block_rate">0</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="mini-stat clearfix bg-googleplus rounded">
                    <div class="mini-stat-info">
                        CALL DROP RATE
                        <span id="call_drop_rate">0</span>
                    </div>
                </div>
            </div>            
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="mini-stat clearfix bg-bitbucket rounded">
                    <div class="mini-stat-info">
                        MUTE CALL RATE
                        <!--<span class="uplink" style="display: table-cell; font-size: 26px; padding: 6px; color: #da06f9;">&#8593; </span>-->
                        <span style="display: table-cell; font-size: 16px; padding: 6px;">Up </span>
                        <span id="mute_call_rate_uplink" style="display: table-cell">0</span>
                        <!--<span class="downlink" style="display: table-cell; font-size: 26px; padding: 6px; color: #04e199;">&#8595;</span>-->
                        <span style="display: table-cell; font-size: 16px; padding: 6px;">Down </span>
                        <span id="mute_call_rate_downlink" style="display: table-cell">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">MAP</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div id="plotingProgress" style="display: none; width:200px; height:20px;background-color: black; position: absolute; top:-5px; left: 40%; z-index: 100;color: #fff; padding: 0 10px;">Plotting Data.Please Wait...</div>
                        <div id="map" style="width:100%; height:500px;"></div>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">CALL CONNECT TIME</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div id="call_connect_time_chart" style="width:100%; height:400px;"></div>
<!--                        <div class="col-md-3">
                            <div class="mini-stat-info">
                                <label class="counter">No. of Samples: <span id="cc_samples" style="display: inherit;">0</span></label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mini-stat-info">
                                <label class="counter">Min: 
                                    <span id="cc_min" style="display: inherit;">0</span>
                                    sec
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mini-stat-info">
                                <label class="counter">Max: 
                                    <span id="cc_max" style="display: inherit;">0</span>
                                    sec
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mini-stat-info">
                                <label class="counter">Avg: 
                                    <span id="cc_avg" style="display: inherit;">0</span>
                                    sec
                                </label>
                            </div>
                        </div>-->
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">BLOCK CALL RATE</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div id="block_call_rate_chart" style="width:100%; height:400px;"></div>
<!--                        <div class="col-md-3" style="width: 15%; padding-right: 5px;">
                            <div class="mini-stat-info">
                                <label class="counter">No. of Calls: <span id="bcr_total_calls" style="display: inherit;">0</span></label>
                            </div>
                        </div>
                        <div class="col-md-3" style="width: 22%; padding-right: 5px;">
                            <div class="mini-stat-info">
                                <label class="counter">No. of Block Calls: <span id="bcr_block_calls" style="display: inherit;">0</span>
                                </label>
                            </div>
                        </div>-->
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">DROP CALL RATE</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div id="drop_call_rate_chart" style="width:100%; height:400px;"></div>
<!--                        <div class="col-md-3" style="width: 15%; padding-right: 5px;">
                            <div class="mini-stat-info">
                                <label class="counter">No. of Calls: <span id="dcr_total_calls" style="display: inherit;">0</span></label>
                            </div>
                        </div>
                        <div class="col-md-3" style="width: 22%; padding-right: 5px;">
                            <div class="mini-stat-info">
                                <label class="counter">No. of Drop Calls: <span id="dcr_drop_calls" style="display: inherit;">0</span>
                                </label>
                            </div>
                        </div>-->
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">MUTE CALL RATE</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div id="mute_call_rate_chart" style="width:100%; height:400px;"></div>
                        <div class="col-md-2" style="width: 15%; padding-right: 5px;">
                            <div class="mini-stat-info">
                                <label class="counter">No. of Calls: <span id="mcr_total_calls" style="display: inherit;">0</span></label>
                            </div>
                        </div>
                        <div class="col-md-3" style="width: 22%; padding-right: 5px;">
                            <div class="mini-stat-info">
                                <label class="counter">No. of Mute Calls: 
                                    <span class="uplink" style="display: inherit; /*font-size: 26px; padding: 6px;*/">&#8593; </span>
                                    <span id="mcr_mute_calls_uplink" style="display: inherit;">0</span>
                                    <span class="downlink" style="display: inherit; /*font-size: 26px; padding: 6px;*/">&#8595; </span>
                                    <span id="mcr_mute_calls_downlink" style="display: inherit;">0</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2" style="width: 21%; padding-right: 5px;">
                            <div class="mini-stat-info">
                                <label class="counter">Min: 
                                    <span class="uplink" style="display: inherit; /*font-size: 26px; padding: 6px;*/">&#8593; </span>
                                    <span id="mcr_min_sec_uplink" style="display: inherit;">0</span>
                                    <span class="downlink" style="display: inherit; /*font-size: 26px; padding: 6px;*/">&#8595; </span>
                                    <span id="mcr_min_sec_downlink" style="display: inherit;">0</span>
                                    (sec)
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2" style="width: 21%; padding-right: 5px;">
                            <div class="mini-stat-info">
                                <label class="counter">Max: 
                                    <span class="uplink" style="display: inherit; /*font-size: 26px; padding: 6px;*/">&#8593; </span>
                                    <span id="mcr_max_sec_uplink" style="display: inherit;">0</span>
                                    <span class="downlink" style="display: inherit; /*font-size: 26px; padding: 6px;*/">&#8595; </span>
                                    <span id="mcr_max_sec_downlink" style="display: inherit;">0</span>
                                    (sec)
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2" style="padding-right: 5px;">
                            <div class="mini-stat-info">
                                <label class="counter">Avg: 
                                    <span class="uplink" style="display: inherit; /*font-size: 26px; padding: 6px;*/">&#8593; </span>
                                    <span id="mcr_avg_sec_uplink" style="display: inherit;">0</span>
                                    <span class="downlink" style="display: inherit; /*font-size: 26px; padding: 6px;*/">&#8595; </span>
                                    <span id="mcr_avg_sec_downlink" style="display: inherit;">0</span>
                                    (sec)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">MUTE DURATION (Total Mute Duration/Call)</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div id="mute_duration_chart" style="width:100%; height:400px;"></div>
                        <div class="col-md-3" style="padding-right: 5px;">
                            <div class="mini-stat-info">
                                <label class="counter">Min: 
                                    <span class="uplink" style="display: inherit; /*font-size: 26px; padding: 6px;*/">&#8593; </span>
                                    <span id="mute_duration_min_uplink" style="display: inherit;">0</span>
                                    <span class="downlink" style="display: inherit; /*font-size: 26px; padding: 6px;*/">&#8595; </span>
                                    <span id="mute_duration_min_downlink" style="display: inherit;">0</span>
                                    (sec)
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3" style="padding-right: 5px;">
                            <div class="mini-stat-info">
                                <label class="counter">Max: 
                                    <span class="uplink" style="display: inherit; /*font-size: 26px; padding: 6px;*/">&#8593; </span>
                                    <span id="mute_duration_max_uplink" style="display: inherit;">0</span>
                                    <span class="downlink" style="display: inherit; /*font-size: 26px; padding: 6px;*/">&#8595; </span>
                                    <span id="mute_duration_max_downlink" style="display: inherit;">0</span>
                                    (sec)
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3" style="padding-right: 5px;">
                            <div class="mini-stat-info">
                                <label class="counter">Avg: 
                                    <span class="uplink" style="display: inherit; /*font-size: 26px; padding: 6px;*/">&#8593; </span>
                                    <span id="mute_duration_avg_uplink" style="display: inherit;">0</span>
                                    <span class="downlink" style="display: inherit; /*font-size: 26px; padding: 6px;*/">&#8595; </span>
                                    <span id="mute_duration_avg_downlink" style="display: inherit;">0</span>
                                    (sec)
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3" style="padding-right: 5px;">
                            <div class="mini-stat-info">
                                <label class="counter">No. of Mute Calls: 
                                    <span class="uplink" style="display: inherit; /*font-size: 26px; padding: 6px;*/">&#8593; </span>
                                    <span id="mute_duration_calls_uplink" style="display: inherit;">0</span>
                                    <span class="downlink" style="display: inherit; /*font-size: 26px; padding: 6px;*/">&#8595; </span>
                                    <span id="mute_duration_calls_downlink" style="display: inherit;">0</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="scatter_chart">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">COVERAGE v/s QUALITY v/s DOWNLINK MUTE</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div id="cov_qua_mute_chart" style="width:100%; height:520px;"></div>
                        <div class="col-md-3">
                            <div class="mini-stat-info">
                                <label class="counter">Min: <span id="mute_call_duration_min" style="display: inherit;">0</span>sec</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mini-stat-info">
                                <label class="counter">Max: <span id="mute_call_duration_max" style="display: inherit;">0</span>sec</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mini-stat-info">
                                <label class="counter">Avg: <span id="mute_call_duration_avg" style="display: inherit;">0</span>sec</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mini-stat-info">
                                <label class="counter">No. of Samples: <span id="mute_call_duration_calls" style="display: inherit;">0</span></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">CALL CONNECT TIME</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div id="call_connect_chart" style="width:100%; height:400px;"></div>
                        <div class="col-md-3">
                            <div class="mini-stat-info">
                                <label class="counter">Min: <span id="call_connect_min" style="display: inherit;">0</span>sec</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mini-stat-info">
                                <label class="counter">Max: <span id="call_connect_max" style="display: inherit;">0</span>sec</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mini-stat-info">
                                <label class="counter">Avg: <span id="call_connect_avg" style="display: inherit;">0</span>sec</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mini-stat-info">
                                <label class="counter">No. of Samples: <span id="call_connect_calls" style="display: inherit;">0</span></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="compare">
            <div class="panel panel-default" id="compare1" style="display: none;width: 305.8px;opacity: 0.85;margin-left:30px;margin-top: 80px;">
                <div class="panel-heading" id="head_back_color" style="background-color:#dc3d35;color:#fff">
                    <h3 class="panel-title" id="call_type">-</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6" style="font-size:12px;">Date/Time</div>
                        <div class="col-md-6" id="date_time" style="font-size:12px;">-</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" style="font-size:12px;">SINR</div>
                        <div class="col-md-6" id="sinr" style="font-size:12px;">-</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" style="font-size:12px;">RSRP</div>
                        <div class="col-md-6" id="rsrp" style="font-size:12px;">-</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" style="font-size:12px;">Cell ID</div>
                        <div class="col-md-6" id="cid" style="font-size:12px;">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .downlink {
            color: #04e199;
        }
        .uplink {
            color: #da06f9;
        }
        .gmnoprint span {display: none;}
        .gmnoprint a {display: none;}
        img[src="https://maps.gstatic.com/mapfiles/api-3/images/google4.png"] {display: none;}
        
        #legend {
            font-family: Arial, sans-serif;
            background: #fff;
            padding: 10px;
            margin: 10px;
            border: 0px solid #000;
            box-shadow: 2px 2px;
            font-size: 13px;
            line-height: 21px;
            cursor: pointer;
        }
        #legend h3 {
            margin-top: 0;
            font-size: 15px;
            font-weight: bold;
        }
        #legend img {
            vertical-align: middle;
        }
    </style>    
    
    <script type='text/javascript' src="./high_charts-6.1.1/code/highcharts.js"></script>    
    <script>
        var country = $("#country").val();
        var mymap = L.map('map').setView([20.5937, 78.9629], 5);

        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
            maxZoom: 18,
            attribution: '',
            id: 'mapbox/streets-v11'
        }).addTo(mymap);

    </script>
<?php include('footer_s.php'); ?>
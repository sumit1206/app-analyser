<?php include('header.php'); ?>
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
<!--                        <span style="display: table-cell; font-size: 16px; padding: 6px;">Up </span>
                        <span id="mute_call_rate_uplink" style="display: table-cell">-</span>-->
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
                            <h3 class="panel-title">Map</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div id="plotingProgress" style="display: none; width:200px; height:20px;background-color: black; position: absolute; top:-5px; left: 40%; z-index: 100;color: #fff; padding: 0 10px;">Plotting Data.Please Wait...</div>
                        <div id="color_legend" style="display: none; background-color:#fff;position:absolute;top:20%;left:2%;z-index:999;max-height: 500px;overflow: scroll;resize: both;width: 180px;height:250px;"></div>
                        <div id="map" style="width:100%; height:500px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered nowrap">
                    <tr>
                        <th>KPI</th>
                        <th>Value</th>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Call Attempt</td>
                        <td id="kpi_call_attempt">0</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Call Block</td>
                        <td id="kpi_call_block">0</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Call Established</td>
                        <td id="kpi_call_established">0</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Call Drop</td>
                        <td id="kpi_call_drop">0</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Call Block Rate</td>
                        <td id="kpi_call_block_rate">0</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Call Drop Rate</td>
                        <td id="kpi_call_drop_rate">0</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Call Setup Time</td>
                        <td id="kpi_call_setup_time">0</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Mute Call Rate</td>
                        <td id="kpi_mute_call_rate">0</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">Call Connect Time</h3>
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
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">Call Connect Time</h3>
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
                            <h3 class="panel-title">Block Call Rate</h3>
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
                            <h3 class="panel-title">Drop Call Rate</h3>
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
                            <h3 class="panel-title">Mute Call Rate</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div id="mute_call_rate_chart" style="width:100%; height:400px;"></div>
<!--                        <div class="col-md-2" style="width: 15%; padding-right: 5px;">
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
                        </div>-->
                        <div class="col-md-12">
                            <!--<table class="col-md-12">-->
                            <table class="table table-striped table-bordered nowrap">
                                <tr>
                                    <th>Direction</th>
                                    <th>No. of Calls Established</th>
                                    <th>No. of Mute Calls</th>
                                    <th>Min(sec)</th>
                                    <th>Max(sec)</th>
                                    <th>Avg(sec)</th>
                                </tr>
                                <tr>
                                    <td>Uplink</td>
                                    <!--<td class="mcr_total_calls">0</td>-->
                                    <td class="">-</td>
                                    <td id="mcr_mute_calls_uplink">0</td>
                                    <td id="mcr_min_sec_uplink">0</td>
                                    <td id="mcr_max_sec_uplink">0</td>
                                    <td id="mcr_avg_sec_uplink">0</td>
                                </tr>
                                <tr>
                                    <td>Downlink</td>
                                    <td class="mcr_total_calls">0</td>
                                    <td id="mcr_mute_calls_downlink">0</td>
                                    <td id="mcr_min_sec_downlink">0</td>
                                    <td id="mcr_max_sec_downlink">0</td>
                                    <td id="mcr_avg_sec_downlink">0</td>
                                </tr>
                            </table>
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
                            <h3 class="panel-title">Mute Duration (Total Mute Duration/Call)</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div id="mute_duration_chart" style="width:100%; height:400px;"></div>
<!--                        <div class="col-md-3" style="padding-right: 5px;">
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
                        </div>-->
                        <div class="col-md-12">
<!--                            <table class="col-md-12">-->
                            <table class="table table-striped table-bordered nowrap">
                                <tr>
                                    <th>Direction</th>
                                    <th>No. of Mute Calls</th>
                                    <th>Min(sec)</th>
                                    <th>Max(sec)</th>
                                    <th>Avg(sec)</th>
                                </tr>
                                <tr>
                                    <td>Uplink</td>
                                    <td id="mute_duration_calls_uplink">-</td>
                                    <td id="mute_duration_min_uplink">0</td>
                                    <td id="mute_duration_max_uplink">0</td>
                                    <td id="mute_duration_avg_uplink">0</td>
                                </tr>
                                <tr>
                                    <td>Downlink</td>
                                    <td id="mute_duration_calls_downlink">0</td>
                                    <td id="mute_duration_min_downlink">0</td>
                                    <td id="mute_duration_max_downlink">0</td>
                                    <td id="mute_duration_avg_downlink">0</td>
                                </tr>
                            </table>
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
                            <h3 class="panel-title">Coverage v/s Quality v/s Downlik Mute</h3>
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
    </div>
    
    <style>
        .downlink {
            color: #04e199;
        }
        .uplink {
            color: #da06f9;
        }
        input[type=color] {
            width: 16px;
            height: 16px; 
            border-radius: 50%;
            overflow: hidden;
            border: none !important;
        }

        input[type=color]::-webkit-color-swatch {
          border: none;
          border-radius: 50%;
          padding: 0;
        }

        input[type=color]::-webkit-color-swatch-wrapper {
            border: none;
            border-radius: 50%;
            padding: 0;
        }

        #legends1 tr td {
            border: none !important;
            padding: 0px
        }
    </style>    
    
    <script type='text/javascript' src="./high_charts-6.1.1/code/highcharts.js"></script>    
    <script>
        var country = $("#country").val();
        var mymap;
        
        $.ajax({
            type:'POST',
            url:'ajax/save_frm_data_new.php',
            data:{type: 'country_lat_lng', country: country},
            dataType:'json',
            beforeSend :function() {
    //            jQuery('#txt_city').find("option:eq(0)").html("Please wait..");
            },
            success:function(jdata) {
                if(jdata.status = 200) {
                    var country_lat_lng = jdata.message;
                    mymap = L.map('map').setView([country_lat_lng['lat'],country_lat_lng['lng']], 5);
                    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                        maxZoom: 18,
                        attribution: '',
                        id: 'mapbox/streets-v11'
                    }).addTo(mymap);
                }
            }
        });
        //"https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw"
        
//        var mymap = L.map('map').setView([20.5937, 78.9629], 5);
//
//        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
//            maxZoom: 18,
//            attribution: '',
//            id: 'mapbox/streets-v11'
//        }).addTo(mymap);

    </script>
<?php include('footer.php'); ?>

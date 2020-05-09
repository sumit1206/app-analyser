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
                        <!--<form class="form-inline" id="frm_chart" name="frm_chart" method="POST">-->
                            <div class="form-body">
                                <div class="col-md-3">
                                    <select class="form-control select_width" id="city_drop_down">
                                        <option value="">City</option>
                                        <option value="Mumbai">Mumbai</option>
                                        <option value="Delhi">Delhi</option>
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
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-body">
                                <div class="col-md-12 text-center">
                                    <input type="hidden" name="country" id="country" value="<?php echo $_SESSION['login_details']['country']; ?>">
                                    <button type="button" name="btn_map" id="btn_map" class="btn btn-info">Submit</button>
                                    <button type="button" name="btn_export" id="btn_export" class="btn btn-info">Export</button>
                                </div>
                            </div>                            
                        <!--</form>-->                        
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">Drop & Block Call Details</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <table id="drop_block_rf_tbl" class="table table-striped table-bordered nowrap"></table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">Mute Call Details</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <table id="mute_rf_tbl" class="table table-striped table-bordered nowrap"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        var country = $("#country").val();
    </script>
        
    <link href="assets/admin/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <script src="assets/admin/js/jquery.dataTables.min.js"></script>
    <script src="assets/admin/js/dataTables.bootstrap.min.js"></script>
    <?php include('footer.php'); ?>
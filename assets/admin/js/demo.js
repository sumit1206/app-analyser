var BlankonDemo = function () {

    // =========================================================================
    // SETTINGS APP
    // =========================================================================
    var adminCssPath = BlankonApp.handleBaseURL()+'/assets/admin/css';

    return {

        // =========================================================================
        // CONSTRUCTOR APP
        // =========================================================================
        init: function () {
            BlankonDemo.handleChooseThemes();
            BlankonDemo.handleNavbarColor();
            BlankonDemo.handleSidebarColor();
            BlankonDemo.handleLayoutSetting();
        },
        
        // =========================================================================
        // CHOOSE THEMES
        // =========================================================================
        handleChooseThemes: function () {
            // Check cookie for color schemes
            if ($.cookie('color_schemes')) {
                $('link#theme').attr('href', adminCssPath+'/themes/'+$.cookie('color_schemes')+'.theme.css');
            }
            // Check cookie for navbar color
            if ($.cookie('navbar_color')) {
                $('.navbar-toolbar').attr('class', 'navbar navbar-toolbar navbar-'+$.cookie('navbar_color'));
            }
            // Check cookie for sidebar color
            if ($.cookie('sidebar_color')) {
                // Check variant sidebar class
                if($('#sidebar-left').hasClass('sidebar-box')){
                    $('#sidebar-left').attr('class','sidebar-box sidebar-'+$.cookie('sidebar_color'));
                }
                else if($('#sidebar-left').hasClass('sidebar-rounded')){
                    $('#sidebar-left').attr('class','sidebar-rounded sidebar-'+$.cookie('sidebar_color'));
                }
                else if($('#sidebar-left').hasClass('sidebar-circle')){
                    $('#sidebar-left').attr('class','sidebar-circle sidebar-'+$.cookie('sidebar_color'));
                }
                else if($('#sidebar-left').attr('class') == ''){
                    $('#sidebar-left').attr('class','sidebar-'+$.cookie('sidebar_color'));
                }
            }

            $('.color-schemes .theme').on('click',function(){

                // Create variable name selector file css
                var themename = $(this).find('.hide').text();

                // Add effect sound
                if($('.page-sound').length){
                    ion.sound.play("camera_flashing_2");
                }

                // Add attribut href css theme
                $('link#theme').attr('href', adminCssPath+'/themes/'+themename+'.theme.css');

                // Set cookie theme name value to variable themename
                $.cookie('color_schemes',themename, {expires: 1});

            });
        },

        // =========================================================================
        // NAVBAR COLOR
        // =========================================================================
        handleNavbarColor: function () {
            $('.navbar-color .theme').on('click',function(){
                // Create variable name selector file css
                var classname = $(this).find('.hide').text();
                // Add effect sound
                if($('.page-sound').length){
                    ion.sound.play("camera_flashing_2");
                }
                // Add class navbar-color
                $('.navbar-toolbar').attr('class', 'navbar navbar-toolbar navbar-'+classname);
                // Set cookie theme name value to variable classname
                $.cookie('navbar_color',classname, {expires: 1});
            });
        },

        // =========================================================================
        // SIDEBAR COLOR
        // =========================================================================
        handleSidebarColor: function () {
            $('.sidebar-color .theme').on('click',function(){
                // Create variable name selector file css
                var classname = $(this).find('.hide').text();
                // Add effect sound
                if($('.page-sound').length){
                    ion.sound.play("camera_flashing_2");
                }
                // Check variant sidebar class
                if($('#sidebar-left').hasClass('sidebar-box')){
                    $('#sidebar-left').attr('class','sidebar-box sidebar-'+classname);
                }
                else if($('#sidebar-left').hasClass('sidebar-rounded')){
                    $('#sidebar-left').attr('class','sidebar-rounded sidebar-'+classname);
                }
                else if($('#sidebar-left').hasClass('sidebar-circle')){
                    $('#sidebar-left').attr('class','sidebar-circle sidebar-'+classname);
                }
                else if($('#sidebar-left').attr('class') == ''){
                    $('#sidebar-left').attr('class','sidebar-'+classname);
                }
                // Set cookie theme name value to variable classname
                $.cookie('sidebar_color',classname, {expires: 1});
            });
        },

        // =========================================================================
        // LAYOUT SETTING
        // =========================================================================
        handleLayoutSetting: function () {
            // Check cookie for layout setting
            if ($.cookie('layout_setting')) {
                $('body').addClass($.cookie('layout_setting'));
            }

            // Check cookie for header layout setting
            if ($.cookie('header_layout_setting')) {
                $('body').addClass($.cookie('header_layout_setting'));
            }

            // Check cookie for sidebar layout setting
            if ($.cookie('sidebar_layout_setting')) {
                $('#sidebar-left').addClass($.cookie('sidebar_layout_setting'));
            }

            // Check cookie for sidebar type layout setting
            if ($.cookie('sidebar_type_setting')) {
                $('#sidebar-left').addClass($.cookie('sidebar_type_setting'));
            }

            // Check cookie for footer layout setting
            if ($.cookie('footer_layout_setting')) {
                $('body').addClass($.cookie('footer_layout_setting'));
            }

            // Check checked status input on layout setting
            if($('body').not('.page-boxed')){
                $('.layout-setting li:eq(0) input').attr('checked','checked');
            }
            if($('body').hasClass('page-boxed')){
                $('.layout-setting li:eq(1) input').attr('checked','checked');
                $('body').removeClass('page-header-fixed');
                $('body').removeClass('page-sidebar-fixed');
                $('body').removeClass('page-footer-fixed');
                $('.header-layout-setting li:eq(1) input').attr('disabled','disabled').next().css('text-decoration','line-through').parent('.rdio').attr({'data-toggle':'tooltip','data-container':'body','data-placement':'left','data-title':'Not working on page boxed'}).tooltip();
                $('.sidebar-layout-setting li:eq(1) input').attr('disabled','disabled').next().css('text-decoration','line-through').parent('.rdio').attr({'data-toggle':'tooltip','data-container':'body','data-placement':'left','data-title':'Not working on page boxed'}).tooltip();
                $('.footer-layout-setting li:eq(1) input').attr('disabled','disabled').next().css('text-decoration','line-through').parent('.rdio').attr({'data-toggle':'tooltip','data-container':'body','data-placement':'left','data-title':'Not working on page boxed'}).tooltip();
            }

            // Check checked status input on header layout setting
            if($('body').not('.page-header-fixed')){
                $('.header-layout-setting li:eq(0) input').attr('checked','checked');
            }
            if($('body').hasClass('page-header-fixed')){
                $('.header-layout-setting li:eq(1) input').attr('checked','checked');
            }

            // Check checked status input on sidebar layout setting
            if($('body').not('.page-sidebar-fixed')){
                $('.sidebar-layout-setting li:eq(0) input').attr('checked','checked');
            }
            if($('body').hasClass('page-sidebar-fixed')){
                $('.sidebar-layout-setting li:eq(1) input').attr('checked','checked');
            }

            // Check checked status input on sidebar type layout setting
            if($('#sidebar-left').not('.sidebar-box, .sidebar-rounded, .sidebar-circle')){
                $('.sidebar-type-setting li:eq(0) input').attr('checked','checked');
            }
            if($('#sidebar-left').hasClass('sidebar-box')){
                $('.sidebar-type-setting li:eq(1) input').attr('checked','checked');
            }
            if($('#sidebar-left').hasClass('sidebar-rounded')){
                $('.sidebar-type-setting li:eq(2) input').attr('checked','checked');
            }
            if($('#sidebar-left').hasClass('sidebar-circle')){
                $('.sidebar-type-setting li:eq(3) input').attr('checked','checked');
            }

            // Check checked status input on footer layout setting
            if($('body').not('.page-footer-fixed')){
                $('.footer-layout-setting li:eq(0) input').attr('checked','checked');
            }
            if($('body').hasClass('page-footer-fixed')){
                $('.footer-layout-setting li:eq(1) input').attr('checked','checked');
            }


            $('.layout-setting input').change(function(){

                // Create variable class name for layout setting
                var classname = $(this).val();

                // Add trigger change class on body HTML
                if($('body').hasClass('page-boxed')){
                    $('body').removeClass('page-boxed');
                    $('body').removeClass('page-header-fixed');
                    $('body').removeClass('page-sidebar-fixed');
                    $('body').removeClass('page-footer-fixed');
                    $('.header-layout-setting li:eq(1) input').removeAttr('disabled').next().css('text-decoration','inherit').parent('.rdio').tooltip('destroy');
                    $('.sidebar-layout-setting li:eq(1) input').removeAttr('disabled').next().css('text-decoration','inherit').parent('.rdio').tooltip('destroy');
                    $('.footer-layout-setting li:eq(1) input').removeAttr('disabled').next().css('text-decoration','inherit').parent('.rdio').tooltip('destroy');
                }else{
                    $('body').addClass($(this).val());
                    $('body').removeClass('page-header-fixed');
                    $('body').removeClass('page-sidebar-fixed');
                    $('body').removeClass('page-footer-fixed');
                    $('.header-layout-setting li:eq(1) input').attr('disabled','disabled').next().css('text-decoration','line-through').parent('.rdio').attr({'data-toggle':'tooltip','data-container':'body','data-placement':'left','data-title':'Not working on page boxed'}).tooltip();
                    $('.sidebar-layout-setting li:eq(1) input').attr('disabled','disabled').next().css('text-decoration','line-through').parent('.rdio').attr({'data-toggle':'tooltip','data-container':'body','data-placement':'left','data-title':'Not working on page boxed'}).tooltip();
                    $('.footer-layout-setting li:eq(1) input').attr('disabled','disabled').next().css('text-decoration','line-through').parent('.rdio').attr({'data-toggle':'tooltip','data-container':'body','data-placement':'left','data-title':'Not working on page boxed'}).tooltip();
                }

                // Set cookie theme name value to variable classname
                $.cookie('layout_setting',classname, {expires: 1});

            });

            $('.header-layout-setting input').change(function(){

                // Create variable class name for layout setting
                var classname = $(this).val();

                // Add trigger change class on body HTML
                if($('body').hasClass('page-header-fixed')){
                    $('body').removeClass('page-header-fixed');
                    $('body').addClass($(this).val());
                }

                $('body').addClass($(this).val());

                // Set cookie theme name value to variable classname
                $.cookie('header_setting',classname, {expires: 1});

            });

            $('.sidebar-layout-setting input').change(function(){

                // Create variable class name for layout setting
                var classname = $(this).val();

                // Add trigger change class on body HTML
                if($('body').hasClass('page-sidebar-fixed')){
                    $('body').removeClass('page-sidebar-fixed');
                    $('.header-layout-setting li:eq(0) input').removeAttr('disabled').next().css('text-decoration','inherit').parent('.rdio').tooltip('destroy');
                }else{
                    $('body').addClass($(this).val());
                    $('body').addClass('page-header-fixed');
                    $('.header-layout-setting li:eq(0) input').attr('disabled','disabled').next().css('text-decoration','line-through').parent('.rdio').attr({'data-toggle':'tooltip','data-container':'body','data-placement':'left','data-title':'Not working on sidebar fixed'}).tooltip();
                    $('.header-layout-setting li:eq(1) input').attr('checked','checked');
                }

                // Set cookie theme name value to variable classname
                $.cookie('sidebar_layout_setting',classname, {expires: 1});

            });

            $('.sidebar-type-setting input').change(function(){

                // Create variable class name for layout setting
                var classname = $(this).val();

                // Add trigger change class on sidebar left element
                if($('#sidebar-left').hasClass('sidebar-circle')){
                    $('#sidebar-left').removeClass('sidebar-circle');
                    $('#sidebar-left').addClass($(this).val());
                }

                if($('#sidebar-left').hasClass('sidebar-box')){
                    $('#sidebar-left').removeClass('sidebar-box');
                    $('#sidebar-left').addClass($(this).val());
                }

                if($('#sidebar-left').hasClass('sidebar-rounded')){
                    $('#sidebar-left').removeClass('sidebar-rounded');
                    $('#sidebar-left').addClass($(this).val());
                }

                $('#sidebar-left').addClass($(this).val());

                // Set cookie theme name value to variable classname
                $.cookie('sidebar_type_setting',classname, {expires: 1});

            });

            $('.footer-layout-setting input').change(function(){

                // Create variable class name for layout setting
                var classname = $(this).val();

                // Add trigger change class on body HTML
                if($('body').hasClass('page-footer-fixed')){
                    $('body').removeClass('page-footer-fixed')
                }else{
                    $('body').addClass($(this).val());
                }

                // Set cookie theme name value to variable classname
                $.cookie('footer_layout_setting',classname, {expires: 1});

            });
        }
        
    };

}();
var colorLayer = L.tileLayer('https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}.png');
var greyLayer = L.tileLayer.grayscale('https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}.png');
var overlayMaps = {};
var baseLayers = {};
myRenderer = L.canvas({ padding: 1.1 });
var dropCallIcon = L.icon({
    iconUrl: '/call_analyse/admin/assets/admin/images/dropcall.png',iconSize:[16, 16]
});
var blockCallIcon = L.icon({
    iconUrl: '/call_analyse/admin/assets/admin/images/blockcall.png',iconSize:[16, 16]
});

function loadDatePicker(setting) {
        
    if(setting == 'hour') {
        $('#dayyearmonth').show();
        $('#yearmonth').hide();  
        $('.input-daterange').hide();
    }
    else if(setting == 'day') {
        $('#yearmonth').show();
        $('#dayyearmonth').hide();
        $('.input-daterange').hide();
    }
    else if(setting == 'custom') {
        $('.input-daterange').show();
        $('#dayyearmonth').hide();
        $('#yearmonth').hide();
    }
}

$(document).ready(function(){
    var current_page = window.location.pathname.split('/').pop();
    
    showHideLegends();    
    loadDatePicker($('#trend_drop_down').val());
    
    $( ".sidebar-menu a" ).each(function() {
        if($(this).attr("href") == 'index.php' && current_page == '') {
            $( this ).addClass( "active" );
        }
        if($(this).attr("href") == current_page) {
            $( this ).parent().addClass( "active" );
        }
    });
    
    $('#trend_drop_down').change(function() {
        var type = $(this).val();
        if(type){
            loadDatePicker(type);
        }
    });

    $('#yearmonth').datepicker({
        format: 'yyyy-mm',
        viewMode: "months",
        minViewMode: "months",
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        todayHighlight: true,
        autoclose: true,
        orientation: 'bottom'
    });
    $('#dayyearmonth').datepicker({
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true
    });
        
    $('#start_dt').datepicker({
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true
    }).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        var newdate = new Date(minDate);
        newdate.setDate(minDate.getDate()+1);
        $('#end_dt').datepicker('setStartDate', newdate);
    });
    
    $('#end_dt').datepicker({
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true
    });
    
    $("input:checkbox[name='rf_list']").click(function() {
        $(this).not(this).prop('checked', false);
    });
    
    $("input:checkbox[name='rf_list']").on('click', function () {
        if ($(this).is(":checked")) {
            $("input:checkbox[name='rf_list']").prop("checked", false);
            $(this).prop("checked", true);
        } else {
            $(this).prop("checked", false);
        }
                
//        toggleMarkersGroup();
        toggleMarkersNew($(this).val(), $(this).prop("checked"));
    });
    
    $("input:checkbox[name='mute_call']").on('click', function () {
//        toggleMarkersGroup();
        toggleMarkersNew($(this).val(), $(this).prop("checked"));
    });
    
    $(document).on('click', '.leaflet-control-layers-selector', function () {
        var txt = $(this).next('span').text().trim();
        txt = txt.replace(" ", "_");
        
        if($(this).is(":checked")) {
            $("#legends_" + txt.toLowerCase()).show();
        } else {
            $("#legends_" + txt.toLowerCase()).hide();
        }
    });
    
    var onload_total_ajax_calls = 2;
    var onload_ajax_complete = 0;
    
    $.ajax({
        type:'POST',
        url:'ajax/save_frm_data_new.php',
        data: {type: 'get_city_details'},
        dataType:'json',
        beforeSend :function() {
//            jQuery('#txt_city').find("option:eq(0)").html("Please wait..");
        },
        success:function(jdata) {
            onload_ajax_complete++;
            
            if(jdata.status = 200)
            {
                var city_list = "<option value=''>City</option>";
                $.each(jdata.message, function(key, value) {
                    city_list += "<option value='"+value+"'>"+value+"</option>";
                });
                $('#city_drop_down').html(city_list);
                
                if(onload_ajax_complete == onload_total_ajax_calls) {
                    loadData();
                }
            }
        }
    });
    
    $.ajax({
        type:'POST',
        url:'ajax/save_frm_data_new.php',
        data: {type: 'op_drop_down', country: country},
        dataType:'json',
        beforeSend :function() {
//            jQuery('#txt_city').find("option:eq(0)").html("Please wait..");
        },
        success:function(jdata) {
            onload_ajax_complete++;
            
            if(jdata.status = 200)
            {
                var op_list = "<option value=''>Operator</option>";
                $.each(jdata.message, function(key, value) {
                    op_list += "<option value='"+value+"'>"+value+"</option>";
                });
                $('#op_drop_down').html(op_list);
                
                if(onload_ajax_complete == onload_total_ajax_calls) {
                    loadData();
                }
            }
        }
    });
        
    $('#btn_chart').on('click',function(){
        var btn = $(this);
        var btn_val = $(this).html();
        var city = $("#city_drop_down").val();
        var os = $("#os_drop_down").val();
        var op = $("#op_drop_down").val();
        var trend = $("#trend_drop_down").val();
        var start_dt, end_dt;
        if(trend == 'hour') {
            start_dt = end_dt = $("#dayyearmonth").val();
        } else if(trend == 'day') {
            var ym = $("#yearmonth").val();
            start_dt = ym + '-01';
            end_dt = ym + '-31';
        } else if(trend == 'custom') {
            start_dt = $("#start_dt").val();
            end_dt =$("#end_dt").val();
        } else {
            trend = 'hour';
        }
        
        var total_ajax_calls = 13;
        if(os.toUpperCase() == 'IOS') {
            total_ajax_calls = 12;
            $("#scatter_chart").css('display', 'none');
        } else {
            $("#scatter_chart").css('display', 'block');
        }
        
        filterUpdate();
        
        var total_ajax_complete = 0;
        inProgress(btn);
        
        clearMarkers();
        $("#call_connect").html('0 sec');
        $("#call_block_rate").html('0 %');
        $("#call_drop_rate").html('0 %');
        $("#mute_call_rate_uplink").html('-');
        $("#mute_call_rate_downlink").html('0 %');
        $("#kpi_call_attempt").html('0');
        $("#kpi_call_block").html('0');
        $("#kpi_call_established").html('0');
        $("#kpi_call_drop").html('0');
        $("#kpi_call_block_rate").html('0 %');
        $("#kpi_call_drop_rate").html('0 %');
        $("#kpi_call_setup_time").html('0');
        $("#kpi_mute_call_rate").html('<b>Uplink</b> - <b>Downlink</b> 12.1 %');
        $("#call_connect_chart").html('');
        $("#call_connect_min").html('0');
        $("#call_connect_max").html('0');
        $("#call_connect_avg").html('0');
        $("#call_connect_calls").html('0');        
        $("#call_connect_time_chart").html('');
        $("#block_call_rate_chart").html('');
        $("#drop_call_rate_chart").html('');
        $("#mute_call_rate_chart").html('');
        $("#mute_duration_chart").html('');
        $("#cov_qua_mute_chart").html('');
        $(".mcr_total_calls").html('0');
        $("#mcr_mute_calls_downlink").html('0');
        $("#mcr_min_sec_downlink").html('0');
        $("#mcr_max_sec_downlink").html('0');
        $("#mcr_avg_sec_downlink").html('0');
        $("#mcr_mute_calls_uplink").html('-');
        $("#mcr_min_sec_uplink").html('-');
        $("#mcr_max_sec_uplink").html('-');
        $("#mcr_avg_sec_uplink").html('-');
        $("#mute_duration_calls_uplink").html('-');
        $("#mute_duration_min_uplink").html('-');
        $("#mute_duration_max_uplink").html('-');
        $("#mute_duration_avg_uplink").html('-');
        $("#mute_duration_calls_downlink").html('0');
        $("#mute_duration_min_downlink").html('0');
        $("#mute_duration_max_downlink").html('0');
        $("#mute_duration_avg_downlink").html('0');
        $("#mute_call_duration_min").html('0');
        $("#mute_call_duration_max").html('0');
        $("#mute_call_duration_avg").html('0');
        $("#mute_call_duration_calls").html('0');
        
        markersGroupKPISINR = new L.layerGroup();
        markersGroupKPIRSRP = new L.layerGroup();
        markersGroupKPIRSRQ = new L.layerGroup();
        $.ajax({
            type:'POST',
            url:'ajax/save_frm_data_new.php',
            data: {type: 'get_summary_box_details', country: country, city: city, os: os, op: op, trend: trend, start_dt: start_dt, end_dt: end_dt},
            dataType:'json',
            beforeSend :function() {
                //inProgress(btn);
            },
            success:function(res) {
                total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
                    stopProgress(btn, btn_val);
                }
                if(res.status == 200) {
                    var call_connect = parseFloat(res.message['call_connect']);
                    if(call_connect >= 0 && call_connect <= 2) {
                        $(".bg-facebook").css('background-color', 'green');
                    } else if(call_connect > 2 && call_connect <= 5) {
                        $(".bg-facebook").css('background-color', '#ffe300');
                    } else if(call_connect > 5) {
                        $(".bg-facebook").css('background-color', 'red');
                    }
                    $(".bg-facebook").css('border', 'none');
                    
                    var call_block_rate = parseFloat(res.message['call_block_rate']);
                    if(call_block_rate < 2) {
                        $(".bg-twitter").css('background-color', 'green');
                    } else if(call_block_rate >= 2 && call_block_rate <= 5) {
                        $(".bg-twitter").css('background-color', '#ffe300');
                    } else if(call_block_rate > 5) {
                        $(".bg-twitter").css('background-color', 'red');
                    }
                    $(".bg-twitter").css('border', 'none');
                    
                    var call_drop_rate = parseFloat(res.message['call_drop_rate']);
                    if(call_drop_rate < 2) {
                        $(".bg-googleplus").css('background-color', 'green');
                    } else if(call_drop_rate >= 2 && call_drop_rate <= 5) {
                        $(".bg-googleplus").css('background-color', '#ffe300');
                    } else if(call_drop_rate > 5) {
                        $(".bg-googleplus").css('background-color', 'red');
                    }
                    $(".bg-googleplus").css('border', 'none');
                    
                    var mute_call_rate_downlink = parseFloat(res.message['mute_call_rate_downlink']);
                    var mute_call_rate_uplink = parseFloat(res.message['mute_call_rate_uplink']);
                    if(mute_call_rate_downlink < 1 && mute_call_rate_uplink < 1) {
                        $(".bg-bitbucket").css('background-color', 'green');
                    } else if(mute_call_rate_downlink >= 1 && mute_call_rate_downlink <= 3 && mute_call_rate_uplink >= 1 && mute_call_rate_uplink <= 3) {
                        $(".bg-bitbucket").css('background-color', '#ffe300');
                    } else if(mute_call_rate_downlink > 3 || mute_call_rate_uplink > 3) {
                        $(".bg-bitbucket").css('background-color', 'red');
                    }
                    $(".bg-bitbucket").css('border', 'none');
                    
                    $("#call_connect").html(res.message['call_connect']);
                    $("#call_drop_rate").html(res.message['call_drop_rate']);
                    $("#call_block_rate").html(res.message['call_block_rate']);                    
                    $("#mute_call_rate_uplink").html(res.message['mute_call_rate_uplink']);
                    $("#mute_call_rate_downlink").html(res.message['mute_call_rate_downlink']);
                }
            }
        });
        
        var legends_html = '<table class="table legends1" id="legends_calls" style="margin-bottom:0" cellpadding="1">';

        $.ajax({
            type:'POST',
            url:'ajax/get_map_details_new.php',
            data: {type: 'get_map_details', country: country, city: city, os: os, op: op, trend: trend, start_dt: start_dt, end_dt: end_dt},
            dataType:'json',
            beforeSend :function() {
            },
            success:function(res) {
                total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
                    stopProgress(btn, btn_val);
                }
                if(res.status == 200) {
                    if(city != '' && res.message['lat'] != '' & res.message['lng'] != '') {
                        mymap.setView([parseFloat(res.message['lat']), parseFloat(res.message['lng'])],12);                        
                    }
                    if(res.message['drop_locations']) {
                        legends_html += '<tr id="legends_call_drop"><td><img src="assets/admin/images/dropcall.png"><td>Call Drop</td></tr>';
                        loadMarkers(res.message['drop_locations'], '#008fd2', 'Call Drop');
                    }
                    if(res.message['block_locations']) {
                        legends_html += '<tr id="legends_call_block"><td><img src="assets/admin/images/blockcall.png"></td><td>Call Block</td></tr>';
                        loadMarkers(res.message['block_locations'], '#d80707', 'Call Block');
                    }
                    if(res.message['mute_locations_downlink']) {
                        legends_html += '<tr id="legends_mute_downlink"><td><input type="color" value="#04e199"></td><td>Mute Downlink</td></tr>';
                        loadMarkers(res.message['mute_locations_downlink'], '#04e199', 'Mute Downlink');
                    }
                    if(res.message['mute_locations_uplink']) {
                        legends_html += '<tr id="legends_mute_uplink"><td><input type="color" value="#da06f9"></td><td>Mute Uplink</td></tr>';
                        loadMarkers(res.message['mute_locations_uplink'], '#da06f9', 'Mute Uplink');
                    }
                    //$('#color_legend').append('<table class="table legends1" id="legends_calls" style="margin-bottom:0" cellpadding="1"><tr id="legends_call_drop"><td><input type="color" value="#008fd2"></td><td>Call Drop</td></tr><tr id="legends_call_block"><td><input type="color" value="#d80707"></td><td>Call Block</td></tr><tr id="legends_mute_uplink"><td><input type="color" value="#04e199"></td><td>Mute Uplink</td></tr><tr id="legends_mute_downlink"><td><input type="color" value="#da06f9"></td><td>Mute Downlink</td></tr></table>');
                    legends_html += '</table>';
                    $('#color_legend').append(legends_html);
                }
            }
        });
        
        $.ajax({
            type:'POST',
            url:'ajax/save_frm_data_new.php',
            data: {type: 'get_call_summary', country: country, city: city, os: os, op: op, trend: trend, start_dt: start_dt, end_dt: end_dt},
            dataType:'json',
            beforeSend :function() {
                //inProgress(btn);
            },
            success:function(res) {
                total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
                    stopProgress(btn, btn_val);
                }
                if(res.status == 200) {                    
                    $("#kpi_call_attempt").html(res.message['call_attempt']);
                    $("#kpi_call_block").html(res.message['call_block']);
                    $("#kpi_call_established").html(res.message['call_established']);                    
                    $("#kpi_call_drop").html(res.message['call_drop']);
                    $("#kpi_call_block_rate").html(res.message['call_block_rate']);
                    $("#kpi_call_drop_rate").html(res.message['call_drop_rate']);
                    $("#kpi_call_setup_time").html(res.message['call_setup_time']);
                    $("#kpi_mute_call_rate").html(res.message['mute_call_rate']);
                }
            }
        });
        
        $.ajax({
            type:'POST',
            url:'ajax/save_frm_data_new.php',
            data: {type: 'get_call_connect_trend_details', country: country, city: city, os: os, op: op, trend: trend, start_dt: start_dt, end_dt: end_dt},
            dataType:'json',
            beforeSend :function() {
            //inProgress(btn);
            },
            success:function(res) {
                total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
                    stopProgress(btn, btn_val);
                }
                if(res.status == 200) {                    
                    //$("#cc_samples").html(res.message['call_connect_samples']);
                    //$("#cc_min").html(res.message['min_call_connect']);
                    //$("#cc_max").html(res.message['max_call_connect']);
                    //$("#cc_avg").html(res.message['avg_call_connect']);
                    var gdata4 = $.map(res.message['call_connect_time_wise'], function(value, index) {
                        return [value];
                    });
                    loadCallConnectTime(gdata4);
                }
            }
        });
        
        $.ajax({
            type:'POST',
            url:'ajax/save_frm_data_new.php',
            data: {type: 'get_block_call_trend_details', country: country, city: city, os: os, op: op, trend: trend, start_dt: start_dt, end_dt: end_dt},
            dataType:'json',
            beforeSend :function() {
            //inProgress(btn);
            },
            success:function(res) {
                total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
                    stopProgress(btn, btn_val);
                }
                if(res.status == 200) {
                    //$("#bcr_total_calls").html(res.message['total_calls']);
                    //$("#bcr_block_calls").html(res.message['block_calls']);
                    var gdata5 = $.map(res.message['call_block_time_wise'], function(value, index) {
                        return [value];
                    });
                    loadCallBlockRate(gdata5);                    
                }
            }
        });
        
        $.ajax({
            type:'POST',
            url:'ajax/save_frm_data_new.php',
            data: {type: 'get_drop_call_trend_details', country: country, city: city, os: os, op: op, trend: trend, start_dt: start_dt, end_dt: end_dt},
            dataType:'json',
            beforeSend :function() {
                //inProgress(btn);
            },
            success:function(res) {
                total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
                    stopProgress(btn, btn_val);
                }
                if(res.status == 200) {
                    //$("#dcr_total_calls").html(res.message['total_calls']);
                    //$("#dcr_drop_calls").html(res.message['drop_calls']);
                    var gdata6 = $.map(res.message['call_drop_time_wise'], function(value, index) {
                        return [value];
                    });
                    loadCallDropRate(gdata6);
                }
            }
        });
        
        $.ajax({
            type:'POST',
            url:'ajax/save_frm_data_new.php',
            data: {type: 'get_mute_call_trend_details', country: country, city: city, os: os, op: op, trend: trend, start_dt: start_dt, end_dt: end_dt},
            dataType:'json',
            beforeSend :function() {
                //inProgress(btn);
            },
            success:function(res) {
                total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
                    stopProgress(btn, btn_val);
                }
                if(res.status == 200) {
                    $(".mcr_total_calls").html(res.message['total_calls']);
                    $("#mcr_mute_calls_uplink").html(res.message['mute_calls_uplink']);
                    $("#mcr_mute_calls_downlink").html(res.message['mute_calls_downlink']);
                    $("#mcr_min_sec_uplink").html(res.message['min_mute_sec_uplink']);
                    $("#mcr_max_sec_uplink").html(res.message['max_mute_sec_uplink']);
                    $("#mcr_avg_sec_uplink").html(res.message['avg_mute_sec_uplink']);
                    $("#mcr_min_sec_downlink").html(res.message['min_mute_sec_downlink']);
                    $("#mcr_max_sec_downlink").html(res.message['max_mute_sec_downlink']);
                    $("#mcr_avg_sec_downlink").html(res.message['avg_mute_sec_downlink']);
                    
                    var gdata = [];
                    gdata.push($.map(res.message['mute_calls_time_wise_downlink'], function(value, index) {
                        return [value];
                    }));
                    gdata.push($.map(res.message['mute_calls_time_wise_uplink'], function(value, index) {
                        return [value];
                    }));
                    loadMuteCallRate(gdata);
                }
            }
        });
        
        $.ajax({
            type:'POST',
            url:'ajax/save_frm_data_new.php',
            data: {type: 'get_mute_duration_details', country: country, city: city, os: os, op: op, trend: trend, start_dt: start_dt, end_dt: end_dt},
            dataType:'json',
            beforeSend :function() {
                //inProgress(btn);
            },
            success:function(res) {
                total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
                    stopProgress(btn, btn_val);
                }
                if(res.status == 200) {
                    $("#mute_duration_min_downlink").html(res.message['min_mute_duration_downlink']);
                    $("#mute_duration_max_downlink").html(res.message['max_mute_duration_downlink']);
                    $("#mute_duration_avg_downlink").html(res.message['avg_mute_duration_downlink']);
                    $("#mute_duration_calls_downlink").html(res.message['mute_calls_downlink']);                    
                    $("#mute_duration_min_uplink").html(res.message['min_mute_duration_uplink']);
                    $("#mute_duration_max_uplink").html(res.message['max_mute_duration_uplink']);
                    $("#mute_duration_avg_uplink").html(res.message['avg_mute_duration_uplink']);
                    $("#mute_duration_calls_uplink").html(res.message['mute_calls_uplink']);
                    
                    var gdata1 = [];
                    gdata1.push($.map(res.message['mute_duration_histogram_downlink'], function(value, index) {
                        return [value];
                    }));
                    gdata1.push($.map(res.message['mute_duration_histogram_uplink'], function(value, index) {
                        return [value];
                    }));
                    loadMuteDuration(gdata1);
                }
            }
        });
        
        if(os.toUpperCase() != 'IOS') {
            $.ajax({
                type:'POST',
                url:'ajax/save_frm_data_new.php',
                data: {type: 'get_cov_qua_mute_details', country: country, city: city, os: os, op: op, trend: trend, start_dt: start_dt, end_dt: end_dt},
                dataType:'json',
                beforeSend :function() {
                    //inProgress(btn);
                },
                success:function(res) {
                    total_ajax_complete++;
                    if(total_ajax_complete == total_ajax_calls) {
                        stopProgress(btn, btn_val);
                    }
                    if(res.status == 200) {
                        $("#mute_call_duration_calls").html(res.message['mute_samples']);
                        $("#mute_call_duration_min").html(res.message['min_mute_duration_cov']);
                        $("#mute_call_duration_max").html(res.message['max_mute_duration_cov']);
                        $("#mute_call_duration_avg").html(res.message['avg_mute_duration_cov']);

                        $("#scatter_chart").css('display', 'block');
                        var gdata2 = $.map(res.message['cov_qua_mute'], function(value, index) {
                            return [value];
                        });
                        loadCoverageQualityMute(gdata2);
                    }
                }
            });
        }
        
        $.ajax({
            type:'POST',
            url:'ajax/save_frm_data_new.php',
            data: {type: 'get_call_connect_histogram_details', country: country, city: city, os: os, op: op, trend: trend, start_dt: start_dt, end_dt: end_dt},
            dataType:'json',
            beforeSend :function() {
            },
            success:function(res) {
                total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
                    stopProgress(btn, btn_val);
                }
                if(res.status == 200) {
                    $("#call_connect_min").html(res.message['min_call_connect']);
                    $("#call_connect_max").html(res.message['max_call_connect']);
                    $("#call_connect_avg").html(res.message['avg_call_connect']);
                    $("#call_connect_calls").html(res.message['call_connect_samples']);
                    
                    var gdata3 = $.map(res.message['call_connect_histogram'], function(value, index) {
                        return [value];
                    });
                    loadCallConnect(gdata3);
                }
            }
        });
        
        $.ajax({
            type:'POST',
            url:'ajax/get_rsrp_new.php',
            data: {type: 'get_network_details_rsrp', country: country, city: city, os: os, op: op, trend: trend, start_dt: start_dt, end_dt: end_dt},
            dataType:'text',
            beforeSend :function() {
                //inMapProgress(true,"Plotting data, Please Wait...");
            },
            complete:function(){
                //inMapProgress(false,"");
            },
            success:function(res) {
                total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
                    stopProgress(btn, btn_val);
                }
                    
                var lines = res.split('\n');
                var rsrp_locations_total_count = lines.length;
                console.log('rsrp-'+rsrp_locations_total_count);
                var marker_point;
                var rsrp_locations = [[], [], [], [], []];
                var rsrp_locations_count = [[], [], [], [], []];
                var rsrp_locations_percentage = [[], [], [], [], []];
                var rsrp_range_colors = ['#ee2020', '#3f3fe3', '#f7f720', '#6fea6f', '#0cd00c'];
                $('.leaflet-control-layers').remove();
                
                overlayMaps['RSRP'] = markersGroupKPIRSRP;

                for(var line = 0; line < lines.length; line++) {
                    marker_point = lines[line].split(',');
                    
                    if(marker_point[2] < -110) {
                        new L.circleMarker([marker_point[0],marker_point[1]], {color:rsrp_range_colors[0],stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPIRSRP);
                        rsrp_locations[0].push(marker_point);
                    } else if(marker_point[2] >= -110 && marker_point[2] < -100) {
                        new L.circleMarker([marker_point[0],marker_point[1]], {color:rsrp_range_colors[1],stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPIRSRP);
                        rsrp_locations[1].push(marker_point);
                    } else if(marker_point[2] >= -100 && marker_point[2] < -90) {
                        new L.circleMarker([marker_point[0],marker_point[1]], {color:rsrp_range_colors[2],stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPIRSRP);
                        rsrp_locations[2].push(marker_point);
                    } else if(marker_point[2] >= -90 && marker_point[2] < -80) {
                        new L.circleMarker([marker_point[0],marker_point[1]], {color:rsrp_range_colors[3],stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPIRSRP);
                        rsrp_locations[3].push(marker_point);
                    } else if(marker_point[2] >= -80) {
                        new L.circleMarker([marker_point[0],marker_point[1]], {color:rsrp_range_colors[4],stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPIRSRP);
                        rsrp_locations[4].push(marker_point);
                    }
                    
                }
                var layersControl = new L.Control.Layers(baseLayers, overlayMaps,{position: 'topright',collapsed: false});
                mymap.addControl(layersControl);

                for(var i = 0; i < 5; i++) {
                    if(rsrp_locations[i] == undefined) {
                        rsrp_locations[i] = [];
                    }
                    rsrp_locations_count[i] = rsrp_locations[i].length;
                    rsrp_locations_percentage[i] = parseFloat((rsrp_locations_count[i]/rsrp_locations_total_count) * 100).toFixed(1);
                }
                
                $('#color_legend').append('<table class="table legends1" id="legends_rsrp" style="margin-bottom:0" cellpadding="1"><tr><th colspan="2">RSRP</th></tr><tr><td><input type="color" value='+rsrp_range_colors[0]+'></td><td>< -110 ('+rsrp_locations_percentage[0]+' %)</td></tr><tr><td><input type="color" value='+rsrp_range_colors[1]+'></td><td>>= -110 to < -100 ('+rsrp_locations_percentage[1]+' %)</td></tr><tr><td><input type="color" value='+rsrp_range_colors[2]+'></td><td>>= -100 to < -90 ('+rsrp_locations_percentage[2]+' %)</td></tr><tr><td><input type="color" value='+rsrp_range_colors[3]+'></td><td>>= -90 to < -80 ('+rsrp_locations_percentage[3]+' %)</td></tr><tr><td><input type="color" value='+rsrp_range_colors[4]+'></td><td>>= -80 ('+rsrp_locations_percentage[4]+' %)</td></tr></table>');
                $('#legends_rsrp').hide();

                // $('.leaflet-control-layers').remove();
                // var lines = res.split('\n');
                
                // for(var line = 0; line < lines.length; line++) {

                //     marker_point = lines[line].split(',');
                //     new L.circleMarker(marker_point, {color:'#EE3A8C',stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPI);;
                // }
                // overlayMaps['RSRP'] = markersGroupKPI;
                // var layersControl = new L.Control.Layers(baseLayers, overlayMaps,{position: 'topright',collapsed: false});
                // mymap.addControl(layersControl);
            }
        });

        $.ajax({
            type:'POST',
            url:'ajax/get_rsrq_new.php',
            data: {type: 'get_network_details_rsrq', country: country, city: city, os: os, op: op, trend: trend, start_dt: start_dt, end_dt: end_dt},
            dataType:'text',
            beforeSend :function() {
                //inMapProgress(true,"Plotting data, Please Wait...");
            },
            complete:function(){
                //inMapProgress(false,"");
            },
            success:function(res) {
                total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
                    stopProgress(btn, btn_val);
                }
                $('.leaflet-control-layers').remove();

                var lines = res.split('\n');
                var rsrp_locations_total_count = lines.length;
                console.log('rsrq-'+rsrp_locations_total_count);
                var marker_point;
                var rsrp_locations = [[], [], [], [], []];
                var rsrp_locations_count = [[], [], [], [], []];
                var rsrp_locations_percentage = [[], [], [], [], []];
                
                var rsrp_range_colors = ['#ee2020', '#3f3fe3', '#f7f720', '#6fea6f', '#0cd00c'];
                
                overlayMaps['RSRQ'] = markersGroupKPIRSRQ;

                for(var line = 0; line < lines.length; line++) {
                    marker_point = lines[line].split(',');
                    
                    if(marker_point[2] < -20) {
                        new L.circleMarker([marker_point[0],marker_point[1]], {color:rsrp_range_colors[0],stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPIRSRQ);
                        rsrp_locations[0].push(marker_point);
                    } else if(marker_point[2] >= -20 && marker_point[2] < -17) {
                        new L.circleMarker([marker_point[0],marker_point[1]], {color:rsrp_range_colors[1],stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPIRSRQ);
                        rsrp_locations[1].push(marker_point);
                    } else if(marker_point[2] >= -17 && marker_point[2] < -13) {
                        new L.circleMarker([marker_point[0],marker_point[1]], {color:rsrp_range_colors[2],stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPIRSRQ);
                        rsrp_locations[2].push(marker_point);
                    } else if(marker_point[2] >= -13 && marker_point[2] < -9) {
                        new L.circleMarker([marker_point[0],marker_point[1]], {color:rsrp_range_colors[3],stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPIRSRQ);
                        rsrp_locations[3].push(marker_point);
                    } else if(marker_point[2] >= -9) {
                        new L.circleMarker([marker_point[0],marker_point[1]], {color:rsrp_range_colors[4],stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPIRSRQ);
                        rsrp_locations[4].push(marker_point);
                    }
                    
                }
                var layersControl = new L.Control.Layers(baseLayers, overlayMaps,{position: 'topright',collapsed: false});
                mymap.addControl(layersControl);

                for(var i = 0; i < 5; i++) {
                    if(rsrp_locations[i] == undefined) {
                        rsrp_locations[i] = [];
                    }
                    rsrp_locations_count[i] = rsrp_locations[i].length;
                    rsrp_locations_percentage[i] = parseFloat((rsrp_locations_count[i]/rsrp_locations_total_count) * 100).toFixed(1);
                }
                
                $('#color_legend').append('<table class="table legends1" id="legends_rsrq" style="margin-bottom:0" cellpadding="1"><tr><th colspan="2">RSRQ</th></tr><tr><td><input type="color" value='+rsrp_range_colors[0]+'></td><td>< -20 ('+rsrp_locations_percentage[0]+' %)</td></tr><tr><td><input type="color" value='+rsrp_range_colors[1]+'></td><td>>= -20 to < -17 ('+rsrp_locations_percentage[1]+' %)</td></tr><tr><td><input type="color" value='+rsrp_range_colors[2]+'></td><td>>= -17 to < -13 ('+rsrp_locations_percentage[2]+' %)</td></tr><tr><td><input type="color" value='+rsrp_range_colors[3]+'></td><td>>= -13 to < -9 ('+rsrp_locations_percentage[3]+' %)</td></tr><tr><td><input type="color" value='+rsrp_range_colors[4]+'></td><td>>= -9 ('+rsrp_locations_percentage[4]+' %)</td></tr></table>');
                $('#legends_rsrq').hide();

                // var lines = res.split('\n');
                // for(var line = 0; line < lines.length; line++) {

                //     marker_point = lines[line].split(',');
                //     new L.circleMarker(marker_point, {color:'#dda0dd',stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPI);;
                // }
                // overlayMaps['RSRQ'] = markersGroupKPI;
                // var layersControl = new L.Control.Layers(baseLayers, overlayMaps,{position: 'topright',collapsed: false});
                // mymap.addControl(layersControl);
            }
        });

        $.ajax({
            type:'POST',
            url:'ajax/get_sinr_new.php',
            data: {type: 'get_network_details_sinr', country: country, city: city, os: os, op: op, trend: trend, start_dt: start_dt, end_dt: end_dt},
            dataType:'text',
            beforeSend :function() {
                //inMapProgress(true,"Plotting data, Please Wait...");
            },
            complete:function(){
                //inMapProgress(false,"");
            },
            success:function(res) {
                total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
                    stopProgress(btn, btn_val);
                }
                var lines = res.split('\n');
                var rsrp_locations_total_count = lines.length;
                console.log('sinr-'+rsrp_locations_total_count);
                var marker_point;
                var rsrp_locations = [[], [], [], [], []];
                var rsrp_locations_count = [[], [], [], [], []];
                var rsrp_locations_percentage = [[], [], [], [], []];
                
                var rsrp_range_colors = ['#ee2020', '#f7a002', '#3f3fe3', '#f7f720', '#6fea6f', '#0cd00c'];

                $('.leaflet-control-layers').remove();
                
                overlayMaps['SINR'] = markersGroupKPISINR;

                for(var line = 0; line < lines.length; line++) {
                    marker_point = lines[line].split(',');
                    
                    if(marker_point[2] < 0) {
                        new L.circleMarker([marker_point[0],marker_point[1]], {color:rsrp_range_colors[0],stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPISINR);
                        rsrp_locations[0].push(marker_point);
                    } else if(marker_point[2] >= 0 && marker_point[2] < 5) {
                        new L.circleMarker([marker_point[0],marker_point[1]], {color:rsrp_range_colors[1],stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPISINR);
                        rsrp_locations[1].push(marker_point);
                    } else if(marker_point[2] >= 5 && marker_point[2] < 10) {
                        new L.circleMarker([marker_point[0],marker_point[1]], {color:rsrp_range_colors[2],stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPISINR);
                        rsrp_locations[2].push(marker_point);
                    } else if(marker_point[2] >= 10 && marker_point[2] < 15) {
                        new L.circleMarker([marker_point[0],marker_point[1]], {color:rsrp_range_colors[3],stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPISINR);
                        rsrp_locations[3].push(marker_point);
                    } else if(marker_point[2] >= 15 && marker_point[2] < 20) {
                        new L.circleMarker([marker_point[0],marker_point[1]], {color:rsrp_range_colors[4],stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPISINR);
                        rsrp_locations[4].push(marker_point);
                    } else if(marker_point[2] >= 20) {
                        new L.circleMarker([marker_point[0],marker_point[1]], {color:rsrp_range_colors[5],stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPISINR);
                        rsrp_locations[4].push(marker_point);
                    }
                   
                }
                var layersControl = new L.Control.Layers(baseLayers, overlayMaps,{position: 'topright',collapsed: false});
                mymap.addControl(layersControl);

                for(var i = 0; i < 6; i++) {
                    if(rsrp_locations[i] == undefined) {
                        rsrp_locations[i] = [];
                    }
                    rsrp_locations_count[i] = rsrp_locations[i].length;
                    rsrp_locations_percentage[i] = parseFloat((rsrp_locations_count[i]/rsrp_locations_total_count) * 100).toFixed(1);
                }
                
                $('#color_legend').append('<table class="table legends1" id="legends_sinr" style="margin-bottom:0" cellpadding="1"><tr><th colspan="2">SINR</th></tr><tr><td><input type="color" value='+rsrp_range_colors[0]+'></td><td>< 0 ('+rsrp_locations_percentage[0]+' %)</td></tr><tr><td><input type="color" value='+rsrp_range_colors[1]+'></td><td>>= 0 to < 5 ('+rsrp_locations_percentage[1]+' %)</td></tr><tr><td><input type="color" value='+rsrp_range_colors[2]+'></td><td>>= 5 to < 10 ('+rsrp_locations_percentage[2]+' %)</td></tr><tr><td><input type="color" value='+rsrp_range_colors[3]+'></td><td>>= 10 to < 15 ('+rsrp_locations_percentage[3]+' %)</td></tr><tr><td><input type="color" value='+rsrp_range_colors[4]+'></td><td>>= 15 to < 20 ('+rsrp_locations_percentage[4]+' %)</td></tr><tr><td><input type="color" value='+rsrp_range_colors[5]+'></td><td>>= 20 ('+rsrp_locations_percentage[5]+' %)</td></tr></table>');
                $('#legends_sinr').hide();

                // $('.leaflet-control-layers').remove();
                // var lines = res.split('\n');
                // for(var line = 0; line < lines.length; line++) {

                //     marker_point = lines[line].split(',');
                //     new L.circleMarker(marker_point, {color:'#483D8B',stroke:false,radius: 2,fillOpacity:1,renderer: myRenderer}).addTo(markersGroupKPI);;
                // }
                // overlayMaps['SINR'] = markersGroupKPI;
                // var layersControl = new L.Control.Layers(baseLayers, overlayMaps,{position: 'topright',collapsed: false});
                // mymap.addControl(layersControl);
            }
        });
    });
        
    
    /*$('#btn_map').on('click',function(){
        var btn = $(this);
        var btn_val = $(this).html();
        var city = $("#city_drop_down").val();
        var os = $("#os_drop_down").val();
        var op = $("#op_drop_down").val();
        var trend = $("#trend_drop_down").val();
        var start_dt, end_dt;
        if(trend == 'hour') {
            start_dt = end_dt = $("#dayyearmonth").val();
        } else if(trend == 'day') {
            var ym = $("#yearmonth").val();
            start_dt = ym + '-01';
            end_dt = ym + '-31';
        }
        
        if(os.toUpperCase() == 'IOS') {
//            initAutocomplete();
            alert('Network details for iOS devices are not available');
            return false;
        }
             
        $.ajax({
            type:'POST',
            url:'ajax/save_frm_data_new.php',
            data: {type: 'get_network_details', country: country, city: city, os: os, op: op, trend: trend, start_dt: start_dt, end_dt: end_dt},
            dataType:'json',
            beforeSend :function() {
                inProgress(btn);
            },
            success:function(res) {
                stopProgress(btn, btn_val);
                if(res.status == 200) {
                    if(city != '' && res.message['lat'] != '' & res.message['lng'] != '') {
                        var myLatLng = new google.maps.LatLng(res.message['lat'], res.message['lng']);
                        var bounds = new google.maps.LatLngBounds();
                        map.setCenter(myLatLng);
                        bounds.extend(myLatLng);
                        map.setCenter(bounds.getCenter());
                        map.fitBounds(bounds);
                        map.setZoom(12);
                    }
                                        
                    clearMarkers();
                    if(res.message['rsrp_locations']) {
                        var rsrp_range_colors = ['#ee2020', '#3f3fe3', '#f7f720', '#6fea6f', '#0cd00c'];
                        $.each(res.message['rsrp_locations'], function( index, value ) {
                            loadMarkersGroup(value, rsrp_range_colors[index], 'rsrp');
                        });
                    }
                    
                    if(res.message['rsrq_locations']) {
                        var rsrq_range_colors = ['#ee2020', '#3f3fe3', '#f7f720', '#6fea6f', '#0cd00c'];
                        $.each(res.message['rsrq_locations'], function( index, value ) {
                            loadMarkersGroup(value, rsrq_range_colors[index], 'rsrq');
                        });
                    }
                    
                    if(res.message['sinr_locations']) {
                        var sinr_range_colors = ['#ee2020', '#f7a002', '#3f3fe3', '#f7f720', '#6fea6f', '#0cd00c'];
                        $.each(res.message['sinr_locations'], function( index, value ) {
                            loadMarkersGroup(value, sinr_range_colors[index], 'sinr');
                        });
                    }
                    
//                    if(res.message['mute_locations']) {
//                        var mute_range_colors = ['#81B622', '#FAD02C', '#0476D0', '#FF8300', '#F83839'];
//                        $.each(res.message['mute_locations'], function( index, value ) {
//                            loadMarkersGroup(value, mute_range_colors[index], 'mute');
//                        });
//                    }

                    if(res.message['mute_locations_downlink']) {
                        loadMarkersGroup(res.message['mute_locations_downlink'], '#04e199', 'mute_downlink');
                    }
                    
                    if(res.message['mute_locations_uplink']) {
                        loadMarkersGroup(res.message['mute_locations_uplink'], '#da06f9', 'mute_uplink');
                    }
                    
                    for (var i = 0; i < legend_types.length; i++) {
                        if(res.message[legend_types[i] + '_locations_count']) {
                            $.each(res.message[legend_types[i] + '_locations_count'], function( index, value ) {
                                $("#" + legend_types[i] + "_count_" + (index+1)).html(value);
                            });
                        }

                        if(res.message[legend_types[i] + '_locations_percentage']) {
                            $.each(res.message[legend_types[i] + '_locations_percentage'], function( index, value ) {
                                $("#" + legend_types[i] + "_percentage_" + (index+1)).html(value);
                            });
                        }
                    }
                    
                    toggleMarkersGroup();
                }
            }
        });
    });*/
    
    $('#btn_map').on('click',function(){
        var btn = $(this);
        var btn_val = $(this).html();
        var city = $("#city_drop_down").val();
        var os = $("#os_drop_down").val();
        var op = $("#op_drop_down").val();
        var trend = $("#trend_drop_down").val();
        var start_dt, end_dt;
        if(trend == 'hour') {
            start_dt = end_dt = $("#dayyearmonth").val();
        } else if(trend == 'day') {
            var ym = $("#yearmonth").val();
            start_dt = ym + '-01';
            end_dt = ym + '-31';
        }
        
        if(os.toUpperCase() == 'IOS') {
//            initAutocomplete();
            alert('Network details for iOS devices are not available');
            return false;
        }
        
        filterUpdate();
        
        var total_ajax_calls = 2;
        var total_ajax_complete = 0;
        inProgress(btn);
        clearMarkers();
        $("#drop_block_rf_tbl").html('');
        $("#mute_rf_tbl").html('');
        
        $.ajax({
            type:'POST',
            url:'ajax/save_frm_data_new.php',
            data: {type: 'get_rf_details_drop_block', country: country, city: city, os: os, op: op, trend: trend, start_dt: start_dt, end_dt: end_dt},
            dataType:'json',
            beforeSend :function() {
//                inProgress(btn);
            },
            success:function(res) {
                total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
//                    toggleMarkersGroup();
                    stopProgress(btn, btn_val);
                }
                if(res.status == 200) {
                    var rf_det = res.message['rf_details'];
                    if(rf_det) {
                        var tbl_str = "<thead><tr>";
                        tbl_str += "<th>Sr. No.</th>";
                        tbl_str += "<th>DateTime</th>";
                        tbl_str += "<th>Event</th>";
                        tbl_str += "<th>RSRP</th>";
                        tbl_str += "<th>RSRQ</th>";
                        tbl_str += "<th>SINR</th>";
                        tbl_str += "<th>PCI</th>";
                        tbl_str += "<th>Cell ID</th>";
                        tbl_str += "</tr></thead>";
                        
                        var event;
                        var i = 1;
                        $.each(rf_det, function(idx, val){
                            if(val['drop'] == 1) {
                                event = 'Drop';
                            } else if(val['block'] == 1) {
                                event = 'Block';
                            }
                            tbl_str += "<tr>";
                            tbl_str += "<td>" + i + "</td>";
                            tbl_str += "<td>" + val['ts'] + "</td>";
                            tbl_str += "<td>" + event + "</td>";
                            tbl_str += "<td>" + val['rsrp'] + "</td>";
                            tbl_str += "<td>" + val['rsrq'] + "</td>";
                            tbl_str += "<td>" + val['sinr'] + "</td>";
                            tbl_str += "<td>" + val['pci'] + "</td>";
                            tbl_str += "<td>" + val['cell_id'] + "</td>";
                            tbl_str += "</tr>";
                            i++;
                        });
                        $("#drop_block_rf_tbl").html(tbl_str);
                        
                        $('#drop_block_rf_tbl').DataTable().destroy();
                        $('#drop_block_rf_tbl').DataTable({
                            "paging":   true,
                            "ordering": true,
                            "info":     true,
                            "order": [[ 1, "asc" ]]
                        });
                    }
                }
            }
        });
        
        $.ajax({
            type:'POST',
            url:'ajax/save_frm_data_new.php',
            data: {type: 'get_rf_details_mute', country: country, city: city, os: os, op: op, trend: trend, start_dt: start_dt, end_dt: end_dt},
            dataType:'json',
            beforeSend :function() {
//                inProgress(btn);
            },
            success:function(res) {
                total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
//                    toggleMarkersGroup();
                    stopProgress(btn, btn_val);
                }
                if(res.status == 200) {
                    var rf_det = res.message['rf_details'];
                    var voice_file_ids = res.message['voice_file_ids'];
                    if(rf_det) {
                        var tbl_str = "<thead><tr>";
                        tbl_str += "<th>Sr. No.</th>";
                        tbl_str += "<th>DateTime</th>";
                        tbl_str += "<th>Event</th>";
                        tbl_str += "<th>Mute Duration(sec)</th>";
                        tbl_str += "<th>RSRP</th>";
                        tbl_str += "<th>RSRQ</th>";
                        tbl_str += "<th>SINR</th>";
                        tbl_str += "<th>PCI</th>";
                        tbl_str += "<th>Cell ID</th>";
                        tbl_str += "<th>Mute On(sec)</th>";
                        tbl_str += "<th>Play</th>";
                        tbl_str += "</tr></thead>";
                        
                        var event, play;
                        var i = 1;
                        $.each(rf_det, function(idx, val){
                            if(val['drop'] == 1) {
                                event = 'Drop';
                            } else if(val['block'] == 1) {
                                event = 'Block';
                            }
                            play = "NA";
                            if(voice_file_ids[val['call_no']] != undefined) {
                                play = "<a href='" + voice_file_ids[val['call_no']] + "'>Play</a>";
                            }
                            tbl_str += "<tr>";
                            tbl_str += "<td>" + i + "</td>";
                            tbl_str += "<td>" + val['ts'] + "</td>";
                            tbl_str += "<td>Mute</td>";
                            tbl_str += "<td>" + val['mute_duration'] + "</td>";
                            tbl_str += "<td>" + val['rsrp'] + "</td>";
                            tbl_str += "<td>" + val['rsrq'] + "</td>";
                            tbl_str += "<td>" + val['sinr'] + "</td>";
                            tbl_str += "<td>" + val['pci'] + "</td>";
                            tbl_str += "<td>" + val['cell_id'] + "</td>";
                            tbl_str += "<td>" + val['mute_on'] + "</td>";
                            tbl_str += "<td>" + play + "</td>";
                            tbl_str += "</tr>";
                            i++;
                        });
                        $("#mute_rf_tbl").html(tbl_str);
                        
                        $('#mute_rf_tbl').DataTable().destroy();
                        $('#mute_rf_tbl').DataTable({
                            "paging":   true,
                            "ordering": true,
                            "info":     true,
                            "order": [[ 1, "asc" ]]
                        });
                    }
                }
            }
        });
    });

    $('#btn_export').on('click',function(){
        var btn = $(this);
        var btn_val = $(this).html();
        var city = $("#city_drop_down").val();
        var os = $("#os_drop_down").val();
        var op = $("#op_drop_down").val();
        var trend = $("#trend_drop_down").val();
        var start_dt, end_dt;

        if(trend == 'hour') {
            start_dt = end_dt = $("#dayyearmonth").val();
        } else if(trend == 'day') {
            var ym = $("#yearmonth").val();
            start_dt = ym + '-01';
            end_dt = ym + '-31';
        }
        else if(trend == 'custom') {
            start_dt = $("#start_dt").val();
            end_dt =$("#end_dt").val();
        } else {
            trend = 'hour';
        }
        console.log("city"+city+"os"+os+"op"+op+"trend"+trend+"start_dt"+start_dt+"end_dt"+end_dt);
        filterUpdate(); 
        var form = $('<form name="export_form" method="post" action="ajax/save_frm_data_new.php"></form>');        
        form.append('<input type="text" name="type" value="export_csv_data">');
        form.append('<input type="text" name="country" value="'+country+'">');
        form.append('<input type="text" name="city" value="'+city+'">');
        form.append('<input type="text" name="os" value="'+os+'">');
        form.append('<input type="text" name="op" value="'+op+'">');
        form.append('<input type="text" name="trend" value="'+trend+'">');
        form.append('<input type="text" name="start_dt" value="'+start_dt+'">');
        form.append('<input type="text" name="end_dt" value="'+end_dt+'">');
        $(form).appendTo('body').submit();
    });
    
    $(document).on('click', '#call_drop_event, #drop',function(){        
        var checkBoxes = $("#drop");
        (checkBoxes.prop("checked")==true)?checkBoxes.prop("checked", false):checkBoxes.prop("checked", true);
        toggleMarkers('drop');
    });
    
    $(document).on('click', '#call_block_event, #block',function(){
        var checkBoxes = $("#block");
        (checkBoxes.prop("checked")==true)?checkBoxes.prop("checked", false):checkBoxes.prop("checked", true);
        toggleMarkers('block');
    });
    
    $(document).on('click', '#call_mute_event, #mute',function(){
        var checkBoxes = $("#mute");
        (checkBoxes.prop("checked")==true)?checkBoxes.prop("checked", false):checkBoxes.prop("checked", true);
        toggleMarkers('mute');
    });
});

function loadData() {
    if($('#btn_chart').length) {
        filterUpdate('btn_chart');
    }
    if($('#btn_map').length) {
        filterUpdate('btn_map');
    }
}

function filterUpdate(btn_val=false) {
    var city = $("#city_drop_down").val();
    var os = $("#os_drop_down").val();
    var op = $("#op_drop_down").val();
    var trend = $("#trend_drop_down").val();
    var start_dt, end_dt, ym;
    if(trend == 'hour') {
        start_dt = end_dt = $("#dayyearmonth").val();
    } else if(trend == 'day') {
        var ym = $("#yearmonth").val();
    } else if(trend == 'custom') {
        start_dt = $("#start_dt").val();
        end_dt = $("#end_dt").val();
    }

    $.ajax('ajax/filter_update.php', {
        type: 'POST',  // http method
        dataType: 'json',
        data: {country: country, city: city, os: os, op: op, trend: trend, ym: ym, start_dt: start_dt, end_dt: end_dt},
        success: function (res) {                    
            if(res.status == 200) {
                $("#city_drop_down").val(res.msg['city']);
                $("#os_drop_down").val(res.msg['os']);
                $("#op_drop_down").val(res.msg['op']);
                $("#trend_drop_down").val(res.msg['trend']);
                loadDatePicker(res.msg['trend']);
                if(res.msg['trend'] == 'hour') {
                    $("#dayyearmonth").val(res.msg['start_dt']);
                } else if(res.msg['trend'] == 'day') {
                    $("#yearmonth").val(res.msg['ym']);
                } else if(res.msg['trend'] == 'custom') {
                    $("#start_dt").val(res.msg['start_dt']);
                    $("#end_dt").val(res.msg['end_dt']);
                }
                if(btn_val) {
                    $('#'+btn_val).trigger("click");
                }
            }
        },
        error: function (jqXhr, textStatus, errorMessage) {
            console.log(errorMessage);
        }
    });
}

function inProgress(elem) {
    $('#plotingProgress').show();
    $(elem).html("Please wait..");
//    $(elem).disabled = true;
    $(elem).attr("disabled", "disabled");
}

function stopProgress(elem, display_value) {
    $(elem).html(display_value);
//    $(elem).disabled = false;
    $(elem).removeAttr("disabled");
    $('#plotingProgress').hide();
}

function toggleMarkers(type) {
    if (document.getElementById(type).checked == false) { // hide the marker
        for (var i = 0; i < markers.length; i++) {
            if (markers[i].call_type == type) {
                markers[i].setMap(null);
            }
        }
    } else { // show the marker again
        for (var i = 0; i < markers.length; i++) {
            if (markers[i].call_type == type) {
                markers[i].setMap(map);
            }
        }
    }
}

function loadMarkers(locations, markerColor, type) {
    var iconURL = false;
    if(type == 'Call Drop') {
        iconURL = dropCallIcon;
    } else if(type == 'Call Block') {
        iconURL = blockCallIcon;
    }
    $('.leaflet-control-layers').remove();
    markersGroup = new L.layerGroup();
    $.each(locations, function(i, item) {
        if(iconURL) {
            new L.marker([item['lat'],item['lon']], {icon : iconURL}).addTo(markersGroup);
        } else {
            new L.circleMarker([item['lat'],item['lon']], {color:markerColor,stroke:false,radius: 4,fillOpacity:1,renderer: myRenderer}).addTo(markersGroup);
        }
    });
    mymap.addLayer(markersGroup);
    markersGroup.on("mouseover",function(){$('.leaflet-interactive').css('cursor','auto')});
    overlayMaps[type] = markersGroup;
    var layersControl = new L.Control.Layers(baseLayers, overlayMaps,{position: 'topright',collapsed: false});
    mymap.addControl(layersControl);   
}

function clearMarkers() {
    $('.leaflet-control-layers').remove();
    $('#color_legend').empty()
    $('#color_legend').show();
    keys = Object.keys(overlayMaps);
    for(var idx = 0; idx < keys.length; idx++){
        overlayMaps[keys[idx]].removeFrom(mymap);
    }
    overlayMaps = {};
}

function loadMarkersGroup(locations, markerColor, type) {
    var scale = 5;
    if(type == 'rsrp' || type == 'rsrq' || type == 'sinr') {
        scale = 4;
    }
    $.each(locations, function( index, value ) {
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(value[0],value[1]),
            map: map,
            type: type,
//            title: type.toUpperCase() + ': ' + value[2],
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: scale,
                fillColor: markerColor,
                fillOpacity: 1,
                strokeWeight: 0
            },
        });
                
//        marker.addListener('click', function() {
//            map.setZoom(16);
//            map.setCenter(marker.getPosition());
//        });
        markers.push(marker);
    });
    
    /*if(type == 'rsrp' || type == 'rsrq' || type == 'sinr') {
        toggleMarkersNew(type, $("input:checkbox[name='rf_list'][value="+type+"]").prop("checked"));
    } else {
        $.each($("input:checkbox[name='mute_call']"), function(index, value) {
            if(type == value) {
                toggleMarkersNew(type, $(this).prop("checked"));
            }
        });
    }*/
}
    
function showHideLegends() {
    $.each($("input:checkbox[name='rf_list']"), function() {
        $("#legend_"+$(this).val()).css("display", "none");
    });
    $("#legend_mute").css("display", "none");

    if ($("input:checkbox[name='rf_list']:checked").val()) {
        $("#legend_"+$("input:checkbox[name='rf_list']:checked").val()).css("display", "block");
        $("#legend_"+$("input:checkbox[name='rf_list']:checked").val()).css("right", "0px");
    }
    
    $.each($("input:checkbox[name='mute_call']"), function() {
        if ($(this).is(":checked")) {
            $("#legend_mute").css("display", "block");
            $("#call_"+$(this).val()+"_event").css("display", "block");
            $(this).css("right", "0px");
        } else {
            $("#call_"+$(this).val()+"_event").css("display", "none");
        }
    });
}

function toggleMarkersNew(type, is_show) {
    for (var i = 0; i < markers.length; i++) {
        if(markers[i].type == type) {
            if(is_show) {
                markers[i].setMap(map);
            } else {
                markers[i].setMap(null);
            }
        }
    }
    
    showHideLegends();
}

function toggleMarkersGroup() {
    var mute_call_types = $("input:checkbox[name='mute_call']:checked").map(function(){
        return $(this).val();
    }).get();
    var rf = $("input:checkbox[name='rf_list']:checked").val();
    
    for (var i = 0; i < markers.length; i++) {
        if (rf == markers[i].type) {
            console.log(markers[i].type);
            markers[i].setMap(map);
        } else if(mute_call_types.length > 0 && (markers[i].type == 'mute_uplink' || markers[i].type == 'mute_downlink')) {
            $.each(mute_call_types, function(index, value) {
                if (value == markers[i].type) {
                    console.log(markers[i].type);
                    markers[i].setMap(map);
                } else {
                    markers[i].setMap(null);
                }
            });
        } else {
            markers[i].setMap(null);
        }
    }
    
    showHideLegends();
}

function loadMuteCallRate(gdata) {
    gdata[0] = gdata[0].map(function(elem) {
            return parseFloat(elem);
        });
    gdata[1] = gdata[1].map(function(elem) {
            return parseFloat(elem);
        });
        
    var trend = $("#trend_drop_down").val();
    var postfix = '';
    var x_axis_title = 'Days';
    if(trend == 'hour') {
        postfix = 'hr';
        x_axis_title = 'Hours';
    }
    
    Highcharts.chart('mute_call_rate_chart', {
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        exporting: { enabled: false },
        xAxis: {
            min: 1,
//            max: 24,
            tickInterval: 1,
            labels: {
                formatter: function() {
                    return this.axis.defaultLabelFormatter.call({
                        axis: this.axis,
                        value: this.value
                    }) + postfix;
                }
            },
            title: {
                text: x_axis_title
            }
        },
        yAxis: {
            visible: true,
            min: 0,
//            max: 100,
//            tickInterval: 10,
            lineWidth: 1,
            gridLineWidth: 0,
            title: {
                text: 'Mute Call Rate (%)'
            },
            labels: {
                formatter: function() {
                    return this.axis.defaultLabelFormatter.call({
                        axis: this.axis,
                        value: this.value
                    }) + "%";
                }
            },
        },
        legend: {
            enabled: true,
        },
        plotOptions: {
            series: {
                label: {
                    connectorAllowed: false
                }
            }
        },
        tooltip: {
            formatter: function(){
                return this.x + '<br/><span style="color:' + this.color + '">\u25CF</span> ' + this.series.name + ': <b>' + this.y + '</b> %';
            }
        },
        series: [{
            name: 'Mute Call Rate Uplink',
//            data: [15, 10, 22, 18, 25, 12, 20, 17],
            data: gdata[1],
            showInLegend: true,
            color: '#da06f9'
        },
        {
            name: 'Mute Call Rate Downlink',
//            data: [15, 10, 22, 18, 25, 12, 20, 17],
            data: gdata[0],
            showInLegend: true,
            color: '#04e199'
        }],
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                }
            }]
        }

    });
    $(".highcharts-credits").css("display","none");
}

function loadCallBlockRate(gdata) {
    gdata = gdata.map(function(elem) {
            return parseFloat(elem);
        });
        
    var trend = $("#trend_drop_down").val();
    var postfix = '';
    var x_axis_title = 'Days';
    if(trend == 'hour') {
        postfix = 'hr';
        x_axis_title = 'Hours';
    }
    
    Highcharts.chart('block_call_rate_chart', {
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        exporting: { enabled: false },
        xAxis: {
            min: 1,
//            max: 24,
            tickInterval: 1,
            labels: {
                formatter: function() {
                    return this.axis.defaultLabelFormatter.call({
                        axis: this.axis,
                        value: this.value
                    }) + postfix;
                }
            },
            title: {
                text: x_axis_title
            }
        },
        yAxis: {
            visible: true,
            min: 0,
//            max: 100,
//            tickInterval: 10,
            lineWidth: 1,
            gridLineWidth: 0,
            title: {
                text: 'Block Call Rate (%)'
            },
            labels: {
                formatter: function() {
                    return this.axis.defaultLabelFormatter.call({
                        axis: this.axis,
                        value: this.value
                    }) + "%";
                }
            },
        },
        legend: {
            enabled: true,
        },
        plotOptions: {
            series: {
                label: {
                    connectorAllowed: false
                }
            }
        },
        tooltip: {
            formatter: function(){
                return this.x + '<br/><span style="color:' + this.color + '">\u25CF</span> ' + this.series.name + ': <b>' + this.y + '</b> %';
            }
        },
        series: [{
            name: 'Block Call Rate',
//            data: [15, 10, 22, 18, 25, 12, 20, 17],
            data: gdata,
            showInLegend: false,
//            color: '#da06f9'
        }],
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                }
            }]
        }

    });
    $(".highcharts-credits").css("display","none");
}

function loadCallDropRate(gdata) {
    gdata = gdata.map(function(elem) {
            return parseFloat(elem);
        });
        
    var trend = $("#trend_drop_down").val();
    var postfix = '';
    var x_axis_title = 'Days';
    if(trend == 'hour') {
        postfix = 'hr';
        x_axis_title = 'Hours';
    }
    
    Highcharts.chart('drop_call_rate_chart', {
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        exporting: { enabled: false },
        xAxis: {
            min: 1,
//            max: 24,
            tickInterval: 1,
            labels: {
                formatter: function() {
                    return this.axis.defaultLabelFormatter.call({
                        axis: this.axis,
                        value: this.value
                    }) + postfix;
                }
            },
            title: {
                text: x_axis_title
            }
        },
        yAxis: {
            visible: true,
            min: 0,
//            max: 100,
//            tickInterval: 10,
            lineWidth: 1,
            gridLineWidth: 0,
            title: {
                text: 'Drop Call Rate (%)'
            },
            labels: {
                formatter: function() {
                    return this.axis.defaultLabelFormatter.call({
                        axis: this.axis,
                        value: this.value
                    }) + "%";
                }
            },
        },
        legend: {
            enabled: true,
        },
        plotOptions: {
            series: {
                label: {
                    connectorAllowed: false
                }
            }
        },
        tooltip: {
            formatter: function(){
                return this.x + '<br/><span style="color:' + this.color + '">\u25CF</span> ' + this.series.name + ': <b>' + this.y + '</b> %';
            }
        },
        series: [{
            name: 'Drop Call Rate',
//            data: [15, 10, 22, 18, 25, 12, 20, 17],
            data: gdata,
            showInLegend: false,
//            color: '#da06f9'
        }],
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                }
            }]
        }

    });
    $(".highcharts-credits").css("display","none");
}

function loadMuteDuration(gdata) {
    gdata[0] = gdata[0].map(function(elem) {
            return parseFloat(elem);
        });
    gdata[1] = gdata[1].map(function(elem) {
            return parseFloat(elem);
        });
        
    Highcharts.chart('mute_duration_chart', {
        chart: {
            type: 'column',
        },
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        exporting: { enabled: false },
        xAxis: {
            categories: [
                '(1-2)',
                '(2-4)',
                '(4-6)',
                '(6-8)',
                '(8-10)',
                '(10-12)',
                '(12-14)',
                '(14-16)',
                '(>16)'
            ],
            title: {
                text: 'Total Mute Duration/Call'
            }
        },
        yAxis: {
            visible: true,
            min: 0,
//                    max: 100,
//            tickInterval: 2,
            lineWidth: 1,
            gridLineWidth: 0,
            title: {
                text: 'No of Mute Calls'
            },
        },
        plotOptions: {
            series: {
                stacking: 'normal'
            },
            column: {
                borderWidth: 0,
                pointWidth: 30,
                color: '#f03a55',
                dataLabels: {
                    enabled: false,
                    crop: false,
                    overflow: 'none'
                }
            }
        },
        series: [{
            name: 'No. of Mute Calls Uplink',
//            data: [5, 10, 3, 2, 4, 7, 9, 6, 8],
            data: gdata[1],
            showInLegend: true,
            color: '#da06f9'
        },
        {
            name: 'No. of Mute Calls Downlink',
//            data: [5, 10, 3, 2, 4, 7, 9, 6, 8],
            data: gdata[0],
            showInLegend: true,
            color: '#04e199'
        }],

        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                }
            }]
        }

    });
    $(".highcharts-credits").css("display","none");
}

function loadCoverageQualityMute(gdata) {
    gdata = gdata.map(function(elem) {
            return elem.map(function(elem2) {
                res = elem2.split(",");
                return [parseFloat(res[0]),parseFloat(res[1])];
            });
        });
        
    Highcharts.chart('cov_qua_mute_chart', {
        chart: {
            type: 'scatter',
            marginTop: 10,
            marginBottom: 50,
            marginLeft: 50,
            marginRight: 200
        },
        title: {
            text: ''
        },
        xAxis: {
            min: -140,
            max: -50,
            tickInterval: 5,
            gridLineWidth: 1,
            gridLineColor: '#f8f8f8',
            tickWidth: 0,
            title: {
                text: 'RSRP'
            },
            offset: -155
        },
        yAxis: {
            min: -15,
            max: 30,
            tickInterval: 5,
            lineWidth: 1,
            gridLineWidth: 1,
            gridLineColor: '#f8f8f8',
            tickWidth: 0,
            title: {
                text: 'SINR'
            },
            offset: -270
        },
        legend: {
            enabled: true,
            title: {
                text: 'Downlink Mute Duration(sec)'
            },
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            labelFormatter: function () {
                return this.name;
            }
        },
        plotOptions: {
            scatter: {
                marker: {
                    radius: 4,
                    symbol: 'circle'
                },
            },
            series: {
                events: {
                    legendItemClick: function () {
                        for (i=0; i<this.chart.series.length; i++) {
                            this.chart.series[i].setVisible(false,false);
                        }
                    }
                }
            }
        },
        tooltip: {
            formatter: function(){
                return '<br/><span style="color:' + this.color + '">\u25CF</span> ' + this.series.name + '<br>RSRP: <b>' + this.x + '</b> dBm <br>SINR: <b>' + this.y + '</b> dB';
            }
        },
        series: [{
            name: '1-2',
            color: '#81B622',
//            data: [[-104, -6.0], [-103, 1.8]]
            data: gdata[0]

        }, {
            name: '2-5',
            color: '#FAD02C',
//            data: [[-103, -1.4], [-87, 2.6]]
            data: gdata[1]
        }, {
            name: '5-10',
            color: '#0476D0',
//            data: [[-103, -1.4], [-87, 2.6]]
            data: gdata[2]
        }, {
            name: '10-15',
            color: '#FF8300',
//            data: [[-103, -1.4], [-87, 2.6]]
            data: gdata[3]
        }, {
            name: '>15',
            color: '#F83839',
//            data: [[-103, -1.4], [-87, 2.6]]
            data: gdata[4]
        }]
    });
    $(".highcharts-credits").css("display","none");
}

function loadCallConnectTime(gdata) {
    gdata = gdata.map(function(elem) {
            return parseFloat(elem);
        });
        
    var trend = $("#trend_drop_down").val();
    var postfix = '';
    var x_axis_title = 'Days';
    if(trend == 'hour') {
        postfix = 'hr';
        x_axis_title = 'Hours';
    }
    
    Highcharts.chart('call_connect_time_chart', {
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        exporting: { enabled: false },
        xAxis: {
            min: 1,
//            max: 24,
            tickInterval: 1,
            labels: {
                formatter: function() {
                    return this.axis.defaultLabelFormatter.call({
                        axis: this.axis,
                        value: this.value
                    }) + postfix;
                }
            },
            title: {
                text: x_axis_title
            }
        },
        yAxis: {
            visible: true,
            min: 0,
//            max: 100,
//            tickInterval: 10,
            lineWidth: 1,
            gridLineWidth: 0,
            title: {
                text: 'Call Connect Time (sec)'
            },
        },
        legend: {
            enabled: true,
        },
        plotOptions: {
            series: {
                label: {
                    connectorAllowed: false
                }
            }
        },
        tooltip: {
            formatter: function(){
                return this.x + '<br/><span style="color:' + this.color + '">\u25CF</span> ' + this.series.name + ': <b>' + this.y + '</b> sec';
            }
        },
        series: [{
            name: 'Call Connect Time',
//            data: [15, 10, 22, 18, 25, 12, 20, 17],
            data: gdata,
            showInLegend: false,
//            color: '#da06f9'
        }],
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                }
            }]
        }

    });
    $(".highcharts-credits").css("display","none");
}

function loadCallConnect(gdata) {
    gdata = gdata.map(function(elem) {
            return parseFloat(elem);
        });
        
    Highcharts.chart('call_connect_chart', {
        chart: {
            type: 'column',
        },
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        exporting: { enabled: false },
        xAxis: {
            categories: [
                '(0-2)',
                '(2-4)',
                '(4-6)',
                '(6-8)',
                '(8-10)',
                '(10-12)',
                '(12-14)',
                '(14-16)',
                '(>16)'
            ],
            title: {
                text: 'Call connect time (sec)'
            }
        },
        yAxis: {
            visible: true,
            min: 0,
//                    max: 100,
//            tickInterval: 10,
            lineWidth: 1,
            gridLineWidth: 0,
            title: {
                text: '% of samples'
            },
            labels: {
                formatter: function() {
                    return this.axis.defaultLabelFormatter.call({
                        axis: this.axis,
                        value: this.value
                    }) + "%";
                }
            },
        },
        plotOptions: {
            column: {
                borderWidth: 0,
                pointWidth: 30,
                color: '#fbb91c',
                dataLabels: {
                    enabled: false,
                    crop: false,
                    overflow: 'none'
                }
            }
        },
        tooltip: {
            formatter: function(){
                return this.x + '<br/><span style="color:' + this.color + '">\u25CF</span> ' + this.series.name + ': <b>' + this.y + '</b> %';
            }
        },
        series: [{
            name: '% of samples',
//            data: [15, 10, 22, 18, 25, 12, 20, 17],
            data: gdata,
            showInLegend: false,
        }],

        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                }
            }]
        }

    });
    $(".highcharts-credits").css("display","none");
}

function dragElement(elmnt) {
  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  if (document.getElementById(elmnt.id + "header")) {
      
    /* if present, the header is where you move the DIV from:*/
    document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
  } else {
    /* otherwise, move the DIV from anywhere inside the DIV:*/
    elmnt.onmousedown = dragMouseDown;
  }

  function dragMouseDown(e) {
    e = e || window.event;
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    pos4 = e.clientY;
    document.onmouseup = closeDragElement;
    // call a function whenever the cursor moves:
    document.onmousemove = elementDrag;
  }

  function elementDrag(e) {
    e = e || window.event;
    // calculate the new cursor position:
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;
    pos3 = e.clientX;
    pos4 = e.clientY;
    // set the element's new position:
    elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
  }

  function closeDragElement() {
    /* stop moving when mouse button is released:*/
    document.onmouseup = null;
    document.onmousemove = null;
  }
}
dragElement(document.getElementById(("color_legend")));
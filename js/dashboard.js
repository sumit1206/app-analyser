$(function(){
	$( "#start_dt" ).datepicker({
	todayHighlight: true,
	autoclose: true
	}).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        var newdate = new Date(minDate);
        newdate.setDate(minDate.getDate()+1);
        $('#end_dt').datepicker('setStartDate', newdate);
    });
  });
   $(function(){
	$("#end_dt").datepicker({
		todayHighlight: true,
		autoclose: true
	});
});

$(function(){
	$("#single_day").datepicker({
		todayHighlight: true,
		autoclose: true
	});
});
	
function showToast(){
	$('.toast').toast('show');
}	

function inProgress(elem) {
	$(elem).show();
  }
  
  function stopProgress(elem) {
	$(elem).hide();
  }
  
  function disableElement(elem,msg) {
	  $(elem).html(msg);
	  $(elem).attr("disabled", "disabled");
  }
  
  function enableElement(elem, display_value) {
	  $(elem).html(display_value);
	  $(elem).removeAttr("disabled");
  }

  function fetchFilterDataWithCity(city){
	  $('#os_drop_down').empty();
	  $('#application_selection').empty(); 
	  $('#operator').empty();
	  $.ajax({
		  url: "ajax/city_filtered_params_shi.php",
		  type: "GET", 
		  data:{city:city},
		  error: function(jqXHR, textStatus, errorThrown){
		  console.log(textStatus, errorThrown);
		  },
		   success:function(result) 
		  {
			  result = jQuery.parseJSON(result)
			  var operatingSystem='';
			  var application_select='';
			  var operator_selection='';
			  var spn_arr = result.spn;
			  var operating_arr = result.os;
			  var app_arr = result.app;
			  var message = result.success;
			  if(message==0){ 
				  alert("Opps! No Drive on this city");
			  }else{
			  //operatingSystem += '<option value="">Select Operating system</option>';  
			  for (var i=0;i<operating_arr.length;i++){	
			  operatingSystem += '<option value="'+ operating_arr[i] + '">' + operating_arr[i] + '</option>';
			  }
			  $('#os_drop_down').append(operatingSystem);
			 // application_select += '<option value="">Select Application</option>';
			  application_select += '<option value="All Application">All application</option>';
			  for (var i=0;i<app_arr.length;i++){	
			  application_select += '<option value="'+ app_arr[i] + '">' + app_arr[i] + '</option>';
			  }
			  $('#application_selection').append(application_select);
			 // operator_selection += '<option value="">Select Operator</option>';
			  for (var i=0;i<spn_arr.length;i++){	
			  operator_selection += '<option value="'+ spn_arr[i] + '">' + spn_arr[i] + '</option>';
			  }
			  $('#operator').append(operator_selection);
			  }
			  ///////////////////---additional--/////////////////////////
			  	sel_application = fetchData('selApplication');
				$("#application_selection").val(sel_application);
				operatingSystem = fetchData('operatingSystem');
				$("#os_drop_down").val(operatingSystem);
				operator = fetchData('operator');
				$("#operator").val(operator);
				console.log("in filter fetch respect to city"+sel_application+operatingSystem+operator);
			 ///////////////////////////////////////////////////////		
		  }
	  });
  }

function saveData(key, data){
	$.cookie(key, data);
    }
	  
function fetchData(key){
	var data = $.cookie(key);
	return data;
  }	 

  function removeCookie(key){
	$.cookie(key, null, {  expires: -1 });
	console.log("cookie removed"+fromDate+"==="+toDate);
  }

var fromDate='',toDate='',singleDate='',sel_application='',operatingSystem='',operator='',trend='',city='';
var performance_loading,fst_bit_loading,innitial_buffer_time_loading,total_buffer_count_loading,session_throughput_loading,total_buffer_time_loading,throughput_loading;
var running_apps = [];
var total_points = [];
var url_connection_time_out = 100000;
var type='';
var total_ajax_calls = 0;
var total_ajax_complete = 0;
var submit_btn,submit_btn_val; 

$( document ).ready(function(){

	$('#error_text').hide();
	function loadDatePicker(type) {
		type = fetchData('trend_cookie');   
		if(type =='') {
			//$('#start_dt').show();
			$('#single_day').show();
			$('.input-daterange').hide();
			$('#start_dt').val("");		
			$('#end_dt').val("");
			$('#single_day').val("");
		}
		else if(type == 'custom') {
			$('.input-daterange').show();
			$('#single_day').hide();
			$('#start_dt:text').val("");
			$('#end_dt:text').val("");
			$('#single_day:text').val("");
		}
		else if(type == 'day') {
			$('#single_day').show();
			$('.input-daterange').hide();
			$('#start_dt:text').val(""); 
			$('#end_dt:text').val(""); 
			$('#single_day:text').val("");
		}
	}

  performance_loading = $('#performance_loading');
  fst_bit_loading =  $('#fst_bit_loading');
  innitial_buffer_time_loading = $('#innitial_buffer_time_loading');
  total_buffer_count_loading = $('#total_buffer_count_loading');
  session_throughput_loading = $('#session_throughput_loading');
  total_buffer_time_loading = $('#total_buffer_time_loading');
  throughput_loading = $('#throughput_loading');

  loadDatePicker(type);
  setCookieData();
  fetchFilterDataWithCity(city); 
  showChart();
   
  
	//layout visibility
	$('#innitial_buffer_time_max_min_layout').fadeOut();
	$('#first_bit_rcv_time_max_min_layout').fadeOut();
	$('#total_no_of_buffer_max_min_layout').fadeOut();
	$('#total_buffer_time_chart_max_min_layout').fadeOut();
	$('#session_throughput_max_min_layout').fadeOut();
	//$('#throughput_max_min_layout').fadeOut();
	
	$("#city_drop_down").change(function(){
		saveMainData();
		fetchFilterDataWithCity(city); 
		// showChart();
		// cardData(fromDate, toDate, sel_application, operator, operatingSystem, city);
	});
	$("#single_day").change(function(){
		fromDate='';
		toDate='';
		saveMainData();
		fromDate=singleDate;
		console.log("single=="+singleDate+"and=="+fromDate);
	});
	$("#start_dt").change(function(){
		fromDate='';
		toDate='';
		saveMainData();
	});
	$("#end_dt").change(function(){
		fromDate='';
		toDate=''; 
		saveMainData();
	});
	$("#application_selection").change(function(){
		saveMainData();
	});
	$("#os_drop_down").change(function(){
		saveMainData();
	});
	$("#operator").change(function(){
		saveMainData();
	});
	$('#trend_drop_down').change(function() {
		type = $(this).val();
		saveData('trend_cookie',type);
		loadDatePicker(type);  
 });
 $("#btn_submit").click(function(){
	total_ajax_complete = 0; 
	submit_btn = $(this);
	submit_btn_val = $(this).html();
	//disableElement(submit_btn,"Please wait...");
	
	$('#error_text').hide();
	saveMainData();
	setCookieData();
	if(fetchData('trend_cookie')==''){
		$('#error_text').show();
		$('#error_text').html("Please select trend*");
	}
	if(fetchData('trend_cookie')=='day'){
		singleDate = fetchData('singleDate');
		//console.log("chat date single and from"+singleDate);
		fromDate=singleDate;
		saveData('fromDate',fromDate); 
	}
	else(fetchData('trend_cookie')=='custom')
	{		
		showChart();
	}
});
});

// getting current date
function getCurrentDate()
{
var d = new Date();
var month = d.getMonth()+1;
var day = d.getDate();
var output = ((''+month).length<2 ? '0' : '') + month + '/' +((''+day).length<2 ? '0' : '') + day + '/' + d.getFullYear();	
return output;	
}
// set cookie data
function setCookieData(){
  singleDate = fetchData('singleDate');
  $("#single_day").val(singleDate);
  fromDate = fetchData('fromDate');
  console.log("single"+fromDate);
  $("#start_dt").val(fromDate);
  toDate = fetchData('toDate');
  $("#end_dt").val(toDate);
  sel_application = fetchData('selApplication');
  $("#application_selection").val(sel_application);
  operatingSystem = fetchData('operatingSystem');
  $("#os_drop_down").val(operatingSystem);
  operator = fetchData('operator');
  $("#operator").val(operator); 
  trend = fetchData('trend_cookie');
  $("#trend_drop_down").val(trend);
  city = fetchData('city_cookie');
  $("#city_drop_down").val(city);
}
// saving data to cookie
function saveMainData(){		
  fromDate = $("#start_dt").val();
  console.log("before saving from date"+fromDate);
  saveData('fromDate',fromDate);
  console.log("After Saving From Date"+fromDate);
  toDate = $("#end_dt").val(); 
  console.log("before saving to date"+toDate);
  saveData('toDate',toDate);
  console.log("After Saving to Date"+toDate);
  singleDate = $("#single_day").val();
  console.log("single"+singleDate);
  saveData('singleDate',singleDate);
  sel_application = $("#application_selection").val();
  saveData('selApplication',sel_application);
  operatingSystem = $("#os_drop_down").val();
  saveData('operatingSystem',operatingSystem);
  operator = $("#operator").val();
  saveData('operator',operator);
  trend =  $("#trend_drop_down").val();
  saveData('trend_cookie',trend);
  city =  $("#city_drop_down").val();
  saveData('city_cookie',city);
}
 
function showChart()
{	
	total_ajax_complete = 0;
	console.log("from date in chart"+fromDate);
	if(fromDate==""||sel_application==""||operator==""||city==""){
		//alert("please fill all fields");
		$('#error_text').show();
		$('#error_text').html("Please fill all fields*");
	}else{ 
	submit_btn = $('#btn_submit');
	submit_btn_val = $('#btn_submit').html();	
	disableElement(submit_btn,"Please wait...");
	
	cardDataNew(fromDate, toDate, sel_application, operator,operatingSystem,city);//new added for testing
	
	performanceChartView();
	cardData(fromDate, toDate, sel_application, operator,operatingSystem,city);
	//performanceOverView(fromDate, toDate, operator,operatingSystem);
	if(sel_application == "All Application"){
		total_ajax_calls = 5;
		$('#sessionid_vs_session_throughput_chart').fadeOut();
		//$('#application_vs_first_bit_rcv_time').fadeOut();
		$('#application_innitial_buffer_time_chart').fadeOut();
		$('#application_vs_total_buffer_time_chart').fadeOut();
		$('#application_vs_total_no_of_buffer_chart').fadeOut();
		$('#total_no_of_buffer_body').fadeOut();
   // $('#single_application_vs_throughput_chart').fadeOut();
		
		//layout visibility
		$('#innitial_buffer_time_max_min_layout').hide();
		$('#first_bit_rcv_time_max_min_layout').hide();
		$('#total_no_of_buffer_max_min_layout').hide();
		$('#total_buffer_time_chart_max_min_layout').hide();
		$('#session_throughput_max_min_layout').hide();
   // $('#throughput_max_min_layout').fadeOut();
		$("#chart2").fadeIn();
		//$("#chart5").fadeIn();
		$("#chart3").fadeIn();
		$("#chart").fadeIn();
		//$("#chart4").fadeIn();
		//$('#throughput_division').fadeIn();		
		appVsBtime(fromDate, toDate, operator,operatingSystem,city);
		appVsSessionThroughput(fromDate, toDate, operator,operatingSystem,city);
		appVsInnitialbufferTime(fromDate, toDate, operator,operatingSystem,city);
		//appVsThroughput(fromDate, toDate, operator,operatingSystem);
		//appVsPacakageLoadTime(fromDate, toDate, operator,operatingSystem,city);	
	}else{
		total_ajax_calls = 6;
		//ShowApplicationVsGraph();
		$('#sessionid_vs_session_throughput_chart').fadeIn();
		//$('#application_vs_first_bit_rcv_time').fadeIn();
		$('#application_innitial_buffer_time_chart').fadeIn();
		$('#application_vs_total_buffer_time_chart').fadeIn();
		$('#application_vs_total_no_of_buffer_chart').fadeIn();
		$('#total_no_of_buffer_body').fadeIn();
		//$('#single_application_vs_throughput_chart').fadeIn();
		//layout visibility
		$('#innitial_buffer_time_max_min_layout').show();
		//$('#first_bit_rcv_time_max_min_layout').show();
		$('#total_no_of_buffer_max_min_layout').show();
		$('#total_buffer_time_chart_max_min_layout').show();
		$('#session_throughput_max_min_layout').show(); 
  //  $('#throughput_max_min_layout').fadeIn();
		
		$("#chart2").fadeOut();
		//$("#chart5").fadeOut();
		$("#chart3").fadeOut();
		$("#chart").fadeOut();
		//$("#chart4").fadeOut();
		
		sessionVsThroughput(fromDate, toDate, sel_application, operator,operatingSystem,city);
		singleAppVsInnitialBufferTime(fromDate, toDate, sel_application, operator,operatingSystem,city);//not used
		//singleAppVsFirstBitRcvTime(fromDate, toDate, sel_application, operator,operatingSystem,city);
		singleAppVsTotalNoOfBuffer(fromDate, toDate, sel_application, operator,operatingSystem,city);
		singleAppVsTotalBufferTime(fromDate, toDate, sel_application, operator,operatingSystem,city);
   		//singleAppVsThroughput(fromDate, toDate, sel_application,operator,operatingSystem);
	}
	}
}

function cardData(fromDate, toDate, sel_application, operator,operatingSystem,city){
	//console.log("card data");
	var request = $.ajax({
        url: "ajax/card_shi.php",
        type: "GET",
        data:{from_timestamp:fromDate, to_timestamp:toDate, running_app:sel_application, operator:operator, operatingSystem:operatingSystem,city:city},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
		},
		// timeout: url_connection_time_out,
		cache: false,
 		success:function(result) 
		{
			total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
				enableElement(submit_btn,submit_btn_val);
            }
			result = jQuery.parseJSON(result)
			var total_test = result.total_test;
			var total_buffer = result.total_buffer;
			var total_buffer_time = result.total_buffer_time;
			var total_play_time = result.total_play_time;
			//console.log("total_number_of_test"+total_number_of_test+"average_buffer_time"+average_buffer_time+"max_initial_buffer"+max_initial_buffer+"max_session_throughput"+max_session_throughput);
			$("#total_test").text(total_test);
			$("#average_buffer_time").text(total_buffer_time);
			$("#max_initial_buffer").text(total_play_time);
			$("#max_session_throughput").text(total_buffer);
		},
		done:function(  ) {
			console.log("card data done");
		  },
		fail:function( jqXHR, textStatus ) { 
			alert( "card data loading failed !" + textStatus );
	    }   
	});
	   
	  request.fail(function( jqXHR, textStatus ) {
		alert( "Request failed: " + textStatus );
	  }); 
}

function cardDataNew(fromDate, toDate, sel_application, operator,operatingSystem,city){
	//console.log("card data");
	var request = $.ajax({
        url: "ajax/card_shi.php",
        type: "GET",
        data:{from_timestamp:fromDate, to_timestamp:toDate, running_app:sel_application, operator:operator, operatingSystem:operatingSystem,city:city},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
		},
		// timeout: url_connection_time_out,
		cache: false,
 		success:function(result) 
		{
			result = jQuery.parseJSON(result)
			console.log("new card data"+result);
		},
		done:function(  ) {
			console.log("card data done");
		  },
		fail:function( jqXHR, textStatus ) { 
			alert( "card data loading failed !" + textStatus );
	    }   
	});
	request.fail(function( jqXHR, textStatus ) {
		alert( "Request failed: " + textStatus );
    }); 
}
// performanceChart view condition
function performanceChartView(){
	var currentDate = getCurrentDate();
	//console.log("current date"+currentDate);

	if(toDate=='')
	{
		//console.log("fromDate"+fromDate);
		toDate=fromDate;
		//console.log("from date & to date"+fromDate+"===="+toDate);
	}
	if(currentDate==fromDate||currentDate==toDate|| operator =='WIFI'){
		performanceOverView(fromDate, toDate, operator, operatingSystem, city);
		//console.log("performance overView"+fromDate+"===="+toDate); 
	}
	else
	{
		performanceOverView(fromDate, toDate, operator, operatingSystem, city);
		//performanceOverViewCalculation(fromDate, toDate, operator, operatingSystem, city);
		//console.log("performance calculation"+fromDate+"===="+toDate);
	}
}

function getdata() {
    var series = {
        type: 'pie',
        name:  'Points',
        data: [],
        sliced: true,
        selected: true,
        colorByPoint: true
    };
    for (var i = 0; i < total_points.length; i++) {
        series.data.push({
            name: running_apps[i],
            y: total_points[i] 
        });
    }
    return series;
}

function performanceOverViewCalculation(fromDate, toDate, operator, operatingSystem, city){
	inProgress(performance_loading);
	  $.ajax({
		  url: "ajax/performence_fetch.php",
		  type: "GET",
		  data:{from_date:fromDate, to_date:toDate, operator:operator, operatingSystem:operatingSystem, city:city}, 
		  error: function(jqXHR, textStatus, errorThrown){
		  console.log(textStatus, errorThrown);
		  },
		  // timeout: url_connection_time_out,
		  cache: false,
		   success:function(result) 
		  {
			total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
				enableElement(submit_btn,submit_btn_val);
            }
			stopProgress(performance_loading);
			  running_apps = [];
			  total_points = [];
			  result = jQuery.parseJSON(result);
			  var arr = result.details;
			  if(arr==[]){

			  }
			  for (var i = 0; i < arr.length; i++) {
			  running_apps.push(arr[i].app_name);
			  total_points.push(parseFloat(arr[i].point));
			  //console.log(running_apps+" And "+total_points);
		  }
	  // plot(running_apps, [getdata()],'performanceOverViewChart', 'pie','','');
		var chart;
		chart = new Highcharts.Chart({
			chart: {
				renderTo: 'performanceOverViewChart',
				// borderWidth: 1,
				// borderColor: '#999'
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie'
			  },
			  credits: {
					enabled:false
					},
			xAxis: {},
			plotOptions: {
				pie: {
				  allowPointSelect: true,
				  cursor: 'pointer',
				  showInLegend: true,
				  size: '95%',
				  dataLabels: {
				  enabled: true,
				  format: '<b>{point.name}</b>: {point.percentage:.1f} %'
				  }
				}
			},
			// tooltip: {
			//  pointFormat: '{series.running_apps}: <b>{point.percentage:.1f}%</b>'
			// },
			title: {
				text: ''
			},
			legend: {
				itemWidth: 100
			},
			series: [getdata()]
			});
			},  
			done:function(  ) {
			  console.log("performance overview done");
			},
			fail:function( jqXHR, textStatus ) {
			  alert( "performance overview loading failed !" + textStatus );
			}   
			});
  }
  

function performanceOverView(fromDate, toDate, operator, operatingSystem, city){
  inProgress(performance_loading);
	$.ajax({
        url:"ajax/performance_shi.php",//url: "ajax/performance_pie_chart.php",
        type: "GET",
        data:{from_timestamp:fromDate, to_timestamp:toDate, operator:operator, operatingSystem:operatingSystem, city:city}, 
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
		},
		// timeout: url_connection_time_out,
		cache: false,
 		success:function(result) 
		{
			total_ajax_complete++;
			if(total_ajax_complete == total_ajax_calls) {
			enableElement(submit_btn,submit_btn_val);
		}
      stopProgress(performance_loading);
      running_apps = [];
			total_points = [];
			var arr = jQuery.parseJSON(result);
			for (var i = 0; i < arr.length; i++) {
			running_apps.push(arr[i].running_app);
			total_points.push(parseFloat(arr[i].total_point));
			//console.log(running_apps+" And "+total_points);
		}
    // plot(running_apps, [getdata()],'performanceOverViewChart', 'pie','','');
      var chart;
      chart = new Highcharts.Chart({
          chart: {
              renderTo: 'performanceOverViewChart',
              // borderWidth: 1,
              // borderColor: '#999'
              plotBackgroundColor: null,
              plotBorderWidth: null,
              plotShadow: false,
              type: 'pie'
            },
            credits: {
			      enabled:false
		      	},
          xAxis: {},
          plotOptions: {
              pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                showInLegend: true,
                size: '95%',
                dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
              }
          },
          // tooltip: {
          //  pointFormat: '{series.running_apps}: <b>{point.percentage:.1f}%</b>'
          // },
          title: {
              text: ''
          },
          legend: {
              itemWidth: 100
          },
          series: [getdata()]
          });
		  },  
		  done:function(  ) {
			console.log("performance overview done");
		  },
		  fail:function( jqXHR, textStatus ) {
			alert( "performance overview loading failed !" + textStatus );
	      }   
          });
}

function appVsBtime(fromDate, toDate, operator, operatingSystem,city){
  inProgress(total_buffer_time_loading);
	$.ajax({
        url:"ajax/buffer_time_all_app_shi.php",//url: "ajax/return_session_avg_buffer_time.php",
        type: "GET",
        data:{from_timestamp:fromDate,to_timestamp:toDate,operator:operator,operatingSystem:operatingSystem,city:city},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
		},
		// timeout: url_connection_time_out,
		cache: false,
 		success:function(result) 
		{
			total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
				enableElement(submit_btn,submit_btn_val);
            }
      		stopProgress(total_buffer_time_loading);
			var running_apps = [];
			var averages = [];
			var arr = jQuery.parseJSON(result);
			for (var i = 0; i < arr.length; i++) {
			running_apps.push(arr[i].running_app);
			averages.push(parseFloat(arr[i].average));
		}
		plot(running_apps,averages,'chart', 'column','','Total buffer time (ms)');//Total buffertime (/Test)
		},
		done:function(  ) {
			console.log("application buffer time done");
		  },
		  fail:function( jqXHR, textStatus ) {
			alert( "application vs buffertime loading failed !" + textStatus );
	      }     
    });
}

function appVsSessionThroughput(fromDate, toDate, operator, operatingSystem, city){
  inProgress(session_throughput_loading);
  console.log("app /sessionthroughput");
	$.ajax({
        url:"ajax/avg_session_tp_all_app_shi.php",//url: "ajax/return_session_avg_throughput.php",
        type: "GET",
        data:{from_timestamp:fromDate, to_timestamp:toDate, operator:operator, operatingSystem:operatingSystem, city:city},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
		},
		// timeout: url_connection_time_out,
		cache: false,
 		success:function(result) 
		{
			total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
				enableElement(submit_btn,submit_btn_val);
            }
      		stopProgress(session_throughput_loading);
			var running_apps = [];
			var averages = [];
			var arr = jQuery.parseJSON(result);
			for (var i = 0; i < arr.length; i++) {
			running_apps.push(arr[i].running_app);
			averages.push(parseFloat(arr[i].average));
		}
		plot(running_apps,averages,'chart2', 'column','','Session throughput(kbps)');//Session throughput (/Test)
		},
		done:function(  ) {
			console.log("Application / Session throughput done");
		  },
		  fail:function( jqXHR, textStatus ) {
			alert( "Application / Session throughput loading failed !" + textStatus );
	      }    
    });
}

function appVsInnitialbufferTime(fromDate, toDate, operator, operatingSystem, city){
  inProgress(innitial_buffer_time_loading); 
	$.ajax({
        url:"ajax/initial_buffer_time_all_app_shi.php",//url: "ajax/return_session_avg_initial_buffer.php",
        type: "GET",
        data:{from_timestamp:fromDate,to_timestamp:toDate,operator:operator, operatingSystem:operatingSystem, city:city},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
		},
		//timeout: url_connection_time_out,
		cache: false,
 		success:function(result) 
		{
			total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
				enableElement(submit_btn, submit_btn_val);
            }
     		stopProgress(innitial_buffer_time_loading);
			var running_apps = [];
			var averages = [];
			var arr = jQuery.parseJSON(result);
			for (var i = 0; i < arr.length; i++) {
			running_apps.push(arr[i].running_app);
			averages.push(parseFloat(arr[i].average));
		}
		plot(running_apps,averages,'chart3', 'bar','','Innitial buffer time (ms)');//Innitial buffer time (/Test)
		},
		done:function( ) {
			console.log("Application / Innitial buffertime done");
		  },
		  fail:function( jqXHR, textStatus ) {
			alert( "Application / Innitial buffertime loading failed !" + textStatus );
	      }    
    });
}
function appVsThroughput(fromDate, toDate, operator, operatingSystem, city){
  inProgress(throughput_loading);
	$.ajax({
        url: "ajax/return_avg_throughput_vs_application.php",
        type: "GET",
        data:{from_timestamp:fromDate, to_timestamp:toDate, operator:operator, operatingSystem:operatingSystem,city:city},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
		},
		//timeout: url_connection_time_out,
		cache: false,
 		success:function(result) 
		{
			total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
				enableElement(submit_btn,submit_btn_val);
            }
      		stopProgress(throughput_loading);
			var running_apps = [];
			var averages = [];
			var arr = jQuery.parseJSON(result);
			for (var i = 0; i < arr.length; i++) {
			running_apps.push(arr[i].running_app);
			averages.push(parseFloat(arr[i].average));
		}
		plot(running_apps,averages,'chart4', 'column','','Throughput (kbps)');//Throughput (/Test)
		},
		done:function( ) {
			console.log("Application / Throughput done");
		  },
		  fail:function( jqXHR, textStatus ) {
			alert( "Application / Throughput loading failed !" + textStatus );
	      }     
    });
}
function appVsPacakageLoadTime(fromDate, toDate, operator, operatingSystem, city){
  inProgress(fst_bit_loading);
	$.ajax({
        url: "ajax/return_package_load_time_avg_vs_application.php",
        type: "GET",
        data:{from_timestamp:fromDate,to_timestamp:toDate,operator:operator, operatingSystem:operatingSystem, city:city},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
		},
		//timeout: url_connection_time_out,
		cache: false,
 		success:function(result) 
		{
			total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
				enableElement(submit_btn, submit_btn_val);
            }
      		stopProgress(fst_bit_loading);
			var running_apps = [];
			var averages = [];
			var arr = jQuery.parseJSON(result);
			for (var i = 0; i < arr.length; i++) {
			running_apps.push(arr[i].running_app);
			averages.push(parseFloat(arr[i].average));
    }
    plot(running_apps,averages,'chart5', 'column','','1st bit recived time(ms)');//First bit recived time (/Test)
		},
		done:function( ) {
			console.log("Application / Package loadtime done");
		  },
		  fail:function( jqXHR, textStatus ) {
			alert( "Application / Package loadtime loading failed !" + textStatus );
	      }       
    });
}

//<!--single application chart-->
function sessionVsThroughput(fromDate, toDate, sel_application, operator, operatingSystem, city){
  inProgress(session_throughput_loading);
	$.ajax({ 
        url:"ajax/single_app_session_throughput_shi.php",//url: "ajax/fetch_data_respect_to_datetime_application.php",
        type: "GET",
        data:{from_timestamp:fromDate,to_timestamp:toDate,application:sel_application,operator:operator, operatingSystem:operatingSystem, city:city},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
		},
		//timeout: url_connection_time_out,
		cache: false,
 		success:function(result) 
		{
			total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
				enableElement(submit_btn,submit_btn_val);
            }
     		 stopProgress(session_throughput_loading);
	 		 var max_value=0,min_value=0,average=0;
			 $("#max_session_throughput_chart").text('');
			$("#min_session_throughput_chart").text('');
			$("#average_session_throughput_chart").text('');
			var date_times = [];
			var session_throughputs = [];
			result = jQuery.parseJSON(result)
			var arr = result.details;
			max_value = result.max_value;
			min_value = result.min_value;
			average = result.average;
			$("#max_session_throughput_chart").text(max_value);
			$("#min_session_throughput_chart").text(min_value);
			$("#average_session_throughput_chart").text(average);
			//console.log(max_value+"Session Throughput"+min_value+average);
			for (var i = 0; i < arr.length; i++) {
      date_times.push(arr[i].date_time);
			session_throughputs.push(parseFloat(arr[i].session_throughput));
		}
		plot(date_times,session_throughputs,'sessionid_vs_session_throughput_chart', 'line','','Session throughput (kbps)');//Session throughput (/Test)
		}  
    });
}

function singleAppVsInnitialBufferTime(fromDate, toDate, sel_application, operator, operatingSystem, city){
  inProgress(innitial_buffer_time_loading);
	$.ajax({
        url:"ajax/single_app_initial_buffer_time_shi.php", // url: "ajax/single_app_vs_initial_buffer_time.php",//
        type: "GET",
        data:{from_timestamp:fromDate,to_timestamp:toDate,application:sel_application,operator:operator, operatingSystem:operatingSystem, city},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
		},
		//timeout: url_connection_time_out, 
		cache: false,
 		success:function(result) 
		{
			total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
				enableElement(submit_btn,submit_btn_val);
            }
	  		stopProgress(innitial_buffer_time_loading);
	 		$("#max_innitial_buffer_time_chart").text('');
			$("#min_innitial_buffer_time_chart").text('');
			$("#average_innitial_buffer_time_chart").text('');
      var max_value=0,min_value=0,average=0;
			var date_times = [];
			var initial_buffr_times = [];
			result = jQuery.parseJSON(result)
			var arr = result.details;
			max_value = result.max_value;
			min_value = result.min_value;
			average = result.average;
			$("#max_innitial_buffer_time_chart").text(max_value);
			$("#min_innitial_buffer_time_chart").text(min_value);
			$("#average_innitial_buffer_time_chart").text(average);
			console.log(max_value+"innitial buffer time"+min_value);
			for (var i = 0; i < arr.length; i++) {
			date_times.push(arr[i].date_time);
			initial_buffr_times.push(parseFloat(arr[i].initial_buffr_time));
		}
		plot(date_times,initial_buffr_times,'application_innitial_buffer_time_chart', 'line','','Innitial buffer time (ms)');//Innitial buffer time (/Test)
		},
		done:function( ) {
			console.log("Application / Innitial buffertime done");
		  },
		fail:function( jqXHR, textStatus ) {
			alert( "Application / Innitial buffertime loading failed !" + textStatus );
	      }   
    });
}
function singleAppVsFirstBitRcvTime(fromDate, toDate, sel_application,operator, operatingSystem, city){
  inProgress(fst_bit_loading);
	$.ajax({
        url:"ajax/single_app_first_bit_receive_time_shi.php",//url: "ajax/single_app_vs_first_bit_receive_time.php",
        type: "GET",
        data:{from_timestamp:fromDate,to_timestamp:toDate,application:sel_application,operator:operator, operatingSystem:operatingSystem, city:city},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
		},
		//timeout: url_connection_time_out,
		cache: false,
 		success:function(result) 
		{
			total_ajax_complete++;
            if(total_ajax_complete == total_ajax_calls) {
			enableElement(submit_btn,submit_btn_val);
        }
	  stopProgress(fst_bit_loading);
	  $("#max_recived_time").text('');
	  $("#min_recived_time").text('');
	  $("#average_recived_time").text('');
      var max_value=0,min_value=0,average=0;
			var date_times = [];
			var package_load_times = [];
			result = jQuery.parseJSON(result)
			var arr = result.details;
			max_value = result.max_value;
			min_value = result.min_value;
			average = result.average;
			$("#max_recived_time").text(max_value);
			$("#min_recived_time").text(min_value);
			$("#average_recived_time").text(average);
		//	console.log(max_value+"---"+min_value);
			for (var i = 0; i < arr.length; i++) {
			date_times.push(arr[i].date_time);
			package_load_times.push(parseFloat(arr[i].package_load_time));
		}
		plot(date_times,package_load_times,'application_vs_first_bit_rcv_time', 'line','','First bit recived time (ms)');//Time to first bit recived (/Test)
		},
		done:function( ) {
			console.log("Application / 1st bit recived time done");
		  },
		  fail:function( jqXHR, textStatus ) {
			alert( "Application / 1st bit recived time  loading failed !" + textStatus );
	      }   
    });
}

function singleAppVsTotalNoOfBuffer(fromDate, toDate, sel_application, operator, operatingSystem, city){
  inProgress(total_buffer_count_loading);
	$.ajax({
        url:"ajax/single_app_total_no_of_buffer_shi.php", //url: "ajax/single_app_vs_total_no_of_buffer.php",
        type: "GET",
        data:{from_timestamp:fromDate,to_timestamp:toDate,application:sel_application,operator:operator, operatingSystem:operatingSystem, city:city},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
		},
		//timeout: url_connection_time_out,
		cache: false,
 		success:function(result) 
		{
			total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
				enableElement(submit_btn,submit_btn_val);
            }
      stopProgress(total_buffer_count_loading);
	  var max_value=0,min_value=0,average=0;
	  		$("#max_value_total_no_of_buffer").text('');
			$("#min_value_total_no_of_buffer").text('');
			$("#average_value_total_no_of_buffer").text('');
			var date_times = [];
			var no_of_buffer_per_sessions = [];
			result = jQuery.parseJSON(result)
			var arr = result.details;
			max_value = result.max_value;
			min_value = result.min_value;
			average = result.average;
			$("#max_value_total_no_of_buffer").text(max_value);
			$("#min_value_total_no_of_buffer").text(min_value);
			$("#average_value_total_no_of_buffer").text(average);
			console.log(max_value+"---"+min_value);
			for (var i = 0; i < arr.length; i++) {
			date_times.push(arr[i].date_time);
			no_of_buffer_per_sessions.push(parseFloat(arr[i].no_of_buffer_per_session));
		}
		plot(date_times,no_of_buffer_per_sessions,'application_vs_total_no_of_buffer_chart', 'line','','Total no of buffer');//Total no of buffer (/Test)
		},
		done:function( ) {
			console.log("Application / Total no of buffer done");
		  },
		  fail:function( jqXHR, textStatus ) {
			alert( "Application / Total no of buffer loading failed !" + textStatus );
	      }  
    });
}

function singleAppVsTotalBufferTime(fromDate, toDate, sel_application, operator, operatingSystem, city){
  inProgress(total_buffer_time_loading);
	$.ajax({
        url:"ajax/single_app_total_buffer_time_shi.php",//url: "ajax/single_app_vs_total_buffer_time.php",
        type: "GET",
        data:{from_timestamp:fromDate,to_timestamp:toDate,application:sel_application,operator:operator, operatingSystem:operatingSystem, city:city},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
		},
		//timeout: url_connection_time_out,
		cache: false,
 		success:function(result) 
		{
			total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
				enableElement(submit_btn,submit_btn_val);
            }
      stopProgress(total_buffer_time_loading);
	  var max_value=0,min_value=0,average=0;
			$("#max_value_total_buffer_time").text('');
			$("#min_value_total_buffer_time").text('');
			$("#average_value_total_buffer_time").text('');
			var date_times = [];
			var buffer_times = [];
			result = jQuery.parseJSON(result)
			var arr = result.details;
			max_value = result.max_value;
			min_value = result.min_value;
			average = result.average;
			$("#max_value_total_buffer_time").text(max_value);
			$("#min_value_total_buffer_time").text(min_value);
			$("#average_value_total_buffer_time").text(average);
			console.log(max_value+"singleAppVsTotalBufferTime"+min_value);
			for (var i = 0; i < arr.length; i++) {
			date_times.push(arr[i].date_time);
			buffer_times.push(parseFloat(arr[i].buffer_time));
		}
		plot(date_times,buffer_times,'application_vs_total_buffer_time_chart', 'line','','Total buffer time (ms)');//Total buffer time /Test
		},
		done:function( ) {
			console.log("Application / Total buffer time done");
		},
		fail:function( jqXHR, textStatus ) {
			alert( "Application / Total buffer time loading failed !" + textStatus );
	    }   
    });
}
function singleAppVsThroughput(fromDate, toDate, sel_application,operator, operatingSystem, city){
  inProgress(throughput_loading);
	$.ajax({
        url: "ajax/single_app_vs_throughput.php",
        type: "GET",
        data:{from_timestamp:fromDate,to_timestamp:toDate,application:sel_application,operator:operator,operatingSystem:operatingSystem, city:city},
		error: function(jqXHR, textStatus, errorThrown){
        console.log(textStatus, errorThrown);
		},
		//timeout: url_connection_time_out,
		cache: false,
 		success:function(result) 
		{
			total_ajax_complete++;
                if(total_ajax_complete == total_ajax_calls) {
				enableElement(submit_btn,submit_btn_val);
            }
      stopProgress(throughput_loading);
	  var max_value=0,min_value=0,average=0;
	 		$("#max_value_throughput").text('');
			$("#min_value_throughput").text('');
			$("#average_value_throughput").text('');
			var date_times = [];
			var throughputs = [];
			result = jQuery.parseJSON(result)
			var arr = result.details;
			max_value = result.max_value;
			min_value = result.min_value;
			average = result.average;
			$("#max_value_throughput").text(max_value);
			$("#min_value_throughput").text(min_value);
			$("#average_value_throughput").text(average);
			console.log(max_value+"singleAppVsThroughput"+min_value);
			for (var i = 0; i < arr.length; i++) {
			date_times.push(arr[i].date_time);
			throughputs.push(parseFloat(arr[i].through_put));
		}
		plot(date_times,throughputs,'single_application_vs_throughput_chart', 'line','','Throughput (kbps)');//Total buffer time /Test
		},
		done:function( ) {
			console.log("Application / Throughput done");
		  },
		  fail:function( jqXHR, textStatus ) {
			alert( "Application / Throughput loading failed !" + textStatus );
		  }    
    });
}


function plot(cat, data, id, typeName, chartTitle, yAxisTitle){
		var chart = Highcharts.chart(id,{
			chart: {
			type: typeName
			},
			credits: {
			enabled:false
			},
			title: {
			text: chartTitle
			},
			xAxis: {
			categories: cat
      },
      // legend: {
      //   layout: 'horizontal',
      //   align: 'botton',
      //   verticalAlign: 'middle'
      // },
			yAxis: {
			allowDecimals: false,
			title: {
      text: yAxisTitle
			}
			},
			plotOptions:{
			series: {
      colorByPoint: true,
			allowPointSelect: true
			}
			},
			series: 
			[{
      data: data
      }],
      zones: [{
            value: 0,
            color: '#f7a35c'
        }, {
            value: 1000,
            color: '#7cb5ec'
        }, {
            color: '#90ed7d'
        }]
		});
}
<?php
date_default_timezone_set('Asia/Kolkata');
$lifetime = 8*60*60;
ini_set('session.gc_maxlifetime', $lifetime);
ini_set("session.cookie_lifetime", $lifetime);
session_set_cookie_params($lifetime);

session_start();

if(!isset($_SESSION['login_details'])) {
  header('Location: login.php');
}
//include_once('../config/obj_connection.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
      <title>Call Analyzer | Dashboard</title>
	  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
      <link rel="icon" href="assets/admin/images/redmangoicon.ico" type="image/ico" />
	  <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700" rel="stylesheet">
      <link href="http://fonts.googleapis.com/css?family=Oswald:700,400" rel="stylesheet">
      <link href="assets/global/plugins/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="assets/global/plugins/bower_components/fontawesome/css/font-awesome.min.css" rel="stylesheet">
      <link href="assets/global/plugins/bower_components/animate.css/animate.min.css" rel="stylesheet">
	 <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
      
      <link href="assets/admin/css/reset.css" rel="stylesheet">
      <link href="assets/admin/css/layout.css" rel="stylesheet">
      <link href="assets/admin/css/components.css" rel="stylesheet">
      <link href="assets/admin/css/plugins.css" rel="stylesheet">
      <link href="assets/admin/css/themes/default.theme.css" rel="stylesheet" id="theme">
      <link href="assets/admin/css/custom.css" rel="stylesheet">
	  
	  
  <script src="https://code.jquery.com/jquery-3.4.0.js"></script> 
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="https://code.highcharts.com/modules/data.js"></script>
  <script src="https://code.highcharts.com/modules/exporting.js"></script>  
  <script src="https://code.highcharts.com/modules/accessibility.js"></script>
  
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.js"></script> 
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
   
  <!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/se-2.2.13/dt-1.10.20/af-2.3.4/b-1.6.1/b-print-1.6.1/kt-2.5.1/r-2.2.3/sl-1.3.1/datatables.min.css"/>
  <script type="text/javascript" src="https://cdn.datatables.net/v/se-2.2.13/dt-1.10.20/af-2.3.4/b-1.6.1/b-print-1.6.1/kt-2.5.1/r-2.2.3/sl-1.3.1/datatables.min.js"></script>-->
 
 <link rel="stylesheet" type ="text/css" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css"/>
 <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
	
  
 <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">
  <!--<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.1.1/dt-1.10.20/af-2.3.4/b-1.6.1/b-print-1.6.1/kt-2.5.1/r-2.2.3/sl-1.3.1/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4-4.1.1/dt-1.10.20/af-2.3.4/b-1.6.1/b-print-1.6.1/kt-2.5.1/r-2.2.3/sl-1.3.1/datatables.min.js"></script>-->
 
  <link href="/lib/leaflet/leaflet.css" rel="stylesheet"> 
  <script src="/lib/leaflet/leaflet.js"></script>
  <script src="/lib/leaflet/leaflet.ajax.min.js"></script>
  <script src="/lib/leaflet/TileLayer.Grayscale.js"></script> 
  <script src='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-omnivore/v0.3.1/leaflet-omnivore.min.js'></script>
  
  <!--<script src="js/dashboard.js"></script>-->
  <style>
	.loader {
	  border: 2px solid #f3f3f3;
	  border-radius: 50%;
	  border-top: 5px solid blue;
	  border-right: 5px solid white;
	  border-bottom: 5px solid orange;
	  border-left: 5px solid yellow;
	  width: 30px;
	  height: 30px;
	  -webkit-animation: spin 2s linear infinite;
	  animation: spin 2s linear infinite;
	}

	@-webkit-keyframes spin {
	  0% { -webkit-transform: rotate(0deg); }
	  100% { -webkit-transform: rotate(360deg); }
	}

	@keyframes spin {
	  0% { transform: rotate(0deg); }
	  100% { transform: rotate(360deg); }
	}
</style>
  </head>
  <body class="page-session page-sound page-header-fixed page-sidebar-fixed demo-dashboard-session">
    <section id="wrapper">
      <header id="header">
          <div class="header-left">
              <div class="navbar-minimize-mobile left">
                  <i class="fa fa-bars"></i>
              </div>
              <div class="navbar-header">
                  <a id="tour-1" class="navbar-brand" href="#">
                      <p>Call Analyser</p>
                  </a>
              </div>
              <div class="navbar-minimize-mobile right">
                  <i class="fa fa-cog"></i>
              </div>
              <div class="clearfix"></div>
          </div>
          <div class="header-right">
              <div class="navbar navbar-toolbar">
                  <ul class="nav navbar-nav navbar-left">
                      <li id="tour-2" class="navbar-minimize">
                          <a href="javascript:void(0);" title="Minimize sidebar">
                              <i class="fa fa-bars" style="color: #2a2a2a"></i>
                          </a>
                      </li>
                  </ul>
                  <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown navbar-profile">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                          <span class="meta">
                              <span class="avatar"><img src="assets/admin/images/defaultuser.png" style="background-color: currentColor;" class="img-circle" alt="admin"></span>
                              <span class="text hidden-xs hidden-sm text-muted"><?= @$_SESSION['login_details']['first_name'].' '.@$_SESSION['login_details']['last_name'] ?></span>
                              <span class="caret"></span>
                          </span>
                      </a>
                      <!-- Start dropdown menu -->
                      <ul class="dropdown-menu animated flipInX">
                          <!--<li><a href="#"><i class="fa fa-user"></i>View profile</a></li>-->
                          <li><a href="dashboard.php?type=logout"><i class="fa fa-sign-out"></i>Logout</a></li>
                      </ul>
                      <!--/ End dropdown menu -->
                  </li>
                  </ul>
              </div>
          </div>
      </header>
      
      <aside id="sidebar-left" class="sidebar-circle">
          <div id="tour-8" class="sidebar-content">
            <div class="media">
                <a class="pull-left" href="#.html">
                    <img src="assets/admin/images/RedMango-logo.png" style="background-color: #fff;" alt="admin">
                </a>
                <div class="media-body">
                    <h4 class="media-heading">Welcome, <span><?= @$_SESSION['login_details']['first_name']?></span></h4>
                </div>
            </div>
          </div>
         
          <ul id="tour-9" class="sidebar-menu">
            <li class="submenu">
                  <a href="#">
                      <span class="icon"><i class="fas fa-phone-alt"></i></span>
                      <span class="text">Call analyser</span>
                      <!--<span class="arrow"></span>-->
                      <span class="selected"></span>
				  <ul>
                      <li class="active"><a href="index.php">Dashboard</a></li>
                      <li class="active"><a href="engineering_details.php">Network Details</a></li>
                  </ul>
              </li>
			  <li class="submenu">
                  <a href="#">
                      <span class="icon"><i class="fab fa-android"></i></span>
                      <span class="text">App analyser</span>
                      <!--<span class="arrow"></span>-->
                      <span class="selected"></span>
                  </a>
				  <ul>
                      <li class="active"><a href="app_analyser_dashboard.php">Dashboard</a></li>
                      <li class="active"><a href="app_analyser_engineering_details.php">Network Details</a></li>
					  <li class="active"><a href="settings_app_analyser.php">Settings</a></li>
                  </ul>
              </li>
               <li class="submenu">
                  <a href="#">
                      <span class="icon"><i class="fas fa-diagnoses"></i></span>
                      <span class="text">Black box</span>
                      <!--<span class="arrow"></span>-->
                      <span class="selected"></span>
                  </a>
				  <ul>
                      <li class="active"><a href="black_box_dashboard.php">Dashboard</a></li>
                      <li class="active"><a href="configure_box.php">Configure</a></li>
					  <li class="active"><a href="black_box_settings.php">Settings</a></li>
                  </ul>
              </li>
			  <!--<li class="submenu">
                  <a href="#">
                      <span class="icon"><i class="fas fa-diagnoses"></i></span>
                      <span class="text">Map test</span>
                      <!--<span class="arrow"></span>
                      <span class="selected"></span>
                  </a>
				  <ul>
                      <li class="active"><a href="black_box_map_test.php">Test map</a></li>
                  </ul>
              </li>-->
          </ul>
          
          <div id="tour-10" class="sidebar-footer hidden-xs hidden-sm hidden-md custom_menu_color">
              <a id="fullscreen" class="pull-left" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-title="Fullscreen"><i class="fa fa-desktop"></i></a>
              <a id="logout" data-url="dashboard.php?type=logout" class="pull-left" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-title="Logout"><i class="fa fa-power-off"></i></a>
          </div>
      </aside>

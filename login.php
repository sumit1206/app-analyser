<?php
session_start();
if(isset($_SESSION['login_details']) && $_SESSION['login_details']) {
  header('Location: index.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="description" content="Blankon is a theme fullpack admin template powered by Twitter bootstrap 3 front-end framework. Included are multiple example pages, elements styles, and javascript widgets to get your project started.">
        <meta name="keywords" content="admin, admin template, bootstrap3, clean, fontawesome4, good documentation, lightweight admin, responsive dashboard, webapp">
        <meta name="author" content="Djava UI">
        <title>Call Analyser Admin</title>
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700" rel="stylesheet">
        <link href="assets/global/plugins/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/global/plugins/bower_components/fontawesome/css/font-awesome.min.css" rel="stylesheet">
        <link href="assets/global/plugins/bower_components/animate.css/animate.min.css" rel="stylesheet">
        
        <link href="assets/admin/css/reset.css" rel="stylesheet">
        <link href="assets/admin/css/layout.css" rel="stylesheet">
        <link href="assets/admin/css/themes/default.theme.css" rel="stylesheet" id="theme">
        <link href="assets/admin/css/pages/sign.css" rel="stylesheet">
        <link href="assets/admin/css/custom.css" rel="stylesheet">
    </head>
    <body class="page-sound">

        <div id="sign-wrapper">
            <form class="sign-in form-horizontal shadow rounded no-overflow" id="frm_admin_login" name="frm_admin_login" method="post">
                <div class="sign-header">
                    <div class="form-group">
                        <div class="sign-text">
                            <span>Call Analyser Login</span>
                        </div>
                    </div>
                </div>
                <div class="sign-body">
                    <div class="form-group">
                        <div class="input-group input-group-lg rounded no-overflow">
                            <input type="text" class="form-control input-sm" placeholder="Username or email " name="username">
                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group input-group-lg rounded no-overflow">
                            <input type="password" class="form-control input-sm" placeholder="Password" name="password">
                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group-lg rounded no-overflow">
                            <select class="form-control select_width" id="country_drop_down" name="country_drop_down">
                                <option value="india">India</option>
                                <option value="japan">Japan</option>
                            </select>
                        </div>
                    </div>
                </div><!-- /.sign-body -->
                <div class="sign-footer">
<!--                    <div class="form-group">
                        <div class="row">

                            <div class="col-xs-6 text-right">
                                <a href="page-lost-password.html" title="lost password">Lost password?</a>
                            </div>
                        </div>
                    </div> /.form-group -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-theme btn-lg btn-block no-margin rounded" id="login-btn">Sign In</button>
                    </div><!-- /.form-group -->
                </div><!-- /.sign-footer -->
            </form><!-- /.form-horizontal -->
            
        </div>
        <script src="assets/global/plugins/bower_components/jquery/dist/jquery.min.js"></script>
        <script src="assets/global/plugins/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="assets/global/plugins/bower_components/jquery-validation/dist/jquery.validate.min.js"></script>

        
        <script src="assets/admin/js/notify.js"></script>
        <!-- <script src="assets/admin/js/demo.js"></script> -->
        <script>
        $(document).ready(function(){

            $('#frm_admin_login').validate({ 
                rules: {
                  username : {
                    required : true
                  },
                  password : {
                    required : true
                  },
                  country_drop_down : {
                    required : true
                  }
                },
                messages: {
                  username : {
                    required : "Enter Username"
                  },
                  password : {
                    required : "Enter Password"
                  },
                  country_drop_down : {
                      required : "Select Country"
                  }
                },
                submitHandler: function(form) 
                {      
                  $.ajax({ 
                    url:"dashboard.php",
                    type:'post',
                    data:$('#frm_admin_login').serialize(),
                    dataType:'json',
                    beforeSend:function(){
                      $('#login-btn').attr("disabled", true);
                      $('#login-btn').text('Logging...');
                    },
                    complete:function(){
                      $('#login-btn').attr("disabled", false);
                      $('#login-btn').text('Login');
                    },
                    success:function(jdata) {
                        if(jdata.status == '200') {
                          window.location = jdata.redirect;
                          return;
                        } 
                        else {
                          $('#password').val('');
                          show_alert(jdata.message,'error');
                        }
                    }
                  });
                }
            }); 

            show_alert = function (content,alert_type){
                if(content){
                    $.notify(content, alert_type);
                }
            }
        });
        </script>
    </body>
</html>

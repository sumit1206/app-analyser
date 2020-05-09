<?php

$json_array = array();
session_start();
error_reporting(E_ALL);
//ini_set('display_errors', 1);

if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
    include_once('ajax/obj_connection.php');

    $frm_data = $_POST;

    $validate = array();

    if ($frm_data['username'] == '') {
        $validate[] = 'Username field required';
    }

    if ($frm_data['password'] == '') {
        $validate[] = 'Enter Password';
    }
    
    if ($frm_data['country_drop_down'] == '') {
        $validate[] = 'Select Country';
    }

    if (empty($validate)) {
        $password = $frm_data['password'];
        $is_user = pg_query($con, "select * from admin_mst_users where uname ='" . $frm_data['username'] . "' and password = '" . $password . "' and is_active = '1'");

        if (pg_num_rows($is_user) > 0) {
            
            while ($row = pg_fetch_assoc($is_user)) {

                $session = array('user_id' => $row['user_id'],
                    'username' => $row['uname'],
                    'email_id' => $row['email'],
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'user_type' => $row['user_type'],
                    'is_logged_in' => TRUE,
                    'country' => $frm_data['country_drop_down']
                );
                $_SESSION['login_details'] = $session;
                if (!empty($_SESSION['login_details'])) {
                    //pg_query($con, " update admin_user set logged_in = '".date('Y-m-d H:i:s')."' where id = ".$row['id']." ");
                    $json_array['status'] = 200;
                    $json_array['redirect'] = 'index.php';//'index_test.php'; //edited by sumit
                } else {
                    $json_array['status'] = 400;
                    $json_array['message'] = 'login failed, please try again';
                }
            }
        } else {

            $json_array['status'] = 400;
            $json_array['message'] = 'Invalid Username or Password';
        }
    }
    pg_close($con);
    echo json_encode($json_array);
}

if (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET' && $_GET['type'] == 'logout') {
    session_destroy();
    header("location: login.php");
    exit();
}
?>
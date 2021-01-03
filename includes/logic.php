<?php
// All php process appear here
// include all requirements
/*
 *
 *
 * */
include("DBOP.php");
include("User.php");

// registration processing
if (isset($_POST["reg_name"]) && isset($_POST["reg_phone"])) {
    $user = new User();
    $result = $user->createUser($_POST["reg_name"], $_POST["reg_phone"], $_POST["pass1"]);
    exit();
}

if (isset($_POST['update_user_id'])){
    $user = new User();
    echo $user->updateUser($_POST['edit_name'],$_POST['edit_phone'],$_POST['edit_email'],$_POST['update_user_id']);
    exit();
}

// log in process
if (isset($_POST['log_phone']) && isset($_POST['log_password'])) {
    $user = new User();
    $result = $user->userLogin($_POST['log_phone'], $_POST['log_password']);
    $res = array();
    $res['login_stat'] = $result;
    echo json_encode($res);
    exit();
}

if(isset($_POST['confirm_password'])){
    $pass = md5($_POST['confirm_password']);
    $dbop = new DBOP();
    $res = array();
    $user = $dbop->getConditionalData('users','id',$_SESSION['userId']);
    if($user['passwords'] == $pass){
        $res['confirmation'] = true;
    }
    else{
        $res['confirmation'] = false;
    }
    echo json_encode($res);
    exit();
}

// logout process

if (isset($_POST['logOutReq'])){
    $user = new User();
    $user->userLogout();
}



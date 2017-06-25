<?php

//定义根目录,加载相关文件
define('APP_ROOT', dirname(dirname(__DIR__)).'/');

//加载用户登陆验证
require_once (APP_ROOT.'app/login/loginCheck.php');

//加载数据验证
require_once (APP_ROOT.'include/checkInput.php');

//加载数据库配置
require_once (APP_ROOT.'include/dbConfig.php');
require_once (APP_ROOT.'include/Msqli.class.php');

//验证用户权限
if ($_SESSION['role_group'] < 99) {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '当前无权限操作！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

?>
<!--重置密码-->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/style.css" rel="stylesheet">
        <script src = "../../public/js/jquery-1.8.3/jquery.js"></script>
        <script src = "../../public/js/set-default-password.js"></script>
    </head>
    <body>
        <div class = "reset-pwd-box">
            <div class = "input-box">
                <label>用户名：<input id = "usernameIpt" class = "username-input" type = "text" maxlength = "15" /></label>
                <label>默认密码：<input id = "userpwdIpt" class = "userpwd-input" type = "text" maxlength = "15" value = "123456" /></label>
            </div>
            <div id = "tipsBox" class = "tips-box"></div>
            <div class = "btn-box">
                <button id = "resetBtn" class = "reset-btn" >重置</button>
            </div>

        </div>
    </body>
</html>

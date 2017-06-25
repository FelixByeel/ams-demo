<?php
    //定义根目录，加载数据库相关文件
    define('APP_ROOT', dirname(dirname(__DIR__)).'/');
    require_once (APP_ROOT.'app/login/loginCheck.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../../public/css/modify-password.css" rel="stylesheet">
        <script src = "../../public/js/jquery-1.8.3/jquery.js"></script>
        <script src = "../../public/js/checkInputStr.js"></script>
        <script src = "../../public/js/modify-password.js"></script>
    </head>
    <body>
        <div id = "modifyPwdBox" class = "modify-password-box">
            <div class = "pwd-box">
                <label>用户名：<span class = "username-span"><?php echo $_SESSION['username']; ?></span></label>
                <label>请输入原密码：<input id = "oldPwdIpt" class = "old-pwd-input" type = "password" maxlength = "15"/></label>
                <label>请输入新密码：<input id = "newPwdIpt" class = "new-pwd-input" type = "password" maxlength = "15"/></label>
                <label>请确认新密码：<input id = "cfmPwdIpt" class = "cfm-pwd-input" type = "password" maxlength = "15"/></label>
            </div>
            <div id = "tipsBox" class = "tips-box"></div>
            <div class = "btn-box">
                <button id = "savePwdBtn" class = "save-pwd-btn">保&nbsp;&nbsp;存</button>
            </div>
        </div>
    </body>
</html>

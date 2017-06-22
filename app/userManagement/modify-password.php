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
        <link href="css/style.css" rel="stylesheet">
    </head>
    <body>
        <div class = "modify-password-box">
            <label>用户名：<span class = "username-span"><?php echo $_SESSION['username']; ?></span></label>
            <label>请输入原密码：<input class = "old-pwd-input" type = "password" maxlength = "15"/></label>
            <label>请输入新密码：<input class = "new-pwd-input" type = "password" maxlength = "15"/></label>
            <label>请确认新密码：<input class = "cfm-pwd-input" type = "password" maxlength = "15"/></label>
            <button id = "savePwdBtn" class = "save-pwd-btn">确认</button>
        </div>
    </body>
</html>

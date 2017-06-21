<?php
//登录验证

//加载连接数据库相关文件。
define('APP_ROOT', dirname(dirname(__DIR__)).'/');

//加载数据库配置
require_once (APP_ROOT.'include/dbConfig.php');
require_once (APP_ROOT.'include/Msqli.class.php');

if(isset($_POST['username']) && isset($_POST['userpwd'])){

    $mysqli = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

    $username = mysqli_real_escape_string($mysqli->getLink(), $_POST['username']);
    $password = mysqli_real_escape_string($mysqli->getLink(), $_POST['userpwd']);

    $tableName = 'user_t';
    $condition = " username = '$username' and userpwd = '$password'";

    $result = $mysqli->select($tableName, array('*'), $condition);

    if($row = mysqli_fetch_assoc($result)){

        if(!$row['is_enabled']){
            die('帐号已禁用');
        }

        $recordData['last_time'] = strtotime('now');
        $condition = " username = '" . $username . "'";
        $mysqli->update($tableName, $recordData, $condition);

        session_start();

        $_SESSION['uid'] = $row['uid'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['nick_name'] = $row['nick_name'];
        $_SESSION['role_group'] = $row['role_group'];

        echo "<script language='javascript' type='text/javascript'>
                window.location.href='main.php';
              </script>";
    }
    else{
        //验证失败
        echo '用户名或密码不正确！';
    }
}
else{
    //未登录
    echo "<script language='javascript' type='text/javascript'>
            window.location.href='../../';
          </script>";
}


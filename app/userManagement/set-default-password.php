<?php
//查询用户
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

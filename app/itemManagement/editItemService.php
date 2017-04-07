<?php
//更新操作
/*
*处理接收的JSON数据，添加到数据库
*/
define('APP_ROOT', dirname(dirname(__DIR__)).'/');

//加载数据验证
require_once (APP_ROOT.'include/checkInput.php');

//加载数据库配置
require_once (APP_ROOT.'include/dbConfig.php');
require_once (APP_ROOT.'include/Msqli.class.php');

//验证是否正确提交数据
if (!isset($_POST['itemData'])) {
    die('访问出现异常。');
}

if(!isset ($_POST['status'])){
    die('访问出现异常。');
}

$status = $_POST['status'];
$itemData = $_POST['itemData'];

foreach ($itemData as $key => $value) {
    if(($key == 'itemCount') && ($value[0] == '-')){
        if(checkInput(substr($value,1))){
            die('输入的内容不能包含字符 ：' . checkInput($value));
        }
    }
    else {
        if (checkInput($value)) {
            die('输入的内容不能包含字符 ：' . checkInput($value));
        }
    }
}

print_r($itemData);

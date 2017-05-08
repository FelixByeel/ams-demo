<?php
//本页面处理记录查询
define('APP_ROOT', dirname(dirname(__DIR__)).'/');

//加载用户登陆验证
require_once (APP_ROOT.'app/login/loginCheck.php');

//加载数据验证
require_once (APP_ROOT.'include/checkInput.php');

//加载数据库配置
require_once (APP_ROOT.'include/dbConfig.php');
require_once (APP_ROOT.'include/Msqli.class.php');

if(isset($_POST['searchConditions'])) {
    $searchConditions = $_POST['searchConditions'];
    foreach($searchConditions as $key => $value) {
        if ($checkChar = checkInput($value)) {
            die ('输入的内容不能包含：' + $checkChar);
        }
    }
}else {
    $searchConditions = '';
}



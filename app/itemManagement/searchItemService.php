<?php
/*
*处理所有表的查询请求，将查询结果转为JSON格式并返回
*/

//定义根目录
define('APP_ROOT', dirname(dirname(__DIR__)).'/');

//加载用户登陆验证
require_once (APP_ROOT.'app/login/loginCheck.php');

//加载数据验证
require_once (APP_ROOT.'include/checkInput.php');

//加载数据库配置
require_once (APP_ROOT.'include/dbConfig.php');
require_once (APP_ROOT.'include/Msqli.class.php');

//验证用户权限
if($_SESSION['role_group'] < 2) {
    die('当前用户无法进行此操作！');
}

if(!isset($_POST["searchConditionData"]) || empty($_POST["searchConditionData"])) {
    die("查询失败");
}

$searchCondition = $_POST["searchConditionData"];

//处理全部查询条件组合


//连接数据库
$mysqli          = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

$tabelData      = [];           //保存查询信息

//查询tabelName表的信息

$result = $mysqli->select($tabelName, $columnArray, $conditionStr);

while ($row = mysqli_fetch_assoc($result)) {
    $tabelData[] = $row;
}

//将查询的信息进行JSON转换，添加参数JSON_UNESCAPED_UNICODE解决中文乱码
$tabelData = json_encode($tabelData, JSON_UNESCAPED_UNICODE);

echo $tabelData;

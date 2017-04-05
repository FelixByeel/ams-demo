<?php
/*
*处理所有表的查询请求，将查询结果转为JSON格式并返回
*/

//定义根目录，加载数据库相关文件
define('APP_ROOT', dirname(dirname(__DIR__)).'/');
require_once (APP_ROOT.'include/dbConfig.php');
require_once (APP_ROOT.'include/Msqli.class.php');

if($_SERVER['REQUEST_METHOD'] != 'POST'){
    die('请求的地址发生异常！');
}

$searchCondition = $_POST["tableName"];

if($searchCondition == "item"){
    $tabelName = 'item_t';
    $columnArray = array('*');
    $conditionStr = '';
}
else if($searchCondition == "warehouse"){
    $tabelName = 'warehouse_t';
    $columnArray = array('*');
    $conditionStr = '';
}
//连接数据库
$mysql          = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

$tabelData      = [];           //保存查询信息

//查询tabelName表的信息

$result = $mysql->select($tabelName, $columnArray, $conditionStr);

while ($row = mysqli_fetch_assoc($result)) {
    $tabelData[] = $row;
}

//将查询的信息进行JSON转换，添加参数JSON_UNESCAPED_UNICODE解决中文乱码
$tabelData = json_encode($tabelData, JSON_UNESCAPED_UNICODE);

echo $tabelData;

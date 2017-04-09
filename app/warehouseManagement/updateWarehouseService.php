<?php
/*
*处理接收的数据，添加到数据库
*/
define('APP_ROOT', dirname(dirname(__DIR__)).'/');

//加载用户登陆验证
require_once (APP_ROOT.'app/login/loginCheck.php');

//加载数据验证
require_once (APP_ROOT.'include/checkInput.php');

//加载数据库配置
require_once (APP_ROOT.'include/dbConfig.php');
require_once (APP_ROOT.'include/Msqli.class.php');

//验证用户权限
if ($_SESSION['role_group'] < 2) {
    die('当前用户无法进行此操作！');
}

//验证是否正确提交数据
if (!isset($_POST['warehouseID']) || empty($_POST['warehouseID'])) {
    die('提交的数据有误，请重新输入！');
}
else {
    $warehouseID['warehouse_id'] = $_POST['warehouseID'];
}

if (!isset($_POST['warehouseName']) || empty($_POST['warehouseName'])) {
    die('提交的数据有误，请重新输入！');
}
else {
    $warehouseName['warehouse_name'] = $_POST['warehouseName'];
}

if (checkInput($warehouseName['warehouse_name'])) {
    die('输入的内容不能包含字符 ：' . checkInput($warehouseName['warehouse_name']));
}

$isNumberReg = '/^[1-9]+[0-9]*]*$/';

if (!preg_match($isNumberReg, $warehouseID['warehouse_id'])) {
    die("仓库选择有误!");
}

//写入数据到数据库
$mysqli = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
$tableName = 'warehouse_t';

//检测当前将要添加的仓库是否已经存在
$condition = $warehouseName['warehouse_name'];
$checkRow = $mysqli->select($tableName, array('warehouse_name'), "warehouse_name = '$condition'");
if (mysqli_num_rows($checkRow)) {
    die("$condition  已经存在，请重新输入！");
}

//更新操作
$condition = $warehouseID['warehouse_id'];
$mysqli->update($tableName, $warehouseName, "warehouse_id = $condition");

//判断写入是否成功
if ($mysqli->getAffectedRows() > 0) {
    echo "更新成功！";
}

if ($mysqli->getAffectedRows() < 1) {
    die("更新失败！");
}

if (!$mysqli->getAffectedRows()) {
    die("无更新操作！");
}

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
    $warehouseName['warehouse_id'] = $_POST['warehouseID'];
}

$isNumberReg = '/^[1-9]+[0-9]*]*$/';

if (!preg_match($isNumberReg, $warehouseName['warehouse_id'])) {
    die("仓库选择有误!");
}

//写入数据到数据库
$mysqli = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
$tableName = 'warehouse_t';

//检测当前将要添加的分类是否已经存在
$condition = $warehouseName['warehouse_id'];

$mysqli->delete($tableName, "warehouse_id = $condition");

//判断写入是否成功
if ($mysqli->getAffectedRows() > 0) {
    echo "删除成功！";
}

if ($mysqli->getAffectedRows() < 1) {
    die("删除失败！");
}

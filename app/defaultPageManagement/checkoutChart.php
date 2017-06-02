<?php
define('APP_ROOT', dirname(dirname(__DIR__)).'/');

//加载用户登陆验证
require_once (APP_ROOT.'app/login/loginCheck.php');

//加载数据验证
require_once (APP_ROOT.'include/checkInput.php');

//加载数据库配置
require_once (APP_ROOT.'include/dbConfig.php');
require_once (APP_ROOT.'include/Msqli.class.php');

//处理提交的数据
$windowScreenHeight     = isset($_POST['data']['windowScreenHeight']) ? $_POST['data']['windowScreenHeight'] : 768;
$itemSelectValue        = isset($_POST['data']['itemSelectValue']) ? $_POST['data']['itemSelectValue'] : 0;

if(0 != $itemSelectValue && !preg_match('/^[1-9][0-9]*$/', $itemSelectValue)) {
    die('提交的数据异常');
}

if(!preg_match('/^[1-9][0-9]*$/', $windowScreenHeight)) {
    die('提交的数据异常');
}

//连接数据库
$mysqli         = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

$tableName = 'item_t';
$columnArray = array('id', 'item_name');
$conditionStr = ' is_ended = 1 order by CONVERT(item_name USING gbk)';

$result = $mysqli->select($tableName, $columnArray, $conditionStr);

$itemName = array();
$htmlStr = "<select id = 'itemSelect' class = 'chart-select'>";
while ($row = mysqli_fetch_assoc($result)) {
    $itemName[] = $row['item_name'];
    if($itemSelectValue == $row['id']) {
        $htmlStr .= "<option value = '{$row['id']}' selected = 'selected'>{$row['item_name']}</option>";
    }else {
        $htmlStr .= "<option value = '{$row['id']}'>{$row['item_name']}</option>";
    }
}
$htmlStr .= "</select>";
echo $htmlStr;

//测试输出数据
var_dump($_POST['data']) . '<br/>';


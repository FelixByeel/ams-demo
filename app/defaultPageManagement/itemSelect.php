<?php
define('APP_ROOT', dirname(dirname(__DIR__)).'/');

//加载用户登陆验证
require_once (APP_ROOT.'app/login/loginCheck.php');

//加载数据验证
require_once (APP_ROOT.'include/checkInput.php');

//加载数据库配置
require_once (APP_ROOT.'include/dbConfig.php');
require_once (APP_ROOT.'include/Msqli.class.php');

//当前select选择项
$itemSelectValue        = isset($_POST['data']['itemSelectValue']) ? $_POST['data']['itemSelectValue'] : 0;
//检查提交的数据合法性
if (0 != $itemSelectValue && !preg_match('/^[1-9][0-9]*$/', $itemSelectValue)) {
    die('提交的数据异常');
}

$mysqli         = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

//查询分类列表，并输出分类为select列表
$tableName      = 'item_t';
$columnArray    = array('id', 'item_name');
$conditionStr   = ' is_ended = 1 order by CONVERT(item_name USING gbk)';
$result         = $mysqli->select($tableName, $columnArray, $conditionStr);
$itemArr        = array();
$htmlStr        = "<select id = 'itemSelect' class = 'chart-select'>";

while ($row = mysqli_fetch_assoc($result)) {
    $itemArr[] = $row['id'];
    if ($itemSelectValue == $row['id']) {
        $htmlStr .= "<option value = '{$row['id']}' selected = 'selected'>{$row['item_name']}</option>";
    } else {
        $htmlStr .= "<option value = '{$row['id']}'>{$row['item_name']}</option>";
    }
}
$htmlStr .= "</select>";
echo $htmlStr;

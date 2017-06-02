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
//当前select选择项
$itemSelectValue        = isset($_POST['data']['itemSelectValue']) ? $_POST['data']['itemSelectValue'] : 0;

if (0 != $itemSelectValue && !preg_match('/^[1-9][0-9]*$/', $itemSelectValue)) {
    die('提交的数据异常');
}

if (!preg_match('/^[1-9][0-9]*$/', $windowScreenHeight)) {
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
    $itemArr[$row['id']] = $row['item_name'];
    if ($itemSelectValue == $row['id']) {
        $htmlStr .= "<option value = '{$row['id']}' selected = 'selected'>{$row['item_name']}</option>";
    } else {
        $htmlStr .= "<option value = '{$row['id']}'>{$row['item_name']}</option>";
    }
}
$htmlStr .= "</select>";
echo $htmlStr;

//查询出库记录并统计数据
$tableName      = 'record_t';
$columnArray    = array('record_time', 'update_count');
$conditionStr   = " item_id = {$itemSelectValue} and record_status = '出库'";

//保存最近5个月的月份
$dateArr = array();
for ($i = 0; $i < 5; $i++) {
    if (!$i) {
        $dateArr[$i] = date('Y-m');
    } else {
        $dateArr[$i] = getLastMonth($dateArr[$i - 1]);
    }
}

echo '<br/>';
var_dump($dateArr);
echo '<br/>';

$itemCount  = 0;    //保存当前所选项的总出库数量
$result     = $mysqli->select($tableName, $columnArray, $conditionStr);
while ($row = mysqli_fetch_assoc($result)) {
    $itemCount += $row['update_count'];
}
//测试输出数据
var_dump($_POST['data']) . '<br/>';

echo $itemCount;
echo '<br/>';


//根据当前月份获取上月
function getLastMonth($dateStr)
{
    $year   = explode('-', $dateStr)[0];
    $month  = explode('-', $dateStr)[1];

    if (1 == $month) {
        $month  = 12;
        $year   -= 1;
    } else {
        $month  -= 1;
    }
    return $year . '-' . $month;
}

<?php
define('APP_ROOT', dirname(dirname(__DIR__)).'/');

//加载用户登陆验证
require_once (APP_ROOT.'app/login/loginCheck.php');

//加载数据验证
require_once (APP_ROOT.'include/checkInput.php');

//加载数据库配置
require_once (APP_ROOT.'include/dbConfig.php');
require_once (APP_ROOT.'include/Msqli.class.php');

//加载折线图类
require_once (APP_ROOT.'include/LineChart.class.php');

//处理提交的数据
$windowScreenHeight     = isset($_POST['data']['windowScreenHeight']) ? $_POST['data']['windowScreenHeight'] : 768;
$windowScreenWidth      = isset($_POST['data']['windowScreenWidth']) ? $_POST['data']['windowScreenWidth'] : 1366;
//当前select选择项
$itemSelectValue        = isset($_POST['data']['itemSelectValue']) ? $_POST['data']['itemSelectValue'] : 0;
//检查提交的数据合法性
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
//$itemIDArr        = array();
//$itemNameArr        = array();
while ($row = mysqli_fetch_assoc($result)) {
    $itemIDArr[$row['id']] = $row['item_name'];
}

/**
*统计出库数据。
*根据当前所选择的项，统计最近5个月的出库数量。例如 笔记本硬盘500G， 2017-6 出库数量为 1 ， 2017-5 出库数量为 2
*/

//获取最近$months个月的月份
$dateArr = array();
$months = 10;
for ($i = 0; $i < $months; $i++) {
    if (!$i) {
        $dateArr[$i] = date('Y-m');
    } else {
        $dateArr[$i] = getLastMonth($dateArr[$i - 1]);
    }
}

//查询出库记录并统计数据
$tableName      = 'record_t';
$columnArray    = array('record_time', 'update_count');

//查询条件
//第一次页面加载，默认查询第一项,并查询最近{$length}个月的数据。
if (!$itemSelectValue) {
    $itemSelectValue = array_keys($itemIDArr)[0];
}
$length = count($dateArr);
$startDate  = strtotime($dateArr[$length - 1]);
$conditionStr   = " item_id = {$itemSelectValue} and record_time > {$startDate} and record_status = '出库'";

//查询结果集
$result     = $mysqli->select($tableName, $columnArray, $conditionStr);

//初始化以日期为key的统计数组，将查询结果以月份进行统计。
//$countByMonthArr['2017-06'] = 0;
//$countByMonthArr['2017-05'] = 0;
//$countByMonthArr['2017-04'] = 0;

//初始化统计数组 $countByMonthArr[]
foreach ($dateArr as $key => $value) {
    $countByMonthArr[$value] = 0;
}

while ($row = mysqli_fetch_assoc($result)) {
    for ($i = 0; $i < count($dateArr); $i++) {
        $startDate = strtotime($dateArr[$i]);
        $endDate = $i ? strtotime($dateArr[$i - 1]) : time();
        if ($row['record_time'] > $startDate && $row['record_time'] < $endDate) {
            $countByMonthArr[$dateArr[$i]] += $row['update_count'];
        }
    }
}

//测试数据
ksort($countByMonthArr); //按$key升序排序
/*
foreach ($countByMonthArr as $key => $value) {
    echo '<br/>';
    echo $key . ' : ' . $value;
    echo '<br/>';
}
*/
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

    if($month < 10) {
        return $year . '-' . '0' . $month;
    }
    return $year . '-' . $month;
}

//将上面获得的统计数据，以图表展示。此处为折线图。
header ('Content-Type: image/png');
/*
$im = @imagecreatetruecolor(200, 30)
      or die('Cannot Initialize new GD image stream');
$text_color = imagecolorallocate($im, 233, 14, 91);
imagestring($im, 5, 5, 5, date('Y-m-d H:i:s'), $text_color);
imagepng($im, APP_ROOT . 'public/images/checkoutChart/checkoutChart.png');
imagedestroy($im);
*/
$chartWidth = $windowScreenWidth * 0.5;
$chartHeight = $windowScreenHeight * 0.5;
$img = new LineChart('宽:' . $chartWidth . '- 高：' . $chartHeight, $countByMonthArr, $chartWidth, $chartHeight);
$img->drawLineChart();

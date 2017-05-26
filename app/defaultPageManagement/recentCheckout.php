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

//连接数据库
$mysqli         = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
$tableName      = 'record_t';
$conditionStr   = "rt.record_status = '出库'";
//需要查询的字段
$columnStr = 'it.item_name, rt.record_time, rt.consumer_code';

//record_t 和item_t联合查询
$joinCondition = 'record_t as rt inner join item_t as it on rt.item_id = it.id';

//排序条件
if (1) {
    $orderByDate = ' order by record_time desc';
} else {
    $orderByDate = ' order by record_time asc';
}
$limit = ' limit 0,6';

$sqlStr = 'select ' . $columnStr . ' from ' . $joinCondition . ' where ' . $conditionStr . $orderByDate . $limit;

$result = $mysqli->query($sqlStr);

$str = '<table class = \'recent-checkout-table\'>';

while ($row = mysqli_fetch_assoc($result)) {
    $recordTime = date('m-d', $row['record_time']);

    $str .= '<tr class = \'conten-tr\'>';
    $str .= '<td class = \'conten-td\'>' . $recordTime . '</td>';
    $str .= '<td class = \'conten-td\'>' . '用户：' . $row['consumer_code'] . '</td>';
    $str .= '<td class = \'conten-td\'>' . '物品：' . $row['item_name'] . '</td>';
    $str .= '</tr>';
}

$str .= '</table>';
echo $str;

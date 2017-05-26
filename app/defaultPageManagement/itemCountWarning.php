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
$tableName      = 'item_t';
$columnArray    = array('item_name', 'item_count');
$conditionStr   = ' is_ended = 1 order by item_count asc';

$result = $mysqli->select($tableName, $columnArray, $conditionStr);

$str = '<table class = \'item-warning-table\'>';

while ($row = mysqli_fetch_assoc($result)) {
    if($row['item_count'] < 5) {
        $str .= '<tr class = \'item-tr\'>';
        $str .= '<td class = \'item-td\'>' . $row['item_name'] . '</td>';
        $str .= '<td class = \'item-td\'>库存剩余数量：' . $row['item_count'] . '</td>';
        $str .= '</tr>';
    }
}

$str .= '</table>';
echo $str;

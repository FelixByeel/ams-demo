<?php
define('APP_ROOT', dirname(dirname(__DIR__)).'/');

//加载用户登陆验证
require_once (APP_ROOT.'app/login/loginCheck.php');

//加载数据验证
require_once (APP_ROOT.'include/checkInput.php');

//加载数据库配置
require_once (APP_ROOT.'include/dbConfig.php');
require_once (APP_ROOT.'include/Msqli.class.php');

//测试输出数据
echo $_POST['data'] . '<br/>';

//连接数据库
$mysqli         = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

$tableName = 'item_t';
$columnArray = array('id', 'item_name');
$conditionStr = ' is_ended = 1 order by CONVERT(item_name USING gbk)';

$result = $mysqli->select($tableName, $columnArray, $conditionStr);

$itemName = array();
echo "<select>";
while ($row = mysqli_fetch_assoc($result)) {
    $itemName[] = $row['item_name'];

    echo "<option value = '{$row['id']}'>{$row['item_name']}</option>";

}
echo "</select>";
var_dump($itemName);



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

if(isset($_POST['searchConditions'])) {
    $searchConditions = $_POST['searchConditions'];
    foreach($searchConditions as $key => $value) {
        if ($checkChar = checkInput($value)) {
            die ('输入的内容不能包含：' + $checkChar);
        }
    }
}else {
    $searchConditions = '';
}

//连接数据库
$mysqli         = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
$tableName = 'record_t';

$conditionStr   = '';

//根据提交数据组合查询条件
if(!empty($searchConditions)) {

    if(!empty($searchConditions['startTime'])) {
        $conditionStr .= ' record_time > ' . $searchConditions['startTime'] . ' and';
    }

    if(!empty($searchConditions['endTime'])) {
        $conditionStr .= ' record_time < ' . $searchConditions['endTime'] . ' and';
    }

    if(!empty($searchConditions['dealType'])) {
        if($searchConditions['dealType'] == '其他') {
            $conditionStr .= ' record_status <> \'出库\' and record_status <> \'入库\' and';
        }

        if($searchConditions['dealType'] == '出库') {
            $conditionStr .= ' record_status = \'出库\' and';
        }

        if($searchConditions['dealType'] == '入库') {
            $conditionStr .= ' record_status = \'入库\' and';
        }
    }

    if(!empty($searchConditions['consumerCode'])) {
        $conditionStr .= ' consumer_code = \'' . $searchConditions['consumerCode'] . '\' and';
    }

    if(!empty($searchConditions['computerBarcode'])) {
        $conditionStr .= ' computer_barcode = \'' . $searchConditions['computerBarcode'] . '\' and';
    }

    if(!empty($searchConditions['itemSN'])) {
        $conditionStr .= ' item_sn = \'' . $searchConditions['itemSN'] . '\' and';
    }

    if(!empty($searchConditions['username'])) {
        $conditionStr .= ' username = \'' . $searchConditions['username'] . '\'';
    }
}

//去掉最后一个 and
if(!empty($conditionStr)) {
    $conditionStr = rtrim($conditionStr, 'and');
}

//需要查询的字段
$columnArray    = array('rt.id', 'rt.record_status', 'rt.record_time', 'rt.update_count', 'rt.consumer_code', 'rt.computer_barcode', 'rt.item_sn', 'rt.username', 'it.item_name');

//record_t 和item_t联合查询
$joinCondition = 'record_t as rt inner join item_t as it on rt.item_id = it.id';

$result = $mysqli->joinSelect($tableName, $columnArray, $joinCondition, $conditionStr);

//输出查询结果
echo '<tr>
        <th>记录编号</th>
        <th>名称</th>
        <th>序列号</th>
        <th>数量</th>
        <th>资产条码</th>
        <th>用户工号</th>
        <th>状态</th>
        <th>处理时间</th>
        <th>处理人</th>
    </tr>';

while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['item_name']}</td>
            <td>{$row['item_sn']}</td>
            <td>{$row['update_count']}</td>
            <td>{$row['computer_barcode']}</td>
            <td>{$row['consumer_code']}</td>
            <td>{$row['record_status']}</td>
            <td>{$row['record_time']}</td>
            <td>{$row['username']}</td>
        </tr>";
}


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

if (isset($_POST['searchConditions'])) {
    $searchConditions = $_POST['searchConditions'];
    foreach ($searchConditions as $key => $value) {
        if ($checkChar = checkInput($value)) {
            die ('输入的内容不能包含：' + $checkChar);
        }
    }
} else {
    $searchConditions = '';
}

//连接数据库
$mysqli         = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
$tableName      = 'record_t';
$conditionStr   = '';

//排序条件
if (1) {
    $orderByDate = ' order by record_time desc';
} else {
    $orderByDate = ' order by record_time asc';
}
//根据提交数据组合查询条件
if (!empty($searchConditions)) {
    if (!empty($searchConditions['startTime'])) {
        $conditionStr .= ' record_time >= ' . $searchConditions['startTime'] . ' and';
    }

    if (!empty($searchConditions['endTime'])) {
        $conditionStr .= ' record_time <= ' . $searchConditions['endTime'] . ' and';
    }

    if (!empty($searchConditions['itemName'])) {
        $conditionStr .= 'it.item_name like \'%' . $searchConditions['itemName'] . '%\' and';
    }

    if (!empty($searchConditions['dealType'])) {
        if ($searchConditions['dealType'] == '其他') {
            $conditionStr .= ' record_status <> \'出库\' and record_status <> \'入库\' and';
        }

        if ($searchConditions['dealType'] == '出库') {
            $conditionStr .= ' record_status = \'出库\' and';
        }

        if ($searchConditions['dealType'] == '入库') {
            $conditionStr .= ' record_status = \'入库\' and';
        }
    }

    if (!empty($searchConditions['consumerCode'])) {
        $conditionStr .= ' consumer_code like \'%' . $searchConditions['consumerCode'] . '%\' and';
    }

    if (!empty($searchConditions['computerBarcode'])) {
        $conditionStr .= ' computer_barcode like \'%' . $searchConditions['computerBarcode'] . '%\' and';
    }

    if (!empty($searchConditions['itemSN'])) {
        $conditionStr .= ' item_sn like \'%' . $searchConditions['itemSN'] . '%\' and';
    }

    if (!empty($searchConditions['username'])) {
        $conditionStr .= ' username like \'%' . $searchConditions['username'] . '%\'';
    }
}

//去掉最后一个 and
if (!empty($conditionStr)) {
    $conditionStr = rtrim($conditionStr, 'and');
}

//需要查询的字段
$columnArray    = array('rt.id', 'rt.record_status', 'rt.record_time', 'rt.update_count', 'rt.consumer_code', 'rt.computer_barcode', 'rt.item_sn', 'rt.username', 'it.item_name');

//record_t 和item_t联合查询
$joinCondition = 'record_t as rt inner join item_t as it on rt.item_id = it.id';

$result = $mysqli->joinSelect($tableName, $columnArray, $joinCondition, $conditionStr, $orderByDate);

//输出查询结果
echo '<div class = \'searchResult\'>';
echo '<table><tr class = \'tableHead\'>
        <th>名称</th>
        <th>序列号</th>
        <th>数量</th>
        <th>处理类型</th>
        <th>资产条码</th>
        <th>用户工号</th>
        <th>处理时间</th>
        <th>处理人</th>
    </tr>';

$i = 0;
while ($row = mysqli_fetch_assoc($result)) {
    if ($i % 2) {
        $htmlStr = '<tr class = \'odd-row\'>';
    } else {
        $htmlStr = '<tr class = \'even-row\'>';
    }

    $htmlStr .= "<td class = 'name-column td-content'>{$row['item_name']}</td>";

    if ($row['item_sn']) {
        $htmlStr .= "<td class = 'sn-column td-content'>{$row['item_sn']}</td>";
    } else {
        $htmlStr .= '<td class = \'sn-column td-content\'>-</td>';
    }

    $htmlStr .= "<td class = 'count-column td-content'>{$row['update_count']}</td>";
    $htmlStr .= "<td class = 'type-column td-content'>{$row['record_status']}</td>";

    if ($row['computer_barcode']) {
        $htmlStr .= "<td class = 'computer-barcode-column td-content'>{$row['computer_barcode']}</td>";
    } else {
        $htmlStr .= '<td class = \'computer-barcode-column td-content\'>-</td>';
    }

    if ($row['consumer_code']) {
        $htmlStr .= "<td class = 'consumer-code-column td-content'>{$row['consumer_code']}</td>";
    } else {
        $htmlStr .= '<td class = \'consumer-code-column td-content\'>-</td>';
    }

    $recordTime = date('Y-m-d', $row['record_time']);
    $htmlStr .= "<td class = 'time-column td-content'>$recordTime</td>";
    $htmlStr .= "<td class = 'username-column td-content'>{$row['username']}</td>
                </tr>";
    echo $htmlStr;
    $i++;
}

//输出表格闭合标签
echo '</table>';

//页码跳转
echo '<div class = \'page\'>';
echo $_POST['page'];



//输出闭合标签
echo '</div>';
echo '</div>';

//底部页面跳转功能
function showPage($count, $countPages, $rowCount, $currentPage){

}

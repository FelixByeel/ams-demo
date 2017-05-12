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
        $conditionStr .= ' it.item_name like \'%' . $searchConditions['itemName'] . '%\' and';
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

//获取查询总记录数
if (empty($conditionStr)) {
    $sql = 'select count(1) from ' . $tableName;
} else {
    $sql = 'select count(1) from ' . $joinCondition . ' where ' . $conditionStr;
}

$countResult = $mysqli->query($sql);
$count = mysqli_fetch_assoc($countResult)['count(1)'];


//获取起始页码
if (isset($_POST['page']) && is_numeric($_POST['page'])) {
    $currentPage = $_POST['page'] > 0 ? $_POST['page'] : 1;
} else {
    $currentPage = 1;
}

//每页显示记录数,根据size查询分页
$size = 10;
$limit = ' limit ' . (($currentPage - 1) * $size) . ', ' . $size;

//根据条件查询相应结果数据

$result = $mysqli->joinSelect($tableName, $columnArray, $joinCondition, $conditionStr, $orderByDate, $limit);


//输出查询结果
echo '<div class = \'searchResult\'>';
echo '<table class = \'result-table\'><tr class = \'table-head\'>
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


    if ('出库' == $row['record_status']) {
        $htmlStr .= "<td class = 'type-column td-content'><span class = 'out'>{$row['record_status']}</span></td>";
    } elseif ('入库' == $row['record_status']) {
        $htmlStr .= "<td class = 'type-column td-content'><span class = 'in'>{$row['record_status']}</span></td>";
    } else {
        $htmlStr .= "<td class = 'type-column td-content'><span class = 'other'>{$row['record_status']}</span></td>";
    }


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

//输出分页
if ($count > $size) {
    showPage($count, $size, $currentPage, 3);
}

//输出闭合标签
echo '</div>';


/**
*功能:底部页面跳转
*demo:  上一页 [1] [2] [3] 4 ...[10] [下一页]
*@param
*       $count          //总记录数
*       $size           //每页显示记录数
*       $currentPage    //当前页
*       $showStyle      //显示页码数，页数为 2*$showStyle+1
*                         如$show_pages=2,显示为 [上一页][1][2][3][4][5]...[下一页]
*/
function showPage($count, $size, $currentPage, $showStyle)
{
    $pages = ceil($count / $size);    //获取总页数
    $startPage = $currentPage - $showStyle;//根据当前页获取起始显示页码和结束显示页码
    $endPage = $currentPage + $showStyle;

    //取得的起始显示页小于1，设置第一页为起始页。
    if ($startPage < 1) {
        //$endPage = $endPage + (1 - $startPage);
        $startPage = 1;
    }

    //取得的结束显示页大于总页数，设置最大页为结束页码。
    if ($endPage > $pages) {
        //$startPage = $startPage - ($endPage - $pages);
        $endPage = $pages;
    }

    $str = '<div class = \'pageBox\'>';

    //previous page
    if ($currentPage > 1) {
        $str .= '<span class = \'previous-page\' onclick = \'searchRecord(' . ($currentPage - 1) . ')\'>上一页</span>';
    }

    //first page
    if ($currentPage != 1) {
        $str .= '<span class = \'first-page\' onclick = \'searchRecord(1)\'>1</span>';
    } else {
        $str .= '<span class = \'current-page\'>1</span>';
    }

    if ($startPage >1) {
        $str .= '<span class = \'ellipsis\'>...</span>';
    }

    for ($i = $startPage + 1; $i < $endPage; $i++) {
        if ($i == $currentPage) {
            $str .= '<span class = \'current-page\'>' . $i . '</span>';
        } else {
            $str .= '<span class = \'subpage\' onclick = \'searchRecord('. $i . ')\'>' . $i . '</span>';
        }
    }

    if ($endPage < $pages) {
        $str .= '<span class = \'ellipsis\'>...</span>';
    }

    //final page
    if ($currentPage != $pages) {
        $str .= '<span class = \'final-page\' onclick = \'searchRecord('. $pages . ')\'>' . $pages . '</span>';
    } else {
        $str .= '<span class = \'current-page\'>' . $pages . '</span>';
    }

    //next page
    if ($currentPage < $pages) {
        $str .= '<span class = \'next-page\' onclick = \'searchRecord(' . ($currentPage + 1) . ')\'>下一页</span>';
    }

    //$str .= '<span class = \'page-tips\'>共 ' . $pages . ' 页，' . '当前第 ' . $currentPage . ' 页。 </span>';
    $str .= '<span class = \'page-tips\'>' . '当前第 ' . $currentPage . '/' . $pages . ' 页，共 ' . $count . ' 条记录。';
    $str .= '</div>';
    echo $str;
}

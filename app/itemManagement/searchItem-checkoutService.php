<?php
/*更新操作
*
*处理接收的JSON数据，添加到数据库
*/

define('APP_ROOT', dirname(dirname(__DIR__)).'/');

//加载用户登陆验证
require_once (APP_ROOT.'app/login/loginCheck.php');

//加载数据验证
require_once (APP_ROOT.'include/checkInput.php');

//加载数据库配置
require_once (APP_ROOT.'include/dbConfig.php');
require_once (APP_ROOT.'include/Msqli.class.php');

//验证用户权限
if ($_SESSION['role_group'] < 1) {
    die('当前用户无法进行此操作！');
}

//验证是否正确提交数据
if (!isset($_POST['checkOutRecord']) || empty($_POST['checkOutRecord'])) {
    die('提交的数据异常。');
}

$checkOutRecord = $_POST['checkOutRecord'];

//验证数据是否异常-----------------------------
$isNumberReg = '/^[1-9]+[0-9]*]*$/';

foreach ($checkOutRecord as $key => $value) {
    if (checkInput($value)) {
        $returnStatus['status_id'] = 0;
        $returnStatus['info'] = '输入的内容不能包含字符 ：' . checkInput($value);
        echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
        die();
    }
}
//-----------itemID
if (isset($checkOutRecord['itemID']) || array_key_exists('itemID', $checkOutRecord)) {
    if (empty($checkOutRecord['itemID']) || !preg_match($isNumberReg, $checkOutRecord['itemID'])) {
        $returnStatus['status_id'] = 0;
        $returnStatus['info'] = '提交的数据有误！';
        echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
        die();
    }
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '提交的数据有误！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    die();
}

//-----------updateCount
if (isset($checkOutRecord['updateCount']) || array_key_exists('updateCount', $checkOutRecord)) {
    if (empty($checkOutRecord['updateCount']) || !preg_match($isNumberReg, $checkOutRecord['updateCount'])) {
        $returnStatus['status_id'] = 0;
        $returnStatus['info'] = '提交的数据有误！';
        echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
        die();
    }
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '提交的数据有误！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    die();
}

//-----------itemSN
if (isset($checkOutRecord['itemSN']) || array_key_exists('itemSN', $checkOutRecord)) {
    if (empty($checkOutRecord['itemSN'])) {
        $checkOutRecord['itemSN'] = '';
    }
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '提交的数据有误！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    die();
}

//-----------consumerCode
if (isset($checkOutRecord['consumerCode']) || array_key_exists('consumerCode', $checkOutRecord)) {
    if (empty($checkOutRecord['consumerCode'])) {
        $returnStatus['status_id'] = 0;
        $returnStatus['info'] = '提交的数据有误！';
        echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
        die();
    }
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '提交的数据有误！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    die();
}

//-----------computerBarcode
if (isset($checkOutRecord['computerBarcode']) || array_key_exists('computerBarcode', $checkOutRecord)) {
    if (!empty($checkOutRecord['computerBarcode']) && !preg_match($isNumberReg, $checkOutRecord['computerBarcode'])) {
        $returnStatus['status_id'] = 0;
        $returnStatus['info'] = '提交的数据有误！';
        echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
        die();
    } elseif (empty($checkOutRecord['computerBarcode'])) {
        $checkOutRecord['computerBarcode'] = 0;
    }
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '提交的数据有误！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    die();
}

//var_dump($checkOutRecord);

//处理提交的数据，使之符合数据库写入格式。
$recordData['item_id'] = $checkOutRecord['itemID'];
$recordData['record_status'] = '出库';
$recordData['update_count'] = $checkOutRecord['updateCount'];
$recordData['consumer_code'] = $checkOutRecord['consumerCode'];
$recordData['computer_barcode'] = $checkOutRecord['computerBarcode'];
$recordData['item_sn'] = $checkOutRecord['itemSN'];
$recordData['username'] = $_SESSION['username'];
$recordData['record_time'] = strtotime('now');

//----------------数据库操作处理--------------------
$mysqli = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

//同步更新item_t表中的item_count字段。先取出当前id的item_count值，判断是否大于或等于要出库的数量$checkOutRecord['updateCount'];
$tableName = 'item_t';
$condition = 'id = ' . $checkOutRecord['itemID'];

$result = $mysqli->select($tableName, array('item_count'), $condition);
$row = mysqli_fetch_assoc($result);

$itemCount = $row['item_count'];

//库存数量不足时终止出库操作
if ($itemCount < $checkOutRecord['updateCount']) {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '库存数量不足，请修改物品出库数量!';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    die();
}

$colToValue['item_count'] = (int)$itemCount - (int)$checkOutRecord['updateCount'];
//更新item_t表中的item_count值
$mysqli->update($tableName, $colToValue, $condition);

if ($mysqli->getAffectedRows() > 0) {
    //写入一条出库记录到record_t,
    $tableName = 'record_t';

    $result = $mysqli->insert($tableName, $recordData);

    if ($mysqli->getAffectedRows() > 0) {
        $returnStatus['status_id'] = 1;
        $returnStatus['info'] = '操作成功！';
        echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    } else {
        $returnStatus['status_id'] = 0;
        $returnStatus['info'] = '操作失败！';
        echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    }
}

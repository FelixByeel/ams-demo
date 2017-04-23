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
if($_SESSION['role_group'] < 1) {
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
        die('输入的内容不能包含字符 ：' . checkInput($value));
    }
}
//-----------itemID
if(isset($checkOutRecord['itemID']) || array_key_exists('itemID', $checkOutRecord)){
    if(empty($checkOutRecord['itemID']) || !preg_match($isNumberReg, $checkOutRecord['itemID'])){
        die('提交的数据有误！');
    }
}
else {
    die('提交的数据有误！');
}

//-----------updateCount
if(isset($checkOutRecord['updateCount']) || array_key_exists('updateCount', $checkOutRecord)){
    if(empty($checkOutRecord['updateCount']) || !preg_match($isNumberReg, $checkOutRecord['updateCount'])){
        die('提交的数据有误！');
    }
}
else {
    die('提交的数据有误！');
}

//-----------itemSN
if(isset($checkOutRecord['itemSN']) || array_key_exists('itemSN', $checkOutRecord)){
    if(empty($checkOutRecord['itemSN'])){
        $checkOutRecord['itemSN'] = '';
    }
}
else {
    die('提交的数据有误！');
}

//-----------consumerCode
if(isset($checkOutRecord['consumerCode']) || array_key_exists('consumerCode', $checkOutRecord)){
    if(empty($checkOutRecord['consumerCode'])){
        die('提交的数据有误！');
    }
}
else {
    die('提交的数据有误！');
}

//-----------computerBarcode
if(isset($checkOutRecord['computerBarcode']) || array_key_exists('computerBarcode', $checkOutRecord)){
    if(!empty($checkOutRecord['computerBarcode']) && !preg_match($isNumberReg, $checkOutRecord['computerBarcode'])){
        die('提交的数据有误！');
    }
    else if(empty($checkOutRecord['computerBarcode'])){
        $checkOutRecord['computerBarcode'] = 0;
    }
}
else {
    die('提交的数据有误！');
}

var_dump($checkOutRecord);

//处理提交的数据，使之符合数据库写入格式。
$recordData['item_id'] = $checkOutRecord['itemID'];
$recordData['record_status'] = 'out';
$recordData['update_count'] = $checkOutRecord['updateCount'];
$recordData['consumer_code'] = $checkOutRecord['consumerCode'];
$recordData['computer_barcode'] = $checkOutRecord['computerBarcode'];
$recordData['item_sn'] = $checkOutRecord['itemSN'];
$recordData['username'] = $_SESSION['username'];
$recordData['record_time'] = strtotime('now');



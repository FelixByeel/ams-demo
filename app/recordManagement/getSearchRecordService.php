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
$columnArray    = array('*');   //查询所有列
$resultData      = [];           //保存查询结果
$conditionStr   = '';

//根据提交数据组合查询条件
if(!empty($searchConditions)) {

    if(!empty($searchConditions['startTime'])) {
        $conditionStr += ' record_time > ' . $searchConditions['startTime'] . ' and';
    }

    if(!empty($searchConditions['endTime'])) {
        $conditionStr += ' record_time < ' . $searchConditions['endTime'] . ' and';
    }

    if(!empty($searchConditions['dealType'])) {
        if($searchConditions['dealType'] == 'other') {
            $conditionStr += ' record_status <> \'out\' and record_status <> \'in\' and';
        }

        if($searchConditions['dealType'] == 'out') {
            $conditionStr += ' record_status = \'out\' and';
        }

        if($searchConditions['dealType'] == 'in') {
            $conditionStr += ' record_status = \'in\' and';
        }
    }

    if(!empty($searchConditions['consumerCode'])) {
        $conditionStr += ' consumer_code = ' . $searchConditions['consumerCode'] . ' and';
    }

    if(!empty($searchConditions['computerBarcode'])) {
        $conditionStr += ' computer_barcode = ' . $searchConditions['computerBarcode'] . ' and';
    }

    if(!empty($searchConditions['itemSN'])) {
        $conditionStr += ' item_sn = ' . $searchConditions['itemSN'] . ' and';
    }

    if(!empty($searchConditions['username'])) {
        $conditionStr += ' username = ' . $searchConditions['username'];
    }
}else {
    echo "页面首次载入";
}

echo $conditionStr;

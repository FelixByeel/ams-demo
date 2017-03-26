<?php
/*
*处理接收的JSON数据，添加到数据库
*/
define('APP_ROOT', dirname(dirname(__DIR__)).'/');
require_once (APP_ROOT.'include/checkInput.php');


//验证是否正确提交数据，并对数据过滤
if (isset($_POST['itemData'])) {
    $itemData = $_POST['itemData'];

    foreach ($itemData as $key => $value) {
        $itemData[$key] = inputFilter($value);
    }

    $isNumberReg = '/^[1-9]+[0-9]*]*$/';

    if (array_key_exists('warehouse_id', $itemData)) {
        if (!preg_match($isNumberReg, $itemData['warehouse_id'])) {
            die("输入数据有误!");
        }
    }
    if (array_key_exists('id', $itemData)) {
        if (!preg_match($isNumberReg, $itemData['id'])) {
            die("输入数据有误!");
        }
    }
    if (array_key_exists('item_count', $itemData)) {
        if(!empty($itemData['item_count'])){
            if (!preg_match($isNumberReg, $itemData['item_count'])) {
                die("输入数据有误!");
            }
        }
        else {
            $itemData['item_count'] = 0;
        }

    }

    foreach ($itemData as $key => $value) {
        echo '<br />';
        echo $key.' : '.$value;
    }
} else {
    die("请求异常！");
}

<?php
/*
*处理接收的JSON数据，添加到数据库
*/
define('APP_ROOT', dirname(dirname(__DIR__)).'/');

//加载数据验证
require_once (APP_ROOT.'include/checkInput.php');

//加载数据库配置
require_once (APP_ROOT.'include/dbConfig.php');
require_once (APP_ROOT.'include/Msqli.class.php');

//验证是否正确提交数据
if (!isset($_POST['itemData'])) {
    die('访问出现异常，无法查询到相关信息。');
}

$itemData = $_POST['itemData'];

echo '处理前：<br />';
foreach ($itemData as $key => $value) {
    echo '<br />';
    echo $key.' : '.$value;
}

//验证提交的数据是否安全
foreach ($itemData as $key => $value) {
    if(checkInput($value)){
        die('输入的内容不能包含字符 ：' . checkInput($value));
    }
}

$isNumberReg = '/^[1-9]+[0-9]*]*$/';

if (array_key_exists('warehouse_id', $itemData)) {
    if (!empty($itemData['warehouse_id'])) {
        if (!preg_match($isNumberReg, $itemData['warehouse_id'])) {
            die("仓库选择有误!");
        }
    } else {
        die("请选择一个仓库！");
    }
}

if (array_key_exists('parent_id', $itemData)) {
    if (!empty($itemData['parent_id'])) {
        if (!preg_match($isNumberReg, $itemData['parent_id'])) {
            die("分类ID应为正整数，请确认输入是否有误!");
        }
    } else {
        $itemData['parent_id'] = 0;
    }
}

if (array_key_exists('id', $itemData)){
    if(!empty($itemData['id'])) {
        if (!preg_match($isNumberReg, $itemData['id'])) {
            die("分类ID应为正整数，请确认输入是否有误!");
        }
    }
    else {
        die("分类ID不能为空!");
    }
}

if (array_key_exists('item_count', $itemData)) {
    if(!empty($itemData['item_count'])){
        if (!preg_match($isNumberReg, $itemData['item_count'])) {
            die("物品数量应为正整数，请确认输入是否有误!");
        }
    }
    else {
        $itemData['item_count'] = 0;
    }
}



//写入数据到数据库
$mysql = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

if(3 == count($itemData) && array_key_exists('id', $itemData) && (0 != $itemData['item_count'])) {
    $count = $itemData['item_count'];
    $id    = $itemData['id'];
    $result = $mysql->update('item_t', "item_count = $count", "id = $id");
    
}

//$result = $mysql->update();
//测试输出
echo '处理后：<br />';
foreach ($itemData as $key => $value) {
    echo '<br />';
    echo $key.' : '.$value;
}

echo '<br />';
echo count($itemData);
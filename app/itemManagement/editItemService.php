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

if($_SESSION['role_group'] != 99) {
    die('当前用户无法进行此操作！');
}

//验证是否正确提交数据
if (!isset($_POST['itemData']) || empty($_POST['itemData'])) {
    die('提交的数据异常。');
}

$itemData = $_POST['itemData'];

//验证数据是否异常
$isNumberReg = '/^[1-9]+[0-9]*]*$/';

foreach ($itemData as $key => $value) {
    if(($key == 'itemCount') && ($value[0] == '-')){
        if(checkInput(substr($value,1))){
            die('输入的内容不能包含字符 ：' . checkInput($value));
        }

    }
    else {
        if (checkInput($value)) {
            die('输入的内容不能包含字符 ：' . checkInput($value));
        }
    }
}
//------------------------------------------------
if (array_key_exists('tableName', $itemData)) {
    if (empty($itemData['tableName'])) {
        die("当前操作的表名异常");
    } 
} else {
    die("当前操作的表名异常");
}

//------------------------------------------------
if(array_key_exists('itemID', $itemData)){
    if(!preg_match($isNumberReg, $itemData['itemID'])){
        die("操作项ID异常");
    }
}
else{
    die("操作项ID异常");
}

//------------------------------------------------
if(array_key_exists('itemName', $itemData)){
    if(empty($itemData['itemName'])){
        die("分类名称异常");
    }
}
else{
    die("分类名称异常");
}

//------------------------------------------------
if(array_key_exists('parentID', $itemData)){
    if(!empty($itemData['parentID'])){
        if(!preg_match($isNumberReg, $itemData['parentID'])){
            die("上级分类ID异常");
        }
    }
}
else{
    die("上级分类ID异常");
}

//------------------------------------------------
if(array_key_exists('warehouseID', $itemData)){
    if(!preg_match($isNumberReg, $itemData['warehouseID'])){
        die("仓库信息异常");
    }
}
else{
    die("仓库信息异常");
}

//------------------------------------------------
if(array_key_exists('itemCount', $itemData)){
    if(!empty($itemData['itemCount'])){
        if($itemData['itemCount'][0] == '-'){
            if(!preg_match($isNumberReg, substr($itemData['itemCount'], 1))){
                die("输入数量异常");
            }
        }
        else if(!preg_match($isNumberReg, $itemData['itemCount'])){
            die("输入数量异常");
        }
    }
    else {
        $itemData['itemCount'] = 0;
    }
}
else{
    die("输入数量异常");
}

//------------------------------------------------
if(array_key_exists('currentCount', $itemData)){
    if(!empty($itemData['currentCount'])){
        if(!preg_match($isNumberReg, $itemData['currentCount'])){
            die("物品数量异常");
        }
    }
    else {
        $itemData['currentCount'] = 0;
    }
}
else{
    die("物品数量异常");
}

//保存当前要操作的表名和当前更新项的ID
$tableName = $itemData['tableName'].'_t';
$condition = "item_id = " .$itemData['itemID'];

$columnToValue['item_name'] = $itemData['itemName'];
$columnToValue['parent_id'] = $itemData['parentID'];
$columnToValue['warehouse_id'] = $itemData['warehouseID'];

$columnToValue['item_count'] = (int)$itemData['currentCount'] + (int)$itemData['itemCount'];

if($columnToValue['item_count'] < 0) {
    die("物品数量不能为负数");
}

$mysqli = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

$result = $mysqli->update($tableName, $columnToValue, $condition);

if($mysqli->getAffectedRows() > 0){
    echo '更新成功';
}
else if($mysqli->getAffectedRows() < 0){
    echo '更新失败';
}
else if($mysqli->getAffectedRows() == 0){
    echo '当前无更新';
}



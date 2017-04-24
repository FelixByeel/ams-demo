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
if($_SESSION['role_group'] < 2) {
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

//保存当前要操作的表名和当前更新项的条件，以及要更新的内容
$tableName = $itemData['tableName'].'_t';
$condition = "id = " .$itemData['itemID'];

$itemColumnToValue['item_name'] = $itemData['itemName'];

$itemColumnToValue['warehouse_id'] = $itemData['warehouseID'];

//--------------数据库操作处理--------------
$mysqli = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

//更新item_t表中的item_count字段。先取出当前id的item_count值，和当前需要变更的数量相加，总数量不能为负。
$result = $mysqli->select($tableName, array('item_count'), $condition);
$row = mysqli_fetch_assoc($result);

$itemColumnToValue['item_count'] = (int)$row['item_count'] + (int)$itemData['itemCount'];

//处理物品数量的是否正确。
if($itemColumnToValue['item_count'] < 0) {
    die("物品数量不能为负数");
}

//更新当前记录
$flag = 0;
$mysqli->update($tableName, $itemColumnToValue, $condition);

//判断item_count是否有变动，有则同步写入record_t
if($itemData['itemCount']) {

    $recordData['item_id'] = $itemData['itemID'];
    $recordData['record_status'] = 'in';
    $recordData['update_count'] = $itemData['itemCount'];
    $recordData['username'] = $_SESSION['username'];
    $recordData['record_time'] = strtotime('now');

    $result = $mysqli->insert('record_t', $recordData);
}

//上一次更新成功，则继续执行,否则停止执行，并返回操作失败提示
if($mysqli->getAffectedRows() < 0){
    die('更新失败');
}
else {
    if($mysqli->getAffectedRows() > 0){
        $flag++;
    }

    //处理item_id是否需要更新，如果parent_id改变，则相应的item_id也需要改变。
    //当前记录的item_id由上级分类item_id加上当前记录的id构成
    $itemParentID['parent_id'] = $itemData['parentID'];

    $result = $mysqli->update($tableName, $itemParentID, $condition);
    //上一次更新成功，则继续执行,否则停止执行，并返回操作失败提示
    if($mysqli->getAffectedRows() < 0){
        die('更新失败');
    }
    else if($mysqli->getAffectedRows() > 0){
        $flag++;

        $itemID[] = 'item_id';
        $condition = 'id = ' . $itemData['parentID'];
        $result = $mysqli->select($tableName, $itemID, $condition); //  获取上级分类的item_id

        if(1 == $mysqli->getAffectedRows()) {
            //更新当前记录的item_id
            $currentItemID['item_id'] = mysqli_fetch_assoc($result)['item_id'] . '-' . $itemData['itemID'];
            $condition = " id = " .$itemData['itemID'];

            $mysqli->update($tableName, $currentItemID, $condition);
            //上一次更新成功，则继续执行,否则停止执行，并返回操作失败提示
            if($mysqli->getAffectedRows() < 0){
                die('更新失败');
            }
            else if($mysqli->getAffectedRows() > 0) {
                $flag++;
            }
        }
    }
}

if($flag > 0) {
    echo '更新成功';
}
else {
    echo '当前无更新';
}



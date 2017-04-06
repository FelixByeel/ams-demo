<?php
//执行删除操作

$tableName = $_POST['tableName'];
$itemID = $_POST['id'];

if($tableName == "item") $tableName = $tableName.'_t';

$isNumberReg = '/^[1-9]+[0-9]*]*$/';

if(!preg_match($isNumberReg, $itemID)){
    die("请求错误，无法完成删除操作！");
}
//echo "表：".$tableName."  ID: ".$itemID;

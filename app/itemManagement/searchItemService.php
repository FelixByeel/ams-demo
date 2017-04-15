<?php
/*
*处理所有表的查询请求，将查询结果转为JSON格式并返回
*/

//定义根目录,加载相关文件
define('APP_ROOT', dirname(dirname(__DIR__)).'/');

//加载用户登陆验证
require_once (APP_ROOT.'app/login/loginCheck.php');

//加载数据验证
require_once (APP_ROOT.'include/checkInput.php');

//加载数据库配置
require_once (APP_ROOT.'include/dbConfig.php');
require_once (APP_ROOT.'include/Msqli.class.php');

if (!isset($_POST['searchConditionData']) || empty($_POST['searchConditionData'])) {
    die("查询失败");
}

$searchCondition = $_POST['searchConditionData'];

//验证提交的数据是否异常
$isNumberReg = '/^[1-9]+[0-9]*]*$/';

//检查是否含有特殊字符

foreach ($searchCondition as $key => $value) {
    if ('warehouseID' != $key) {
        if ($checkChar = checkInput($value)) {
            die ('输入的内容不能包含：' + $checkChar);
        }
    } elseif ('warehouseID' == $key) {
        $warehouseIDArr = $value;
        if (count($warehouseIDArr)) {
            foreach ($warehouseIDArr as $key => $value) {
                if (!preg_match($isNumberReg, $value)) {
                    die("仓库选择异常");
                }
            }
        }
    }
}

//------------------------------------------------
if (array_key_exists('itemID', $searchCondition)) {
    if (!empty($searchCondition['itemID'])) {
        if (!preg_match($isNumberReg, $searchCondition['itemID'])) {
            die("分类ID异常");
        }
    }
} else {
    die("分类ID异常");
}

//------------------------------------------------
if (array_key_exists('itemParentID', $searchCondition)) {
    if (!empty($searchCondition['itemParentID'])) {
        if (!preg_match($isNumberReg, $searchCondition['itemParentID'])) {
            die("上级分类ID异常");
        }
    }
} else {
    die("上级分类ID异常");
}

//------------------------------------------------------
if (array_key_exists('itemName', $searchCondition)) {
    if (!empty($searchCondition['itemName'])) {
        if (checkInput($searchCondition['itemName'])) {
            die("分类名称不能包含：" . checkInput($searchCondition['itemName']));
        }
    }
} else {
    die("分类名称异常");
}


//连接数据库
$mysqli         = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
$tabelName      = 'item_t';
$columnArray    = array('*');
$tabelData      = [];           //保存查询信息
$conditionStr   = '';

//组合查询条件
if (!empty($searchCondition['itemID'])) {
    $conditionStr .= 'item_id like \'%-' . $searchCondition['itemID'] . '-%\' or ';
    $conditionStr .= 'item_id like \'' . $searchCondition['itemID'] . '-%\' or ';
    $conditionStr .= 'item_id like \'%-' . $searchCondition['itemID'] . '\'';
} else{
    if (!empty($searchCondition['itemParentID'])) {
        $conditionStr .= 'item_id like \'%-' . $searchCondition['itemParentID'] . '-%\' or ' . 'item_id like \'' . $searchCondition['itemParentID'] . '-%\'';
    }

    if(!empty($searchCondition['itemName'])) {
        if(!empty($conditionStr)){
            $conditionStr = "(" . $conditionStr . ") and ";
        }
        $conditionStr .= ' item_name like \'%' . $searchCondition['itemName'] . '%\'';
    }
}

$result = $mysqli->select($tabelName, $columnArray, $conditionStr);

while ($row = mysqli_fetch_assoc($result)) {
    $tabelData[] = $row;
}

//将查询的信息进行JSON转换，添加参数JSON_UNESCAPED_UNICODE解决中文乱码
$tabelData = json_encode($tabelData, JSON_UNESCAPED_UNICODE);

echo $tabelData;

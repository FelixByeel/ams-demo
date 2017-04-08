<?php
/*
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
if ($_SESSION['role_group'] < 2) {
    die('当前用户无法进行此操作！');
}

//验证是否正确提交数据
if (!isset($_POST['itemData'])) {
    die('访问出现异常，无法查询到相关信息。');
}

//保存前端提交的数据
$itemData = $_POST['itemData'];

/*
echo '处理前：<br />';
foreach ($itemData as $key => $value) {
    echo '<br />';
    echo $key.' : '.$value;
}
*/

//验证提交的数据是否安全
foreach ($itemData as $key => $value) {
    if (checkInput($value)) {
        die('输入的内容不能包含字符 ：' . checkInput($value));
    }
}

$isNumberReg = '/^[1-9]+[0-9]*]*$/';

if (3 !== count($itemData)) {
    die("输入的数据有误，请重新输入！");
}

if (array_key_exists('warehouse_id', $itemData)) {
    if (!empty($itemData['warehouse_id'])) {
        if (!preg_match($isNumberReg, $itemData['warehouse_id'])) {
            die("仓库选择有误!");
        }
    } else {
        die("请选择一个仓库！");
    }
} else {
    die("请选择一个仓库！");
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

if (array_key_exists('item_name', $itemData)) {
    if (empty($itemData['item_name'])) {
        die("分类ID不能为空!");
    }
}
/*
if (array_key_exists('item_count', $itemData)) {
    if (!empty($itemData['item_count'])) {
        if (!preg_match($isNumberReg, $itemData['item_count'])) {
            die("物品数量应为正整数，请确认输入是否有误!");
        }
    } else {
        $itemData['item_count'] = 0;
    }
} else {
    die('输入的数据有误，请重新输入！');
}
*/
//写入数据到数据库
$mysqli = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
$tableName = 'item_t';

//检测当前将要添加的分类是否已经存在
$condition = $itemData['item_name'];
$checkRow = $mysqli->select($tableName, array('item_id'), "item_name = '$condition'");
if (mysqli_num_rows($checkRow)) {
    die("$condition  已经存在，请重新输入！");
}

//将新记录写入数据库
$result = $mysqli->insert($tableName, $itemData);

//判断写入是否成功
if ($mysqli->getAffectedRows() > 0) {
    echo "添加成功！";
}

if ($mysqli->getAffectedRows() < 1) {
    die("添加失败！");
}

//有上级分类时，修改上级分类的is_ended状态,如果is_ended为1，则置为0, 同时将上级分类的count清空
if (!empty($itemData['parent_id'])) {
    $parentItem['is_ended'] = 0;
    $parentItem['item_count'] = 0;

    $condition = " item_id = {$itemData['parent_id']}";
    $column[] = 'is_ended';

    $result = $mysqli->select($tableName, $column, $condition);
    $row = mysqli_fetch_assoc($result);

    if (1 == $row['is_ended']) {
        $mysqli->update($tableName, $parentItem, $condition);

        if ($mysqli->getAffectedRows() < 1) {
            echo "上级分类状态修改失败！";
        }
    }
}

/*
//测试输出
echo '<br />';
echo '<br /> 处理后：<br />';
foreach ($itemData as $key => $value) {
    echo '<br />';
    echo $key.' : '.$value;
}

echo '<br />';

echo '<br />';
echo "错误：".$mysqli->getError();
$result = $mysqli->select($tableName, array('*'));
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<br />';
        echo $row['item_id'].' : '.$row['item_name'].' : '.$row['parent_id'].' : '.$row['warehouse_id'].' : '.$row['item_count'];
    }
}
*/

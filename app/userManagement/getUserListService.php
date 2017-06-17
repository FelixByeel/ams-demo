<?php
//查询用户
//定义根目录,加载相关文件
define('APP_ROOT', dirname(dirname(__DIR__)).'/');

//加载用户登陆验证
require_once (APP_ROOT.'app/login/loginCheck.php');

//加载数据验证
require_once (APP_ROOT.'include/checkInput.php');

//加载数据库配置
require_once (APP_ROOT.'include/dbConfig.php');
require_once (APP_ROOT.'include/Msqli.class.php');

//验证用户权限
if ($_SESSION['role_group'] < 99) {
    die('操作异常！');
}

//---------------------------------------------------------------
//验证参数
if (isset ($_POST['searchUser'])) {
    $searchUser = $_POST['searchUser'];
    $userName     = checkInput($searchUser['userName']) ? '' : $searchUser['userName'];
    switch ($searchUser['userStatus']) {
        case 1:
            $userStatus = 1;
            break;
        case 0:
            $userStatus = 0;
            break;
        default:
            $userStatus = -1;
            break;
    }
} else {
    $userName = '';
    $userStatus = -1;
}

$mysqli = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

$tableName = 'user_t';
$columnArray = array('*');
$condition = '';

if (!empty($userName)) {
    $condition .= ' username = ' . '\'' . $userName . '\'';
}

if ($userStatus > -1) {
    if($condition) {
        $condition .= ' and ';
    }
    $condition .= ' is_enabled = ' . $userStatus;
}

$result = $mysqli->select($tableName, $columnArray, $condition);
$resultArr = array();
while ($row = mysqli_fetch_assoc($result)) {
    array_push($resultArr, $row);
}

//将查询的信息进行JSON转换，添加参数JSON_UNESCAPED_UNICODE解决中文乱码
$resultArr = json_encode($resultArr, JSON_UNESCAPED_UNICODE);
echo $resultArr;

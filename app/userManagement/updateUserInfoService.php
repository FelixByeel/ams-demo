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
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '当前无权限操作！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

//验证参数
if (isset ($_POST['userData'])) {
    $userData = $_POST['userData'];
}
else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '提交数据有误！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

if (isset ($userData['uid']) && checkInput($userData['uid']) === false) {
    $condition = " uid = '" . $userData['uid'] . "' ";
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '提交数据有误！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

if (isset ($userData['username']) && checkInput($userData['username']) === false) {
    $userInfo['username'] = $userData['username'];
}

if (isset ($userData['nickname']) && checkInput($userData['nickname']) === false) {
    $userInfo['nick_name'] = $userData['nickname'];
}

if (isset ($userData['rolegroup']) && checkInput($userData['rolegroup']) === false) {
    $userInfo['role_group'] = $userData['rolegroup'];
}

if (isset ($userData['is_enabled']) && checkInput($userData['is_enabled']) === false) {
    $userInfo['is_enabled'] = $userData['is_enabled'];
}
//更新数据库
$mysqli = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
$tableName = 'user_t';
$sql = 'select uid from ' . $tableName . ' where username = \'' . $userInfo['username'] . '\'';
$result = $mysqli->query($sql);
$row = mysqli_fetch_assoc($result);
if(!empty($row) && $row['uid'] !== $userData['uid']) {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = $userInfo['username'] . ' 已存在，请重新输入一个帐号名！' . $row['uid'] . "--" . $userData['uid'];
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

$mysqli->update($tableName, $userInfo, $condition);

if(mysqli_affected_rows($mysqli->getLink()) > 0) {
    $returnStatus['status_id'] = 1;
    $returnStatus['info'] = '更新成功！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
} else {
    $returnStatus['status_id'] = 1;
    $returnStatus['info'] = '当前无更新操作！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
}


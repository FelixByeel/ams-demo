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
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '提交数据有误！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

//username
if (isset($userData['username']) && checkInput($userData['username']) === false) {
    $userInfo['username'] = $userData['username'];
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '提交数据有误！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

//nickname
if (isset($userData['nickname']) && checkInput($userData['nickname']) === false) {
    $userInfo['nick_name'] = $userData['nickname'];
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '提交数据有误！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

//password
if (isset($userData['userpwd']) && !empty($userData['userpwd'])) {
    $userInfo['userpwd'] = $userData['userpwd'];
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '提交数据有误！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

//role group
if (isset($userData['rolegroup'])) {
    switch($userData['rolegroup']) {
        case '1': $userInfo['role_group'] = 1; break;
        case '2': $userInfo['role_group'] = 2; break;
        default: $userInfo['role_group'] = 0;
    }
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '提交数据有误！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

$userInfo['uid']        = MD5($userInfo['username'] . $userInfo['userpwd']);
$userInfo['is_enabled'] = 1;
$userInfo['last_time']  = 0;

//更新数据库
$mysqli = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
$tableName = 'user_t';

//检测是否已存在该账号
$condition = " username = '" . $userInfo['username'] . "'";
$result = $mysqli->select($tableName, array('count(1)'), $condition);
$row = mysqli_fetch_assoc($result);

if($row['count(1)'] > 0) {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = $userInfo['username'] . ' 已存在！请重新输入。';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

//insert
$mysqli->insert($tableName, $userInfo);

if(mysqli_affected_rows($mysqli->getLink()) > 0) {
    $returnStatus['status_id'] = 1;
    $returnStatus['info'] = '添加成功！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '操作异常！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
}

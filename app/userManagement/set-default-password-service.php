<?php
//重置密码
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

if (isset($_POST['username']) && !empty($_POST['username'])) {
    $username = $_POST['username'];
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '用户名不能为空！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

if (isset($_POST['userpwd']) && !empty($_POST['userpwd'])) {
    $userpwd = $_POST['userpwd'];
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '密码不能为空！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

$mysqli = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

$username = mysqli_real_escape_string($mysqli->getLink(), $username);
$userpwd = mysqli_real_escape_string($mysqli->getLink(), $userpwd);

//MD5
$userpwd = MD5($userpwd);

//检测用户是否存在
$tableName = 'user_t';
$column = array('count(1)');
$condition = ' username = \'' . $username . '\'';

$result = $mysqli->select($tableName, $column, $condition);

$row = mysqli_fetch_assoc($result);

if($row['count(1)']) {
    //更新密码
    $setPwdArr = array('userpwd' => $userpwd);
    $mysqli->update($tableName, $setPwdArr, $condition);

    if (mysqli_affected_rows($mysqli->getLink()) > 0) {
        $returnStatus['status_id'] = 1;
        $returnStatus['info'] = '修改密码成功！';
        echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    } else if(0 === mysqli_affected_rows($mysqli->getLink()) ){
        $returnStatus['status_id'] = 1;
        $returnStatus['info'] = '密码无更新！';
        echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    } else {
        $returnStatus['status_id'] = 0;
        $returnStatus['info'] = '密码更新失败！';
        echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    }
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] =  $username . ' 用户不存在，请重新输入用户名！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

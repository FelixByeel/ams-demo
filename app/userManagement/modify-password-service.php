<?php
//修改密码
//定义根目录,加载相关文件
define('APP_ROOT', dirname(dirname(__DIR__)).'/');

//加载用户登陆验证
require_once (APP_ROOT.'app/login/loginCheck.php');

//加载数据验证
require_once (APP_ROOT.'include/checkInput.php');

//加载数据库配置
require_once (APP_ROOT.'include/dbConfig.php');
require_once (APP_ROOT.'include/Msqli.class.php');

if (isset($_POST['oldPwd']) && !empty($_POST['oldPwd'])) {
    $oldPwd = $_POST['oldPwd'];
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '旧密码不能为空！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

if (isset($_POST['newPwd']) && !empty($_POST['newPwd'])) {
    $newPwd = $_POST['newPwd'];
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '新密码不能为空！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

if ($newPwd === $oldPwd) {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '新密码不能和旧密码相同！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

$mysqli = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

$oldPwd = mysqli_real_escape_string($mysqli->getLink(), $oldPwd);
$newPwd = mysqli_real_escape_string($mysqli->getLink(), $newPwd);

//MD5
$oldPwd = MD5($oldPwd);
$newPwd = MD5($newPwd);

//验证原密码
$tableName  = 'user_t';
$username   = $_SESSION['username'];
$column     = array('count(1)');
$condition  = ' username = ' . '\'' .  $username . '\'' . ' and ' . 'userpwd = ' . '\'' . $oldPwd . '\'';

$result = $mysqli->select('user_t', $column, $condition);

$row = mysqli_fetch_assoc($result);

if ($row['count(1)']) {
    //更新密码
    $condition = ' username = ' . '\'' .  $username . '\'';
    $setPwdArr = array('userpwd' => $newPwd);
    $mysqli->update($tableName, $setPwdArr, $condition);

    if (mysqli_affected_rows($mysqli->getLink()) > 0) {
        $returnStatus['status_id'] = 1;
        $returnStatus['info'] = '更新密码成功！';
        echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    } else {
        $returnStatus['status_id'] = 0;
        $returnStatus['info'] = '更新密码失败！';
        echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    }
} else {
    $returnStatus['status_id'] = 0;
    $returnStatus['info'] = '旧密码输入不正确！';
    echo json_encode($returnStatus, JSON_UNESCAPED_UNICODE);
    exit();
}

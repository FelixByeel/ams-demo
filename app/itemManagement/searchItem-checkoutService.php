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
if($_SESSION['role_group'] < 1) {
    die('当前用户无法进行此操作！');
}

//验证是否正确提交数据
if (!isset($_POST['checkOutRecord']) || empty($_POST['checkOutRecord'])) {
    die('提交的数据异常。');
}

$checkOutRecord = $_POST['checkOutRecord'];

//验证数据是否异常
$isNumberReg = '/^[1-9]+[0-9]*]*$/';

foreach ($checkOutRecord as $key => $value) {
    if(($key == 'computerBarcode')){
        if(!empty($value) && !preg_match($isNumberReg, $value)){
            die("资产条码只能为数字！");
        }

    }
    else {
        if (checkInput($value)) {
            die('输入的内容不能包含字符 ：' . checkInput($value));
        }
    }
}

var_dump($checkOutRecord);


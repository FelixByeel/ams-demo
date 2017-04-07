<?php
//登录验证
session_start();
if(!isset($_SERVER['HTTP_REFERER'])){
    die('访问异常！');
}
if(!isset($_SESSION['uid']) || empty($_SESSION['uid'])){
    die("请登陆后操作！");
}

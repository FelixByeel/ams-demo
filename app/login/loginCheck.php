<?php
//登录验证
session_start();

if(!isset($_SESSION['uid']) || empty($_SESSION['uid'])){
    die("<span>请登陆后操作！3秒后自动跳转。</span><a href = 'index.php'>未跳转？点击这里。</a>
    <script language='javascript' type='text/javascript'>
        var timer = setInterval('goToLogin()', 3000);
        function goToLogin () {
            window.location.href='../../index.php';
        }
    </script>
    ");
}

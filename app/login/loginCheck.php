<?php
//登录验证
session_start();

if(!isset($_SESSION['uid']) || empty($_SESSION['uid'])){
    die("<span>请登陆后操作！3秒后自动跳转。</span><a href = '/'>未跳转？点击我。</a>
    <script language='javascript' type='text/javascript'>
        var timer = setInterval('goToLogin()', 3000);
            function goToLogin () {
                window.location.href='/';
            }
    </script>
    ");
}

<?php
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
if($_SESSION['role_group'] < 2) {
    die('当前用户无法进行此操作！');
}
?>
<!--添加分类-->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<link href="../../public/css/addItem.css" rel="stylesheet" type="text/css" />
</head>
<body>

        <div id = 'bodyBox'>
            <div id = "warehouseBox" class = "itemDiv">
                <p>请选择备货仓库：</p>
                <div id = "warehouseList"></div>
            </div>
            <div id = "itemBox" class = "itemDiv">
                <p>请选择上级分类：</p>
                <div id = "itemList"></div>
            </div>

            <div id = "inputBox" class = "itemDiv">
                <p>请输入分类（物品）名称：</p>
                <input id = "itemNameInput" type = "text"/>
                <!--
                <p>请输入物品数量：</p>
                <input id = "itemCountInput" type = "text"/>
                -->
            </div>
            <button id = "addItem" onclick = "addItem()">保存</button>
            <div id = "tips" ></div>
        </div>
</body>
<!--script-->
<script src = "../../public/js/jquery-1.8.3/jquery.js"></script>
<script src = "../../public/js/addItem.js"></script>
</html>



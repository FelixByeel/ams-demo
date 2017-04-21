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
if($_SESSION['role_group'] < 1) {
    die('当前用户无法进行此操作！');
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../../public/css/searchItem-public.css" rel="stylesheet">
        <link href="../../public/css/searchItem-edit.css" rel="stylesheet">

    </head>
    <body>
            <!--遮罩层-->
        <div id = "shadeBox"></div>
        <div id = 'bodyBox'>
            <!--搜索模块 start-->
            <div id = "searchBox">

                    <p id = "warehouseP">所属仓库：
                        <span id = "searchWarehouseNameSpan">无</span>
                    </p>

                    <span id = "itemIDSpan">分类编号：<input id = "searchItemIDInput" type = "text" /></span>
                    <span id = "itemNameSpan">分类名称：
                        <select id = "searchItemNameSelect"></select>
                        <input id = "searchItemNameInput" type = "text" />
                    </span>
                    <button id = "searchButton" type="button">搜索</button>

            </div>

            <!--搜索结果显示区域-->
            <div id = "resultBox">
                <div id = "itemMenuBox">
                    <div id = "itemMenuDiv"></div>
                </div>
                <div id = "itemDetailBox">
                    <div id = "itemDetail"></div>
                </div>
            </div>

            <!--弹出层，出库界面-->
            <div id = "checkOutPopLayer" style = "display: none">
                <span id = "closeSpan" onclick="closePopLayer()">&times;</span>

            </div>
        </div>
        <script src = "../../public/js/jquery-1.8.3/jquery.js"></script>
        <script src = "../../public/js/searchItem-checkout.js"></script>
        <script src = "../../public/js/searchItem-public.js"></script>
    </body>
</html>

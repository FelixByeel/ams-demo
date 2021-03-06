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

                    <span id = "itemIDSpan">分类编号：<input id = "searchItemIDInput" type = "text" maxlength = "5"/></span>
                    <span class = "item-name-span">分类名称：
                        <select id = "searchItemNameSelect"></select>
                        <input id = "searchItemNameInput" type = "text" maxlength = "20"/>
                    </span>
                    <button id = "searchButton" type="button">搜索</button>

            </div>

            <!--搜索结果显示区域-->
            <div id = "resultBox">
                <div id = "item-menu-wrap">
                    <div id = "itemMenuDiv"></div>
                </div>
                <div id = "itemDetailBox">
                    <div id = "itemDetail"></div>
                </div>
            </div>

            <!--弹出层，编辑界面-->
            <div id = "editBox" style = "display: none">
                <span class = 'popLayerTitle'>编辑<span class = "close-poplayer" onclick="closePopLayer()">&times;</span></span>
                <div id = "editBoxHead" style = 'display:none'>
                    <label class = "labelStyle" id = "IDLabel" style = 'display:none'>编号：<span id = "IDSpan"></span></label>
                    <button id = "delButton">删除</button>
                </div>
                <div id = "editBoxContent">
                    <label class = "labelStyle" id = "nameLabel">物品名称：<input id = "itemNameInput" type = "text" value = "" maxlength = "20"/></label>
                    <label class = "labelStyle" id = "classLabel">上级分类：<select id = "classSelect"></select></label>
                    <label class = "labelStyle" id = "warehouseLabel">所属仓库：<select id = "warehouseSelect"></select></label>
                    <label class = "labelStyle" id = "currentCountLabel">当前数量：<span id = "currentCountSpan"></span></label>
                    <label class = "labelStyle" id = "countLabel">变更数量：
                        <input id = "itemCountInput" type = "text" value = "" maxlength = "5"/>
                        <span id = "tips">负数表示从当前数量减去。</span>
                    </label>
                    <button id = "saveButton">保存</button>
                </div>
            </div>
        </div>
        <script src = "../../public/js/jquery-1.8.3/jquery.js"></script>
        <script src = "../../public/js/searchItem-edit.js"></script>
        <script src = "../../public/js/searchItem-public.js"></script>
    </body>
</html>

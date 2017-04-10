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
        <link href="" rel="stylesheet">
        <script src = "../../public/js/jquery-1.8.3/jquery.js"></script>
    </head>
    <body>
        <!--搜索模块 start-->
        <div id = "searchBox">
            <div id = "searchConditionBox">
                <label>分类编号：<input id = "searchItemIDInput" type = "text" /></label>
                <label>分类名称：
                    <select id = "searchItemNameSelect"></select>
                    <input id = "searchItemNameInput" type = "text" />
                </label>
                <label>所属仓库：
                    <span id = "searchWarehouseNameSpan">无</span>
                </label>
            </div>
            <button id = "searchButton" type="button">搜索</button>
        </div>

        <!--搜索结果显示区域-->
        <div id = "resultBox">
            <div id = "itemMenuBox">
                <p>分类列表：</p>
                <div id = "itemMenuDiv"></div>
            </div>
            <div id = "itemDetailBox">
                <p>详细信息：</p>
                <div id = "itemDetail"></div>
            </div>
        </div>

            <!--弹出层，编辑界面-->
            <div id = "editBox" style = "display: none">
                <div id = "editBoxHead">
                    <label id = "IDLabel">编号：<span id = "IDSpan"></span></label>
                    <button id = "delButton">删除</button>
                </div>
                <div id = "editBoxContent">
                    <label id = "nameLabel">名称：<input id = "itemNameInput" type = "text" value = ""/></label>
                    <label id = "classLabel">上级分类：<select id = "classSelect"></select></label>
                    <label id = "warehouseLabel">所属仓库：<select id = "warehouseSelect"></select></label>
                    <label id = "currentCountLabel">当前数量：<span id = "currentCountSpan"></span></label>
                    <label id = "countLabel">变更数量：
                        <input id = "itemCountInput" type = "text" value = ""/>
                        <span id = "">负数表示从当前数量减去。</span>
                    </label>
                    <button id = "saveButton">保存</button><button id = "cancelButton">取消</button>
                </div>
            </div>
    </body>
    <script src = "../../public/js/editItem.js"></script>
</html>

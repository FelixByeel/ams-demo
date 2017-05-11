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
        <link href="../../public/css/searchItem-checkout.css" rel="stylesheet">
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
                    <span class = "item-name-span">分类名称：
                        <select id = "searchItemNameSelect"></select>
                        <input id = "searchItemNameInput" type = "text" />
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

            <!--弹出层，出库界面-->
            <div id = "checkOutPopLayer" class = "poplayer" style = "display: none">
                <span class = 'popLayerTitle'>出库<span class = "close-poplayer" onclick="closePopLayer()">&times;</span></span>
                <!--IDSpan用来标识当前操作项的ID-->
                <span id = "IDSpan" style = "display: none"></span>
                <div class = "content-wrapper">
                    <label class = "labelStyle">物品名称：<span id = "itemNameSpan" class = "item-name-span"></span></label>
                    <label class = "labelStyle">使用数量：
                        <input id = "itemCountInput" class = "item-count-input" type = "text" value = "1"/>
                    </label>
                    <label class = "labelStyle">物品序列号：
                        <input id = "itemSNInput" class = "item-sn-input" type = "text" value = ""/>
                    </label>
                    <label class = "labelStyle">用户工号：
                        <input id = "consumerCodeInput" class = "consumer-code-input" type = "text" value = ""/>
                    </label>
                    <label class = "labelStyle">资产条码：
                        <input id = "computerBarcodeInput" class = "computer-barcode-input"  maxlength = "15" type = "text" value = ""/>
                    </label>
                    <button id = "confirmCheckoutButton">确认</button>
                </div>
            </div>
        </div>
        <script src = "../../public/js/jquery-1.8.3/jquery.js"></script>
        <script src = "../../public/js/searchItem-checkout.js"></script>
        <script src = "../../public/js/searchItem-public.js"></script>
    </body>
</html>

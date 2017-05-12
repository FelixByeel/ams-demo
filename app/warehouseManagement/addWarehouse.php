<?php
    //定义根目录，加载数据库相关文件
    define('APP_ROOT', dirname(dirname(__DIR__)).'/');
    require_once (APP_ROOT.'app/login/loginCheck.php');

    //验证用户权限
    if($_SESSION['role_group'] < 2) {
        die('当前用户无法进行此操作！');
    }
?>
<!--仓库管理-->
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../../public/css/addWarehouse.css" rel="stylesheet">
        <script src = "../../public/js/jquery-1.8.3/jquery.js"></script>
    </head>
    <body>
        <!--遮罩层-->
        <div id = "shadeBox"></div>
        <div id = "bodyBox">
            <div id = "contentBox">
                <div id = "addBox">
                    <p>添加仓库：</p>
                    <input id = "warehouseInput" type = "text"  maxlength = "20"/>
                    <button id = "addWarehouseButton" onclick="addWarehouse()">添加仓库</button>
                </div>

                <div id = "listBox">
                    <p>仓库列表：</p>
                    <div id = "warehouseListBox"></div>
                </div>
            </div>
            <!--弹出层-->
            <div id = "editWarehousePopLayer" class = "poplayer" style="display:none">
                <span class = 'popLayerTitle'>编辑<span class = "close-poplayer" onclick="closePopLayer()">&times;</span></span>
                <div id = "actionEditBox">
                    <label id = "warehouseCodeP" style="display:none">仓库编号：<span id = "warehouseCodeSpan"></span></label>
                    <label id = "warehouseCodeP">仓库名称：
                        <input id = "editWarehouseInput" type = "text" maxlength = "20"/>
                    </label>
                    <button id = "saveWarehouseButton" onclick = "updateWarehouse()">保存修改</button>
                </div>
            </div>
        </div>
    </body>
    <script src = "../../public/js/addWarehouse.js"></script>
</html>

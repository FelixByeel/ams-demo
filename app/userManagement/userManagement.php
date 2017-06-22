<?php
    //定义根目录，加载数据库相关文件
    define('APP_ROOT', dirname(dirname(__DIR__)).'/');
    require_once (APP_ROOT.'app/login/loginCheck.php');

    //验证用户权限
    if($_SESSION['role_group'] < 99) {
        die('当前用户无法进行此操作！');
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../../public/css/userManagement.css" rel="stylesheet" type="text/css" />
        <script src = "../../public/js/jquery-1.8.3/jquery.js"></script>
        <script src = "../../public/js/checkInputStr.js"></script>
    </head>
    <body>
        <div id = "shade-layer" class = "shadeLayer"></div>
        <!--content-->
        <div id = "userPageBox" class = "user-page-box">
            <div class = "action-box">
                <div class = "serch-user-box"></div>
                <div class = "add-user-box">
                    <button class = "show-add-box-btn" onclick = "showAddUserBox()">添加用户</button>
                </div>
            </div>
            <div id = "userList" class = "user-list-box"></div>
        </div>

        <!--edit user poplayer-->
        <div id = "editUserInfo" class = "edit-user-info" style="display:none">
            <p class = "pop-layer-title">编辑用户信息<span class = "close-poplayer" onclick="closePopLayer('editUserInfo')">&times;</span></p>
            <div class = "input-box">
                <input id="uid" style="display:none" value=""/>
                <label for="username" class = "label-name">用户名：<input id = "username" class = "edit-username-input" name = "username" type = "text" maxlength="15"/></label>

                <label for="nickname" class = "label-name">用户昵称：<input id = "nickname" type="text" name="nickname" maxlength="15" value=""></label>

                <label for="rolegroup" class = "label-name">操作权限：
                    <select id = "rolegroup" class = "edit-role-group-select" name = "rolegroup">
                        <option value="0">查询</option>
                        <option value="1">查询，出库</option>
                        <option value="2">查询，出库，入库</option>
                    </select>
                </label>

                <button id="save" class = "edit-save-btn" onclick="saveUserInfo()">保存</button>
            </div>
        </div>

        <!--add user poplayer-->
        <div id = "addUserInfo" class = "add-user-info" style="display:none">
                <p class = "pop-layer-title">添加用户<span class = "close-poplayer" onclick="closePopLayer('addUserInfo')">&times;</span></p>
                <div class = "input-box">
                <label for="usernameAdd" class = "label-name">用户名：<input id = "usernameAdd" class = "add-username-input" name = "usernameAdd" type = "text" maxlength="15"/></label>

                <label for="nicknameAdd" class = "label-name">用户昵称：<input id = "nicknameAdd" type="text" name="nicknameAdd" maxlength="15" value=""></label>

                <label for ="userpwd" class = "label-name">初始密码：<input id = "userpwd" name = "userpwd" type = "text" maxlength = "15" value = "123456" /></label>

                <label for="rolegroupAdd" class = "label-name">操作权限：
                    <select id = "rolegroupAdd" class = "add-role-group-select" name = "rolegroupAdd">
                        <option value="0">查询</option>
                        <option value="1">查询，出库</option>
                        <option value="2">查询，出库，入库</option>
                    </select>
                </label>
                <button class = "add-save-btn" onclick="addUserInfo()">保存</button>
            </div>
        </div>
    </body>
    <script src = "../../public/js/userManagement.js"></script>
</html>

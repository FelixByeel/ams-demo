/**
 * 用户管理
 */
window.onload = function () {
    let searchUser = {
        userName: "",
        userStatus: -1
    }
    let uri = "getUserListService.php";
    getUserList(uri, searchUser);
}

function getUserList(uri, searchUser) {
    $.post(
        uri,
        {
            "searchUser": searchUser
        },
        function (userData) {
            showUserList(userData);
        },
        'json'
    );
}

function showUserList(userData) {
    let userListDom = document.getElementById("userList");
    let tbStr = "<table class = 'user-list-table'>";
    tbStr += "<tr>";
    tbStr += "<th>用户名</th>";
    tbStr += "<th>昵称</th>";
    tbStr += "<th>操作权限</th>";
    tbStr += "<th>帐号状态</th>";
    tbStr += "<th>最后登录时间</th>";
    tbStr += "<th colspan = '2'>管理</th>"
    tbStr += "</tr>";

    for (var i = 0; i < userData.length; i++) {
        tbStr += "<tr>";
        tbStr += "<td>" + userData[i].username + "</td>";
        tbStr += "<td>" + userData[i].nick_name + "</td>";

        switch (userData[i].role_group) {
            case '99': tbStr += "<td>超级管理员</td>";
                break;
            case '0': tbStr += "<td>查询</td>";
                break;
            case '1': tbStr += "<td>查询，出库</td>";
                break;
            case '2': tbStr += "<td>查询，出库，入库</td>";
                break;
            default: tbStr += "<td>-</td>";
                break;
        }

        if ('1' == userData[i].is_enabled) {
            tbStr += "<td>启用</td>";
        } else {
            tbStr += "<td>禁用</td>";
        }

        if ('0' != userData[i].last_time) {
            tbStr += "<td>" + dateFormat(userData[i].last_time) + "</td>";
        } else {
            tbStr += "<td>-</td>";
        }

        tbStr += "<td>编辑</td>";
        tbStr += "<td>启用</td>";

        tbStr += "</tr>";
    }

    tbStr += "</table>";
    userListDom.innerHTML = tbStr;
}

function dateFormat($num) {
    $num = parseInt($num) * 1000;
    let dateObj = new Date($num);
    let year = dateObj.getFullYear();
    let month = dateObj.getMonth() + 1;
    month = month < 10 ? '0' + month : month;
    let day = dateObj.getDate();
    day = day < 10 ? '0' + day : day;
    return year + '-' + month + '-' + day;
}


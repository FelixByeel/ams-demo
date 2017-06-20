/**
 * 用户管理
 */
window.onload = function () {
    initUserList();
}

/**
 * initialize user list
 */
function initUserList() {
    let searchUser = {
        "userName": "",
        "userStatus": -1
    }
    let uri = "getUserListService.php";
    getUserList(uri, searchUser);
}

/**
 * 查询用户信息，以JSON格式返回用户列表。
 * @param {string} uri          获取数据的地址
 * @param {object} searchUser   需要发送的数据
 */
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

/**
 * 以table形式输出userData里的用户信息到浏览器
 * @param {object} userData
 */
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
            case '0': tbStr += "<td>查询</td>";
                break;
            case '1': tbStr += "<td>查询，出库</td>";
                break;
            case '2': tbStr += "<td>查询，出库，入库</td>";
                break;
            default: tbStr += "<td>-</td>";
                break;
        }

        if ('1' === userData[i].is_enabled) {
            tbStr += "<td>启用</td>";
        } else {
            tbStr += "<td>禁用</td>";
        }

        if ('0' !== userData[i].last_time) {
            tbStr += "<td>" + dateFormat(userData[i].last_time) + "</td>";
        } else {
            tbStr += "<td>-</td>";
        }

        tbStr += "<td><button onclick = 'editUser(\"" + userData[i].username + "\")'>编辑</button></td>";

        if ('1' === userData[i].is_enabled) {
            tbStr += "<td><button onclick = 'disableUser(\"" + userData[i].username + "\")'>禁用</button></td>";
        } else {
            tbStr += "<td><button onclick = 'enableUser(\"" + userData[i].username + "\")'>启用</button></td>";
        }

        tbStr += "</tr>";
    }

    tbStr += "</table>";
    userListDom.innerHTML = tbStr;
}

/**
 * 格式化Unix时间戳为‘0000-00-00’格式的日期。num可以为UNIX时间戳整数或者UNIX时间戳字符串
 * @param {string} num
 */
function dateFormat(num) {
    let dateNum = parseInt(num) * 1000;
    let dateObj = new Date(dateNum);
    let year = dateObj.getFullYear();
    let month = dateObj.getMonth() + 1;
    month = month < 10 ? '0' + month : month;
    let day = dateObj.getDate();
    day = day < 10 ? '0' + day : day;
    return year + '-' + month + '-' + day;
}

/**
 * call edit function
 * @param {string} username
 */
function editUser(username) {
    let searchUser = {
        "userName": username,
        "userStatus": -1
    }
    let uri = "getUserListService.php";

    $.post(
        uri,
        {
            "searchUser": searchUser
        },
        function (userData) {
            editUserAction(userData)
        },
        'json'
    );
}

/**
 * edit user's information
 * @param {object} userData
 */
function editUserAction(userData) {
    console.log("edit:");
    console.log(userData);
}

/**
 * enable user
 * @param {string} username
 */
function enableUser(username) {
    $.post(
        uri,
        {
            "searchUser": searchUser
        },
        function (userData) {

        },
        'json'
    );
}

/**
 * disable user
 * @param {string} username
 */
function disableUser(username) {
    console.log('disable:' + username);
}

function sendUserData(userData) {
    $.post(
        uri,
        {
            "updateUserInfo.php": userData
        },
        function (userData) {
            showUserList(userData);
        },
        'json'
    );
}

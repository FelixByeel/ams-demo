/**
 * 用户管理
 */
window.onload = function () {
    initUserList();
}

/**
 * initialize user list
 */
function initUserList()
{
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
function getUserList(uri, searchUser)
{
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
function showUserList(userData)
{
    let userListDom = document.getElementById("userList");
    let tbStr = "<table class = 'user-list-table'>";
    tbStr += "<tr class = 'table-title'>";
    tbStr += "<td>用户名</td>";
    tbStr += "<td>昵称</td>";
    tbStr += "<td>操作权限</td>";
    tbStr += "<td>帐号状态</td>";
    tbStr += "<td>最后登录时间</td>";
    tbStr += "<td colspan = '2'>管理</td>"
    tbStr += "</tr>";

    for (var i = 0; i < userData.length; i++) {
        if(i % 2) {
            tbStr += "<tr class = 'odd-row'>";
        } else {
            tbStr += "<tr class = 'even-row'>";
        }

        tbStr += "<td class = 'td-username td-content'>" + userData[i].username + "</td>";
        tbStr += "<td class = 'td-nickname td-content'>" + userData[i].nick_name + "</td>";

        switch (userData[i].role_group) {
            case '0': tbStr += "<td class = 'td-rolegroup td-content'>查询</td>";
                break;
            case '1': tbStr += "<td class = 'td-rolegroup td-content'>查询，出库</td>";
                break;
            case '2': tbStr += "<td class = 'td-rolegroup td-content'>查询，出库，入库</td>";
                break;
            default: tbStr += "<td class = 'td-rolegroup td-content'>-</td>";
                break;
        }

        if ('1' === userData[i].is_enabled) {
            tbStr += "<td class = 'td-enabled td-content'>启用</td>";
        } else {
            tbStr += "<td class = 'td-disabled td-content'>禁用</td>";
        }

        if ('0' !== userData[i].last_time) {
            tbStr += "<td class = 'td-lasttime td-content'>" + dateFormat(userData[i].last_time) + "</td>";
        } else {
            tbStr += "<td class = 'td-lasttime td-content'>-</td>";
        }

        tbStr += "<td class = 'td-btn td-content'><button onclick = 'editUser(\"" + userData[i].username + "\")'>编辑</button></td>";

        if ('1' === userData[i].is_enabled) {
            tbStr += "<td class = 'td-btn td-content'><button onclick = 'disableUser(\"" + userData[i].username + "\")'>禁用</button></td>";
        } else {
            tbStr += "<td class = 'td-btn td-content'><button onclick = 'enableUser(\"" + userData[i].username + "\")'>启用</button></td>";
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
function dateFormat(num)
{
    let dateNum = parseInt(num) * 1000;
    let dateObj = new Date(dateNum);
    let year = dateObj.getFullYear();
    let month = dateObj.getMonth() + 1;
    month = month < 10 ? '0' + month : month;
    let day = dateObj.getDate();
    day = day < 10 ? '0' + day : day;
    return year + '-' + month + '-' + day;
}

//------------------edit user start---------------------------

/**
 * set edit box default value
 * @param {string} username
 */
function editUser(username)
{
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
            $("#uid").val(userData[0].uid);
            $("#username").val(userData[0].username);
            $("#nickname").val(userData[0].nick_name);
            $("#rolegroup").val(userData[0].role_group);
            $("#editUserInfo").show();
        },
        'json'
    );
}

/**
 * save user's information
 */
function saveUserInfo()
{
    let uid = $("#uid").val();
    let username = $("#username").val();
    let nickname = $("#nickname").val();
    let rolegroup = $("#rolegroup").val();

    if (checkInputStr.isExistSpecialChar(uid)) {
        return;
    }

    username = checkInputStr.trimSpace(username);
    if (checkInputStr.isExistSpecialChar(username)) {
        alert("用户名不能含有字符：" + checkInputStr.isExistSpecialChar(username));
        return;
    }

    nickname = checkInputStr.trimSpace(nickname);
    if (checkInputStr.isExistSpecialChar(nickname)) {
        alert("昵称不能含有字符：" + checkInputStr.isExistSpecialChar(nickname));
        return;
    }

    if (!checkInputStr.isDigital(rolegroup)) {
        alert("请重新选择正确的操作权限！");
        return;
    }

    let userData = {
        "uid": uid,
        "username": username,
        "nickname": nickname,
        "rolegroup": rolegroup
    }

    $.post(
        "updateUserInfo.php",
        {
            "userData": userData
        },
        function (status) {
            alert(status.info);
            if (!status.status_id) {
                statusId = status.status_id;
                return;
            }
            initUserList();
            $("#editUserInfo").hide();
        },
        'json'
    );
}
//--edit user end

//------------------enable user start-------------------------
/**
 * enable user
 * @param {string} username
 */
function enableUser(username)
{
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
            let userInfo = {
                "uid": userData[0].uid,
                "is_enabled": 1
            }
            sendUserData(userInfo);
        },
        'json'
    );
}
//--enable user end

//------------------disable user start-------------------------
/**
 * disable user
 * @param {string} username
 */
function disableUser(username)
{
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
            let userInfo = {
                "uid": userData[0].uid,
                "is_enabled": 0
            }
            sendUserData(userInfo);
        },
        'json'
    );
}//--disable user end

/**
 * send update data
 * @param {object} userData
 */
function sendUserData(userData)
{
    $.post(
        "updateUserInfo.php",
        {
            "userData": userData
        },
        function () {
            initUserList();
        }
    );
}

/**
 * close pop-layer
 * @param {string} str
 */
function closePopLayer (str)
{
    if ("editUserInfo" === str) {
        $("#editUserInfo").hide();
    } else if ("addUserInfo" === str) {
        $("#addUserInfo").hide();
    }
}

//---------------add user start-------------------
function showAddUserBox() {
    $("#addUserInfo").show();
    $("#usernameAdd").val("");
    $("#nicknameAdd").val("");
    $("#userpwd").val("123456");
}

function addUserInfo() {
    //get input user data and check
    let username = $("#usernameAdd").val();
    let userpwd = $("#userpwd").val();
    let nickname = $("#nicknameAdd").val();
    let rolegroup = $("#rolegroupAdd").val();

    username = checkInputStr.trimSpace(username);
    if("" === username) {
        alert("用户名不能为空！");
        $("#usernameAdd").focus();
        return;
    }

    if(checkInputStr.isExistSpecialChar(username)) {
        alert("用户名中不能含有字符：" + checkInputStr.isExistSpecialChar(username));
        $("#usernameAdd").focus();
        return;
    }

    nickname = checkInputStr.trimSpace(nickname);
    if("" === nickname) {
        alert("昵称不能为空！");
        $("#nickname").focus();
        return;
    }

    if(checkInputStr.isExistSpecialChar(nickname)) {
        alert("用户名中不能含有字符：" + checkInputStr.isExistSpecialChar(nickname));
        $("#nicknameAdd").focus();
        return;
    }

    if("" === userpwd) {
        alert("用密码不能为空！");
        $("#userpwd").focus();
        return;
    }

    if(userpwd.length < 6) {
        alert("密码长度不能小于6位！");
        $("#userpwd").focus();
        return;
    }

    for(let i = 0; i < userpwd.length; i++) {
        if(userpwd.charCodeAt(i) < 0 || userpwd.charCodeAt(i) > 255) {
            alert("密码只能包含数字、英文字母、符号等字符。");
            $("#userpwd").focus();
            return;
        }
    }

    let userData = {
        "username":username,
        "nickname":nickname,
        "userpwd":userpwd,
        "rolegroup":rolegroup
    }

    $.post(
        "addUserService.php",
        {
            "userData":userData
        },
        function (status) {
            alert(status.info);
            if (!status.status_id) {
                return;
            }
            $("#addUserInfo").hide();
            initUserList();
        },
        "json"
    );
}

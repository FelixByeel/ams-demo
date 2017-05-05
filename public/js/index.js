//检测是否有选择“记住密码”，有则自动填充用户名和密码
window.onload = function () {
    var rememberStatus = getCookieValue("rememberpwd");

    if (1 == rememberStatus) {
        var userNameValue = getCookieValue("username");
        var userpwdValue = getCookieValue("userpwd");

        $("#username").val(userNameValue);
        $("#userpwd").val(userpwdValue);
        $("#rememberpwd").attr("checked", true);
    }
    else {
        $("#rememberpwd").attr("checked", false);
    }
    //$(":checkbox").on("change", function(){

    //});
}

//检查是否选择了“记住密码”，是，则保存登陆用户名和密码，否则清除用户名和密码。
function checkRememberStatus() {
    if ($("#rememberpwd").attr("checked")) {
        var userNameValue = $("#username").val();
        var userpwdValue = $("#userpwd").val();

        setCookie("username", userNameValue, 360, "/");
        setCookie("userpwd", userpwdValue, 360, "/");
        setCookie("rememberpwd", 1, 360, "/");
    }
    else {
        deleteCookie("username", "/");
        deleteCookie("userpwd", "/");
        deleteCookie("rememberpwd", "/");
    }
}

//login
function login_submit() {

    checkRememberStatus();

    var username = document.getElementById("username");
    var password = document.getElementById("userpwd");
    var errorinfoObj = document.getElementById("errorinfo");
    if ("" == username.value || "" == password.value) {
        errorinfoObj.innerHTML = "用户名或密码不能为空！";
        return false;
    }
    else {
        $("#errorinfo").load("app/login/loginAction.php", { "username": username.value, "userpwd": password.value });
    }
}

//响应Enter按键登录
document.onkeydown = function (event) {
    var e = event || window.event || arguments.callee.caller.arguments[0];
    if (e && e.keyCode == 13) {
        login_submit();
    }
}

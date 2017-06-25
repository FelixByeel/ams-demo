/**
 * 处理重置密码脚本
 */

$(document).ready(function () {
    $("#resetBtn").click(function () {
        let username = $("#usernameIpt").val();
        let userpwd = $("#userpwdIpt").val();

        if (username === "") {
            $("#tipsBox").text("用户名不能为空.");
            $("#usernameIpt").focus();
            return;
        }

        if (userpwd.length < 6) {
            $("#tipsBox").text("密码不能小于6位.");
            $("#userpwdIpt").focus();
            return;
        }

        sendPwd(username, userpwd);
    });
});

function sendPwd(username, userpwd) {
    $.post(
        "set-default-password-service.php",
        {
            "username": username,
            "userpwd": userpwd
        },
        function (status) {
            $("#tipsBox").text(status['info']);
        },
        'json'
    );
}

/**
 * Modify password
 */

$(document).ready(function () {
    $("#savePwdBtn").click(function () {
        let oldPwd = $("#oldPwdIpt").val();
        let newPwd = $("#newPwdIpt").val();
        let cfmPwd = $("#cfmPwdIpt").val();

        if (oldPwd.length === "") {
            $("#tipsBox").text("密码不能为空.");
            $("#newPwdIpt").focus();
            return;
        }

        if (newPwd.length < 6) {
            $("#tipsBox").text("密码不能小于6位.");
            $("#newPwdIpt").focus();
            return;
        }

        if (oldPwd === newPwd) {
            $("#tipsBox").text("新密码不能和旧密码相同.");
            $("#newPwdIpt").focus();
            return;
        }

        if (newPwd !== cfmPwd) {
            $("#tipsBox").text("确认密码不正确，请重新输入确认密码.");
            $("#cfmPwdIpt").focus();
            return;
        }

        sendPwd(oldPwd, newPwd);
    });
});

function sendPwd(oldPwd, newPwd) {
    $.post(
        "modify-password-service.php",
        {
            "oldPwd": oldPwd,
            "newPwd": newPwd
        },
        function (status) {
            console.log(status);
            if (1 === status['status_id']) {
                $("#modifyPwdBox").text(status['info']);
            } else {
                $("#tipsBox").text(status['info']);
            }
        },
        'json'
    );
}

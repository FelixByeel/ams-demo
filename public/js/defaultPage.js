//页面加载默认显示内容
window.onload = function () {
    loadAllPage();
}

//5分钟自动加载一次内容
var timer = setInterval("loadAllPage()", 300000);

//加载模块
function loadAllPage() {
    var recentCheckoutId = "recentCheckout";
    var recentCheckoutUrl = "app/defaultPageManagement/recentCheckout.php";
    loadPage(recentCheckoutId, recentCheckoutUrl);

    var itemCountWarningId = "itemCountWarning";
    var itemCountWarningUrl = "app/defaultPageManagement/itemCountWarning.php";
    loadPage(itemCountWarningId, itemCountWarningUrl);
}

function loadPage(contentId, pageUrl) {
    $("#" + contentId).load(pageUrl);
}

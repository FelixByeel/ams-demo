//页面加载默认显示内容
window.onload = function () {
    loadAllPage();
}

//5分钟自动加载一次内容
var timer = setInterval("loadAllPage()", 300000);

function loadAllPage() {

    //加载统计图表模块
    var checkoutChartId = "checkoutChart";
    var checkoutChartUrl = "app/defaultPageManagement/checkoutChart.php";
    var windowScreenHeight = window.screen.height;  //获取屏幕高度
    var data = {
        "windowScreenHeight": windowScreenHeight
    }
    loadChart(checkoutChartId, checkoutChartUrl, data);

    //加载最近出库记录模块
    var recentCheckoutId = "recentCheckout";
    var recentCheckoutUrl = "app/defaultPageManagement/recentCheckout.php";
    loadPage(recentCheckoutId, recentCheckoutUrl);

    //加载库存预警模块
    var itemCountWarningId = "itemCountWarning";
    var itemCountWarningUrl = "app/defaultPageManagement/itemCountWarning.php";
    loadPage(itemCountWarningId, itemCountWarningUrl);
}

/**
 * 用来加载各个对应模块。
 *
 * return void
 * @param {string} contentId    容器ID
 * @param {string} pageUrl      需要加载的模块的地址。
 * @param {object} data         传递给模块后台的参数
 */
function loadPage(contentId, pageUrl) {
    $("#" + contentId).load(pageUrl);
}

function selectChange(){
    //加载统计图表模块
    var checkoutChartId = "checkoutChart";
    var checkoutChartUrl = "app/defaultPageManagement/checkoutChart.php";
    var windowScreenHeight = window.screen.height;
    var itemSelectValue = $("#itemSelect").val();
    var data = {
        "windowScreenHeight": windowScreenHeight,
        "itemSelectValue": itemSelectValue,
    }
    loadChart(checkoutChartId, checkoutChartUrl, data);
}


function loadChart(checkoutChartId, checkoutChartUrl, data) {

    $("#" + checkoutChartId).load(checkoutChartUrl, { "data": data }, function(){
        var selectObj = document.getElementById("itemSelect");
        selectObj.addEventListener("change", function(){
            selectChange();
        });
    });
}

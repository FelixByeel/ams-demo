//页面加载默认显示内容
window.onload = function () {
    loadAllPage();
}

//5分钟自动加载一次内容
var timer = setInterval("loadAllPage()", 60000);

function loadAllPage() {

    //加载统计图表模块
    var checkoutChartId = "itemSelectBox";
    var checkoutChartUrl = "app/defaultPageManagement/itemSelect.php";
    var windowScreenHeight = window.screen.height;                     //获取屏幕高度
    var windowScreenWidth = window.screen.width;
    var itemSelectObj = document.getElementById("itemSelect");    //获取select对象
    var itemSelectValue = 0;

    if (itemSelectObj != null) { //select对象存在时，获取select的值
        var index = itemSelectObj.selectedIndex;
        itemSelectValue = itemSelectObj.options[index].value;
    }

    var data = {
        "windowScreenHeight": windowScreenHeight,
        "windowScreenWidth": windowScreenWidth,
        "itemSelectValue": itemSelectValue
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

function selectChange() {
    //加载统计图表模块
    var checkoutChartId = "itemSelectBox";
    var checkoutChartUrl = "app/defaultPageManagement/itemSelect.php";
    var windowScreenHeight = window.screen.height;
    var windowScreenWidth = window.screen.width;
    var itemSelectValue = $("#itemSelect").val();
    var data = {
        "windowScreenHeight": windowScreenHeight,
        "windowScreenWidth": windowScreenWidth,
        "itemSelectValue": itemSelectValue
    }
    loadChart(checkoutChartId, checkoutChartUrl, data);
}

function loadChart(checkoutChartId, checkoutChartUrl, data) {
    //加载select列表
    $("#" + checkoutChartId).load(checkoutChartUrl, { "data": data }, function () {
        var selectObj = document.getElementById("itemSelect");
        //给新的select添加change事件
        selectObj.addEventListener("change", function () {
            selectChange();
        });
        //加载图表。先调用生成图表的页面，输出图表到路径：public/images/checkoutChart/checkoutChart.png，然后在把上述路径图表加载到html的img里面
        $(document).load("app/defaultPageManagement/checkoutChart.php", { "data": data }, function () {
            document.getElementById("checkoutChart").src = "public/images/checkoutChart/checkoutChart.png?" + Math.random();
        });
    });
}

//页面加载初始化，默认显示所有分类信息，实现无限分类菜单
var warehouseListBox;
var warehouseJSON;


window.onload = function () {

    initData();
}

function initData(){

    warehouseListBoxObj = document.getElementById('warehouseListBox');      //获取加载li列表的ul对象
    warehouseListBoxObj.innerHTML = "";
    loadAjaxGetData(warehouseListBoxObj);
}

//ajax()方法加载分类数据,返回成功调用showItemInfo()初始化分类显示
function loadAjaxGetData(warehouseListBoxObj) {

    $.ajax({
        type: "post",
        url: "searchItemService.php",
        data: { "tableName": "warehouse" },
        dataType: "json",
        cache: false,
        success: function (data) {
            warehouseJSON = data;
        },
        error: function (msg, e) {
            alert("请求的数据发生异常：" + e);
        }
    });
}


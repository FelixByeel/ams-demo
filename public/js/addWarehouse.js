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

//ajax()方法加载分类数据,返回成功调用loadWarehouseInfo()初始化分类显示
function loadAjaxGetData(warehouseListBoxObj) {

    $.ajax({
        type: "post",
        url: "../itemManagement/searchItemService.php",
        data: { "tableName": "warehouse" },
        dataType: "json",
        cache: false,
        success: function (data) {
            warehouseJSON = data;
            loadWarehouseInfo(warehouseListBoxObj);
        },
        error: function (msg, e) {
            alert("请求的数据发生异常：" + e);
        }
    });


}

//加载仓库信息到页面
function loadWarehouseInfo(warehouseListBoxObj) {
    
    let tableObj = document.createElement('table');

    tableObj.id = "warehouseTable";

    for(let i = 0; i < warehouseJSON.length; i++){
        let trObj = tableObj.insertRow();

        trObj.insertCell(0).innerHTML = warehouseJSON[i].warehouse_name;
        trObj.insertCell(1).innerHTML = "<button id = 'warehouseID_" + warehouseJSON[i].warehouse_id + "' onclick = 'editWarehouseName(" + warehouseJSON[i].warehouse_id + "'>编辑</button>";
        trObj.insertCell(2).innerHTML = "<button id = 'warehouseID_" + warehouseJSON[i].warehouse_id + "' onclick = 'delWarehouseName(" + warehouseJSON[i].warehouse_id + "'>删除</button>";
    }
    warehouseListBoxObj.innerHTML = '';
    warehouseListBoxObj.appendChild(tableObj);
}

function editWarehouseName(warehouseID) {
    
}

function delWarehouseName(warehouseID) {
    
}

//添加仓库
function addWarehouse(){
    let inputObj = document.getElementById('warehouseInput');
    let warehouseName  = inputObj.value.replace(/(^\s*)|(\s*$)/g, "");;

    let str = checkInput(warehouseName);

    if(str){
        alert("输入名称不能包含字符：" + str);
        inputObj.focus();
        return false;
    }

    $.post(
        "addWarehouseService.php",
        { "warehouseName": warehouseName },
        function (msg) {
            alert(msg);
        }
    );
}

//判断字符串是否合法，合法返回true，不合法返回false------------
function checkInput(str) {

        //不接受下列字符的输入
        let specialCharacter = new Array('~', '`', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-',
            '=', '+', '[', ']', '{', '}', '\\', '|', ';', ':', '\'', '\"', ',', '<', '.', '>', '?',
            '~', '·', '！', '＠', '＃', '￥', '％', '………', '＆', '＊', '（', '）', '——', '＋', '＝',
            '【', '】', '｛', '｝', '、', '｜', '；', '：', '’', '“', '，', '《', '。', '》', '？', ' ', '　');

        let flag = false;
        let i = 0;

        for (; i < specialCharacter.length; i++) {
            if(-1 != str.indexOf(specialCharacter[i])) {
                flag =  true;
                break;
            }
        }
    
    if(flag){
        return specialCharacter[i];
    }
    else return flag;
}

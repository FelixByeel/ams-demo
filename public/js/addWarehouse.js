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
        url: "../itemManagement/getItemService.php",
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
        trObj.insertCell(1).innerHTML = "<button id = 'warehouseID_" + warehouseJSON[i].warehouse_id + "' onclick = 'editWarehouseName(" + warehouseJSON[i].warehouse_id + ")'>编辑</button>";
        trObj.insertCell(2).innerHTML = "<button id = 'warehouseID_" + warehouseJSON[i].warehouse_id + "' onclick = 'delWarehouseName(" + warehouseJSON[i].warehouse_id + ")'>删除</button>";
    }
    warehouseListBoxObj.innerHTML = '';
    warehouseListBoxObj.appendChild(tableObj);
}

//----编辑-----
function editWarehouseName(warehouseID) {
    let warehouseName = '';

    for(let i = 0; i < warehouseJSON.length; i++){
        if(warehouseID == warehouseJSON[i].warehouse_id) {
            warehouseName  = warehouseJSON[i].warehouse_name;
            break;
        }
    }

    document.getElementById('warehouseCodeSpan').innerHTML = warehouseID;
    document.getElementById('editWarehouseInput').value = warehouseName;

    //显示遮罩层
    document.getElementById('shadeBox').style.width = '100%';
    document.getElementById('shadeBox').style.height = '100%';

    document.getElementById('editWarehousePopLayer').style.display = 'block'; //弹出编辑界面
    
}

//-----删除--------
function delWarehouseName(warehouseID) {
    let isNumberReg = /^[1-9]+[0-9]*]*$/;
    //warehouseID = warehouseID.replace(/(^\s*)|(\s*$)/g, "");
    let conf = confirm("确认删除？");

    if(!isNumberReg.test(warehouseID)){
        alert("操作异常！");
    }
    else if(conf){
        $.post(
            "delWarehouseService.php",
            { "warehouseID": warehouseID },
            function (msg) {
                alert(msg);
                initData();
            }
        );
    }
}

//添加仓库
function addWarehouse(){
    let inputObj = document.getElementById('warehouseInput');
    let warehouseName  = inputObj.value.replace(/(^\s*)|(\s*$)/g, "");

    let str = checkInput(warehouseName);

    if(warehouseName.length == 0) {
        alert("仓库名称不能为空！");
        return false;
    }

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
            initData();
            document.getElementById('warehouseInput').value = '';
            document.getElementById('warehouseInput').focus();
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

//弹出层更新动作
function updateWarehouse(){
    let warehouseID = document.getElementById('warehouseCodeSpan').innerHTML;
    let warehouseName =  document.getElementById('editWarehouseInput').value;

    let isNumberReg = /^[1-9]+[0-9]*]*$/;
    warehouseName  = warehouseName.replace(/(^\s*)|(\s*$)/g, "");

    let str = checkInput(warehouseName);

    if(!isNumberReg.test(warehouseID)){
        alert("操作异常！");
        return false;
    }

    if(warehouseName.length == 0) {
        alert("仓库名称不能为空！");
        return false;
    }

    if(str){
        alert("输入名称不能包含字符：" + str);
        document.getElementById('editWarehouseInput').focus();
        return false;
    }

    $.post(
        "updateWarehouseService.php",
        { 
            "warehouseID": warehouseID,
            "warehouseName": warehouseName 
        },
        function (msg) {
            alert(msg);
            initData();
        }
    );

    //关闭弹出层，关闭遮罩层
    document.getElementById('shadeBox').style.width = '0';
    document.getElementById('shadeBox').style.height = '0';
    document.getElementById('editWarehousePopLayer').style.display = 'none';
}

//关闭弹出层，关闭遮罩层
function closePopLayer(){
    document.getElementById('shadeBox').style.width = '0';
    document.getElementById('shadeBox').style.height = '0';
    document.getElementById('editWarehousePopLayer').style.display = 'none';
}

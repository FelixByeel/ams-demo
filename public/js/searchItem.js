//页面加载初始化部分数据
window.onload = function(){

    loadWarehouse();
    loadItem();
}

//加载仓库信息
function loadWarehouse(){
    //定义查询条件
    let warehouseSearch = {
        "tabelName" : "warehouse_t",
        "columnArray" : Array("warehouse_id", "warehouse_name")
    };

    //ajax()方法,返回成功调用showWarehouseCallback
    $.ajax({
        type:"post",
        url : "selectTabelService.php",
        data : sendData,
        dataType : JSON,
        cache: false,
        success : getCallbackData
    });
}

function getCallbackData(data){
    let warehouseJSON = JSON.parse(data);
    
}

function loadItem(){
    let itemSearch = {
        "tabelName" : "item_t",
        "columnArray" : Array("*"),
        "conditionStr" : ''
    };
}

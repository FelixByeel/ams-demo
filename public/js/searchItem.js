//页面加载初始化，默认显示所有分类信息，实现无限分类菜单
window.onload = function(){

    let searchCondition = {'hello': "world"};                       //查询条件
    let itemMenuDivObj  = document.getElementById('itemMenu');      //获取加载li列表的ul对象

    let Global_warehouseJSON;
    let Global_itemJSON;

    loadAjaxGetData(itemMenuDivObj, searchCondition);

}

//ajax()方法加载分类数据,返回成功调用showItemInfo()初始化分类显示
function loadAjaxGetData(itemMenuDivObj, searchCondition){
    $.ajax({
        type:"post",
        url : "searchItemService.php",
        data : "item",
        dataType : "json",
        cache: false,
        success : function(itemJSON){
            Global_itemJSON = itemJSON;
            showItemInfo(itemMenuDivObj, itemJSON, 0);
            loadAllEndedItems(itemMenuDivObj, itemJSON);
        },
        error : function(msg,e){

            alert( "请求的数据发生异常：" + e);
        }
    });

    $.ajax({
        type:"post",
        url : "searchItemService.php",
        data : "warehouse",
        dataType : "json",
        cache: false,
        success : function(warehouseJSON){
            Global_warehouseJSON = warehouseJSON;
        },
        error : function(msg,e){
            alert( "请求的数据发生异常：" + e);
        }
    });
}

//显示分类菜单
function showItemInfo(itemMenuDivObj, itemJSON, currentSelectedId){

    let ulFlag = 1; //当前项有子项时才创建子项的容器ul，防止创建空的<ul></ul>标签
    let itemUlObj;

    for(let i = 0, j = 0; i< itemJSON.length; i++){
        if((currentSelectedId == itemJSON[i].parent_id) && (itemJSON[i].is_ended != 1)){
            //没有子分类的项不显示
            if(ulFlag){
                    itemUlObj = document.createElement("ul");
                    ulFlag = 0;
            }

            let itemLiObj = document.createElement("li");

            itemLiObj.id = "itemLiId_" + itemJSON[i].id;
            itemLiObj.innerHTML = itemJSON[i].item_name;

            itemLiObj.addEventListener("click", function(e){

                itemClicked(this, itemMenuDivObj, itemJSON);
                e.stopPropagation();    //阻止事件冒泡，点击内层li不会触发上层li的click事件
            });

            itemUlObj.id = "itemUlId_" + currentSelectedId;
            itemUlObj.appendChild(itemLiObj);
        }
        else if((currentSelectedId == itemJSON[i].parent_id) && (1 == itemJSON[i].is_ended)){
            showCurrentSelectedDetail(itemMenuDivObj, itemJSON, currentSelectedId);
        }
    }

    if(itemUlObj){
        itemMenuDivObj.appendChild(itemUlObj);
    }
}

//菜单click事件
function itemClicked(currentClickLi, itemMenuDivObj, itemJSON) {
    let currentSelectedId = currentClickLi.id.split("_")[1];

    //检测当前点击菜单项的子菜单是否存在
    if(checkSubItem(currentSelectedId)){
        itemMenuDivObj = document.getElementById("itemLiId_" + currentSelectedId);
        showItemInfo(itemMenuDivObj, itemJSON, currentSelectedId);
    }
}

//检测当前点击菜单项的子菜单是否存在
function checkSubItem(currentSelectedId){

    let ulListObj = document.getElementById("itemMenu").getElementsByTagName("ul");

    for(let i = 0; i < ulListObj.length; i++){
        if((ulListObj[i].id.split("_")[1]) == currentSelectedId){
            let currentUl = "itemUlId_" + currentSelectedId
            $("#" + currentUl).remove();
            return false;
        }
    }
    return true;
}

//显示当前分类下的详细信息
function showCurrentSelectedDetail(itemMenuDivObj, itemJSON, currentSelectedId) {

    let itemDetailDivObj = document.getElementById("itemDetail");
    let tableObj = document.createElement("table");
    let trObj = tableObj.insertRow();

    tableObj.id = "showDetailTable_" + currentSelectedId;
    trObj.id = "trHead";
    trObj.insertCell(0).innerHTML = "分类名称";
    trObj.insertCell(1).innerHTML = "所属仓库";
    trObj.insertCell(2).innerHTML = "数量";
    trObj.insertCell(3).innerHTML = "操作";

    for(let i = 0, j = 0; i < itemJSON.length; i++){
        if((currentSelectedId == itemJSON[i].parent_id) && (1 == itemJSON[i].is_ended)){
            let trObj = tableObj.insertRow();
            if(j % 2){
                trObj.className = "row_odd";
            }
            else{
                trObj.className = "row_even";
            }
            trObj.insertCell(0).innerHTML = itemJSON[i].item_name;
            trObj.insertCell(1).innerHTML = itemJSON[i].warehouse_id;
            trObj.insertCell(2).innerHTML = itemJSON[i].item_count;
            trObj.insertCell(3).innerHTML = "<button id = 'edit' onclick = 'editItem(" + itemJSON[i].id + ")'>编辑</button>";

            j++;
        }
    }
    itemDetailDivObj.innerHTML = "";
    itemDetailDivObj.appendChild(tableObj);
}

//页面加载时显示详细信息
function loadAllEndedItems(itemMenuDivObj, itemJSON) {

    let itemDetailDivObj = document.getElementById("itemDetail");
    let tableObj = document.createElement("table");
    let trObj = tableObj.insertRow();

    tableObj.id = "showDetailTable_allItems";
    trObj.id = "trHead";
    trObj.insertCell(0).innerHTML = "分类名称";
    trObj.insertCell(1).innerHTML = "所属仓库";
    trObj.insertCell(2).innerHTML = "数量";
    trObj.insertCell(3).innerHTML = " ";

    for(let i = 0, j = 0; i < itemJSON.length; i++){
        if(1 == itemJSON[i].is_ended){
            let trObj = tableObj.insertRow();
            if(j % 2){
                trObj.className = "row_odd";
            }
            else{
                trObj.className = "row_even";
            }
            trObj.insertCell(0).innerHTML = itemJSON[i].item_name;
            trObj.insertCell(1).innerHTML = itemJSON[i].warehouse_id;
            trObj.insertCell(2).innerHTML = itemJSON[i].item_count;
            trObj.insertCell(3).innerHTML = "<button id = 'edit' onclick = 'editItem(" + itemJSON[i].id + ")'>编辑</button>";

            j++;
        }
    }
    itemDetailDivObj.innerHTML = "";
    itemDetailDivObj.appendChild(tableObj);
}

//编辑操作
function editItem(currentSelectedId){

    $("#editBox").show();
    $("#codeSpan").text(currentSelectedId);

    let i = 0;

    for(; i < Global_itemJSON.length; i++){
        if(Global_itemJSON[i].id == currentSelectedId){
            break;
        }
    }
    console.log(Global_itemJSON[i]);
    $("#nameInput").val(Global_itemJSON[i].item_name);
    $("#countInput").val(Global_itemJSON[i].item_count);


}
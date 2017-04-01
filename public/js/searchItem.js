//页面加载初始化，默认显示所有分类信息，实现无限分类菜单
window.onload = function(){

    let searchCondition = {'hello': "world"};                   //查询条件
    let itemMenuDivObj     = document.getElementById('itemMenu');    //获取加载li列表的ul对象

    loadAjaxGetData(itemMenuDivObj, searchCondition);

}

//ajax()方法加载分类数据,返回成功调用showItemInfo()初始化分类显示
function loadAjaxGetData(itemMenuDivObj, searchCondition){
    $.ajax({
        type:"post",
        url : "searchItemService.php",
        data : searchCondition,
        dataType : "json",
        cache: false,
        success : function(itemJSON){

            showItemInfo(itemMenuDivObj, itemJSON, 0);
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
            showCurrentSelectedDetail(currentSelectedId, itemJSON);
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
    //alert("测试click事件");
}

//检测当前点击菜单项的子菜单是否存在
function checkSubItem(currentSelectedId){

    let ulListObj = document.getElementById("itemMenu").getElementsByTagName("ul");

    for(let i = 0; i < ulListObj.length; i++){
        if((ulListObj[i].id.split("_")[1]) == currentSelectedId){
            ulListObj[i].remove();
            return false;
        }
    }
    return true;
}

function showCurrentSelectedDetail(currentSelectedId, itemJSON) {
    let itemDetailDivObj = document.getElementById("itemDetail");
    let str = "";
    for(let i = 0; i < itemJSON.length; i++){
        if(currentSelectedId == itemJSON[i].parent_id){
            str += "<td>" + itemJSON[i].item_name + "</td>";
        }
    }
    itemDetailDivObj.innerHTML = str;
}
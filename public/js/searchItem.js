//页面加载初始化，默认显示所有分类信息，实现无限分类菜单
window.onload = function(){

    let searchCondition = {'hello': "world"};                   //查询条件
    let itemMenuObj     = document.getElementById('itemList');    //获取加载li列表的ul对象

    loadAjaxGetData(itemMenuObj, searchCondition);

}

//ajax()方法加载分类数据,返回成功调用showItemInfo()初始化分类显示
function loadAjaxGetData(itemMenuObj, searchCondition){
    $.ajax({
        type:"post",
        url : "searchItemService.php",
        data : searchCondition,
        dataType : "json",
        cache: false,
        success : function(itemJSON){

            showItemInfo(itemMenuObj, itemJSON, 0);
        },
        error : function(msg,e){

            alert( "请求的数据发生异常：" + e);
        }
    });
}

//显示分类菜单
function showItemInfo(itemMenuObj, itemJSON, currentSelectedId){

    let ulFlag = 1;
    let itemUlObj;

    for(let i = 0, j = 0; i< itemJSON.length; i++){
        if(currentSelectedId == itemJSON[i].parent_id){
            if(ulFlag){
                    itemUlObj = document.createElement("ul");
                    ulFlag = 0;
            }

            let itemLiObj = document.createElement("li");

            itemLiObj.id = "itemLiId_" + itemJSON[i].id;
            itemLiObj.innerHTML = itemJSON[i].item_name;

            itemLiObj.addEventListener("click", function(e){
                itemClicked(this, itemMenuObj, itemJSON);
                e.stopPropagation();
            });

            itemUlObj.id = "itemUlId_" + currentSelectedId;
            itemUlObj.appendChild(itemLiObj);
        }
    }

    if(itemUlObj){
        itemMenuObj.appendChild(itemUlObj);
    }

}

//分类菜单click事件
function itemClicked(currentClickLi, itemMenuObj, itemJSON) {
    let currentSelectedId = currentClickLi.id.split("_")[1];

    //检测当前点击菜单项的子菜单是否存在
    if(checkSubItem(currentSelectedId)){
        itemMenuObj = document.getElementById("itemLiId_" + currentSelectedId);
        showItemInfo(itemMenuObj, itemJSON, currentSelectedId);
    }
    //alert("测试click事件");
}

//检测当前点击菜单项的子菜单是否存在
function checkSubItem(currentSelectedId){

    let ulListObj = document.getElementById("itemList").getElementsByTagName("ul");

    for(let i = 0; i < ulListObj.length; i++){
        if((ulListObj[i].id.split("_")[1]) == currentSelectedId){
            return false;
        }
    }
    return true;
}
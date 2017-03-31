//页面加载初始化，默认显示所有分类信息
window.onload = function(){

    let searchCondition = '';   //查询条件

    loadItem(searchCondition);
}

//加载分类数据，ajax()方法,返回成功调用showWarehouseCallback
function loadItem(searchCondition){

    $.ajax({
        type:"post",
        url : "selectTabelService.php",
        data : searchCondition,
        dataType : JSON,
        cache: false,
        success : showItemInfo
    });
}

//显示分类信息
function showItemInfo(itemJSON){

    let itemJSON = JSON.parse(itemJSON);

    let itemListObj    = document.getElementById('itemList');
    let itemDetailObj = document.getElementById('itemDetail');

    let itemUlObj = new Array();

    for(let i = 0, j = 0; i< itemJSON.length; i++){
        if(0 == itemJSON[i].parent_id){
            itemUlObj[j] = document.createElement("ul");
            itemUlObj[j].id = "classOne_" + itemJSON[i].id;
            j++;
        }
    }


    for(let i = 0; i < itemJSON.length; i++){
        itemLiObj.id = "item_" + itemJSON[i].id;
        itemLiObj.innerHTML = itemJSON[i].item_name;
        itemUlObj.appendChild(itemLiObj);
    }
}


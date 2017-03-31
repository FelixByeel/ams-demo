//页面加载初始化，默认显示所有分类信息
window.onload = function(){

    let searchCondition = '';   //查询条件
    let itemMenuObj    = document.getElementById('itemUl');

    //ajax()方法加载分类数据,返回成功调用showItemInfo()初始化分类显示
    $.ajax({
        type:"post",
        url : "selectTabelService.php",
        data : searchCondition,
        dataType : JSON,
        cache: false,
        success : function(itemJSON){

            itemJSON = JSON.parse(itemJSON);
            showItemInfo(itemMenuObj, itemJSON, 0);
        }
    });
}

//显示分类菜单
function showItemInfo(itemMenuObj, itemJSON, currentSelectedId){

    for(let i = 0, j = 0; i< itemJSON.length; i++){
        if(currentSelectedId == itemJSON[i].parent_id){
            let itemLiObj = document.createElement("li");

            itemLiObj.id = "itemMenuId_" + itemJSON[i].id;
            itemLiObj.className = "itemMenuClass_" + itemJSON[i].parent_id;
            itemLiObj.innerHTML = itemJSON[i].item_name;

            itemLiObj.addEventListener("click", function(){
                itemClicked(this, itemMenuObj, itemJSON);
            });

            itemMenuObj.appenChlid(itemLiObj);
        }
    }
}

//菜单click事件
function itemClicked(currentClickLi, itemMenuObj, itemJSON) {
    
}


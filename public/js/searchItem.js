//页面加载初始化，默认显示所有分类信息
window.onload = function(){

    let searchCondition = '';   //查询条件
    let itemMenuObj    = document.getElementById('itemUl');

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
        error : function(){
            alert("无法从服务器获取数据！");
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
    alert("测试");
}


//页面加载初始化，默认显示所有分类信息，实现无限分类菜单
window.onload = function(){

    let searchCondition = {'hello': "world"};                   //查询条件
    let itemMenuObj     = document.getElementById('itemUl');    //获取加载li列表的ul对象

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

    for(let i = 0, j = 0; i< itemJSON.length; i++){
        if(currentSelectedId == itemJSON[i].parent_id){
            let itemLiObj = document.createElement("li");

            itemLiObj.id = "itemMenuId_" + itemJSON[i].id;
            itemLiObj.className = "itemMenuClass_" + itemJSON[i].parent_id;
            itemLiObj.innerHTML = itemJSON[i].item_name;

            itemLiObj.addEventListener("click", function(){
                itemClicked(this, itemMenuObj, itemJSON);
            });
            itemMenuObj.appendChild(itemLiObj);
        }
    }
}

//分类菜单click事件
function itemClicked(currentClickLi, itemMenuObj, itemJSON) {
    alert("测试click事件");
}


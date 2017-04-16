//页面加载初始化，默认显示所有分类信息，实现无限分类菜单
var itemMenuObj;
var warehouseJSON;
var itemJSON;

window.onload = function () {

    initData();
}

//初始化内容
function initData() {

    itemMenuObj = document.getElementById('itemMenuDiv');      //获取加载li列表的ul对象
    itemMenuObj.innerHTML = "";

    //初始化仓库信息
    $.ajax({
        type: "post",
        url: "getItemService.php",
        data: { "tableName": "warehouse" },
        dataType: "json",
        cache: false,
        success: function (warehouseJSON_s) {
            warehouseJSON = warehouseJSON_s;

            initWarehouseSpan(warehouseJSON);
            loadAjaxGetData(itemMenuObj);
        },
        error: function (msg, e) {
            alert("请求的数据发生异常：" + e);
        }
    });

}

//ajax()方法加载分类数据,返回成功调用showItemInfo()初始化分类显示
function loadAjaxGetData(itemMenuObj) {

    $.ajax({
        type: "post",
        url: "getItemService.php",
        data: { "tableName": "item" },
        dataType: "json",
        cache: false,
        success: function (itemJSON_s) {
            itemJSON = itemJSON_s;

            initItemSelect(itemJSON);
            showItemInfo(itemMenuObj, itemJSON, 0);
            loadAllEndedItems(itemJSON);
        },
        error: function (msg, e) {
            alert("请求的数据发生异常：" + e);
        }
    });
}

//显示分类菜单
function showItemInfo(itemMenuObj, itemJSON, currentSelectedId) {

    let ulFlag = 1; //当前项有子项时才创建子项的容器ul，防止创建空的<ul></ul>标签
    let itemUlObj;

    for (let i = 0, j = 0; i < itemJSON.length; i++) {
        if ((currentSelectedId == itemJSON[i].parent_id) && (itemJSON[i].is_ended == 0)) {
            if (ulFlag) {
                itemUlObj = document.createElement("ul");
                ulFlag = 0;
            }

            let itemLiObj = document.createElement("li");

            itemLiObj.id = "itemLiId_" + itemJSON[i].id;
            itemLiObj.innerHTML = "<span>" + itemJSON[i].item_name + "</span>";

            itemLiObj.addEventListener("click", function (e) {

                itemClicked(this, itemJSON);
                e.stopPropagation();    //阻止事件冒泡，点击内层li不会触发上层li的click事件
            });

            itemUlObj.id = "itemUlId_" + currentSelectedId;
            itemUlObj.appendChild(itemLiObj);
        }
        else if ((currentSelectedId == itemJSON[i].parent_id) && (itemJSON[i].is_ended == 1)) {
            showCurrentSelectedDetail(itemJSON, currentSelectedId);
        }
    }

    if (itemUlObj) {
        itemMenuObj.appendChild(itemUlObj);
    }
}

//菜单click事件
function itemClicked(currentClickLi, itemJSON) {
    let currentSelectedId = currentClickLi.id.split("_")[1];
    let itemMenuObj = document.getElementById("itemLiId_" + currentSelectedId);
    let itemMenuDivObj = document.getElementById('itemMenuDiv');
    //检测当前点击菜单项的子菜单是否存在
    if (checkSubItem(currentSelectedId)) {

        showItemInfo(itemMenuObj, itemJSON, currentSelectedId);
    }
}

//检测当前点击菜单项的子菜单是否存在,没有返回true,有返回false
function checkSubItem(currentSelectedId) {

    let ulListObj = document.getElementById("itemMenuDiv").getElementsByTagName("ul");

    for (let i = 0; i < ulListObj.length; i++) {
        if ((ulListObj[i].id.split("_")[1]) == currentSelectedId) {
            let currentUl = "itemUlId_" + currentSelectedId;
            $("#" + currentUl).remove();
            return false;
        }
    }
    return true;
}

//判断字符串是否合法，合法返回true，不合法返回false------------
function checkInput(content, flag) {

    let isNumberReg = /^[1-9]+[0-9]*]*$/;

    if (1 == flag && (!isNumberReg.test(content))) {

        return false;
    }
    else if (0 == flag) {

        let is_existC = 0;            //is_existC用来跳出双层FOR循环
        //不接受下列字符的输入
        let specialCharacter = new Array('~', '`', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-',
            '=', '+', '[', ']', '{', '}', '\\', '|', ';', ':', '\'', '\"', ',', '<', '.', '>', '?',
            '~', '·', '！', '＠', '＃', '￥', '％', '………', '＆', '＊', '（', '）', '——', '＋', '＝',
            '【', '】', '｛', '｝', '、', '｜', '；', '：', '’', '“', '，', '《', '。', '》', '？', ' ', '　');

        for (let i = 0; i < content.length; i++) {
            for (let j = 0; j < specialCharacter.length; j++) {
                if (content[i] == specialCharacter[j]) {
                    is_existC = 1;
                    if ((' ' == specialCharacter[j]) || ('　' == specialCharacter[j])) {
                        alert("分类名称中不能含有空格！");
                    }
                    else {
                        alert("分类名称中不能含有字符：" + specialCharacter[j]);
                    }

                    return false;
                }
            }

            if (is_existC) return false;
        }

        if (is_existC) return false;
        else return true;
    }
    else {
        return true;
    }
}

//分类选择列表初始化。
function initItemSelect(itemJSON) {
    let itemSelectObj = document.getElementById('searchItemNameSelect');
    $('#searchItemNameSelect').empty();
    let optionObj = document.createElement('option');
    optionObj.value = '0';
    optionObj.text = "无";

    itemSelectObj.appendChild(optionObj);

    for(let i = 0; i < itemJSON.length; i++){
        if(0 == itemJSON[i].is_ended){
            let optionObj = document.createElement('option');
            optionObj.value = itemJSON[i].id;
            optionObj.text = itemJSON[i].item_name;

            itemSelectObj.appendChild(optionObj);
        }
    }
}

//仓库选择列表初始化
function initWarehouseSpan(warehouseJSON) {
    let warehouseSpanObj = document.getElementById('searchWarehouseNameSpan');

    let str = '';

    for(let i = 0; i < warehouseJSON.length; i++){
        str += "<label><input id = 'warehouse_" + i + "' name = 'warehouseName' type = 'checkbox' value = '" + warehouseJSON[i].warehouse_id +"' />"+ warehouseJSON[i].warehouse_name + "</label>";
    }

    if(!str.length){
        str = '未查询到仓库信息，请先添加仓库信息！';
    }
    warehouseSpanObj.innerHTML = '';
    warehouseSpanObj.innerHTML = str;
}

//--------------------------搜索----------------------------------------

//点击搜索按钮
$("#searchButton").click(function(){
    getSearchItemResult(getSearchItemCondition());
});

//获取输入的搜索条件，并return整合后的条件对象
function getSearchItemCondition() {
    //获取输入的ID
    let itemID = $("#searchItemIDInput").val();
    itemID = itemID.replace(/(^\s*)|(\s*$)/g, "");

    //获取选择的上级分类
    let itemParentID = $("#searchItemNameSelect option:selected").val();

    //获取输入的分类名
    let itemName = $("#searchItemNameInput").val();
    itemName = itemName.replace(/(^\s*)|(\s*$)/g, "");

    //获取仓库信息
    let warehouseCheckInputObj = document.getElementsByName("warehouseName");
    let warehouseCheckInputValueArray = new Array();
    for(let i = 0; i < warehouseCheckInputObj.length; i++) {
        if(warehouseCheckInputObj[i].checked) {
            warehouseCheckInputValueArray.push(warehouseCheckInputObj[i].value);
        }
    }

    //验证用户输入的数据
    if(0 != itemID.length){
        if(!checkInput(itemID, 1)){
            alert("分类编号只能为数字或空！");
            $("#searchItemIDInput").val('');
            $("#searchItemIDInput").focus();
            return false;
        }
    }
    else{
        itemID = 0 ;
    }

    if(!checkInput(itemName, 0)){
        $("#searchItemNameInput").val('');
        $("#searchItemNameInput").focus();
        return false;
    }

    //整合用户输入的搜索条件数据。
    let searchConditionData = {
        "itemID": itemID,
        "itemName":itemName,
        "itemParentID":itemParentID,
        "warehouseID":warehouseCheckInputValueArray
    };

    return searchConditionData;
}

//获取服务器返回数据，转为json对象
function getSearchItemResult(searchConditionData) {

    $.post(
        "searchItemService.php",
        {"searchConditionData": searchConditionData},
        function (searchItemResultJSON) {
            if(searchConditionData['warehouseID'].length){
                searchByWarehouse(searchItemResultJSON,searchConditionData['warehouseID']);
            }
            else {
                loadAllEndedItems(searchItemResultJSON);
            }
        },
        'json'
    );
}

//加载按仓库搜索的结果
function searchByWarehouse(searchItemResultJSON,warehouseArr) {
    let itemResultJSON = [];
    let k = 0;

    for(let i = 0; i < warehouseArr.length; i++){
        for(let j = 0; j < searchItemResultJSON.length; j++){
 
            if(0 == searchItemResultJSON[j].is_ended) {
                itemResultJSON[k++] = searchItemResultJSON[j];
            }
            else if(warehouseArr[i] == searchItemResultJSON[j].warehouse_id) {
                itemResultJSON[k++] = searchItemResultJSON[j];
            }
        }
    }
    loadAllEndedItems(itemResultJSON);
}

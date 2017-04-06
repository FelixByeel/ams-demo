//页面加载初始化，默认显示所有分类信息，实现无限分类菜单
window.onload = function () {

    let itemMenuDivObj = document.getElementById('itemMenu');      //获取加载li列表的ul对象

    let Global_warehouseJSON;
    let Global_itemJSON;

    loadAjaxGetData(itemMenuDivObj);

}

//ajax()方法加载分类数据,返回成功调用showItemInfo()初始化分类显示
function loadAjaxGetData(itemMenuDivObj) {

    $.ajax({
        type: "post",
        url: "searchItemService.php",
        data: { "tableName": "warehouse" },
        dataType: "json",
        cache: false,
        success: function (warehouseJSON) {
            Global_warehouseJSON = warehouseJSON;
        },
        error: function (msg, e) {
            alert("请求的数据发生异常：" + e);
        }
    });

    $.ajax({
        type: "post",
        url: "searchItemService.php",
        data: { "tableName": "item" },
        dataType: "json",
        cache: false,
        success: function (itemJSON) {
            Global_itemJSON = itemJSON;
            showItemInfo(itemMenuDivObj, itemJSON, 0);
            loadAllEndedItems(itemMenuDivObj, itemJSON);
        },
        error: function (msg, e) {
            alert("请求的数据发生异常：" + e);
        }
    });
}

//显示分类菜单
function showItemInfo(itemMenuDivObj, itemJSON, currentSelectedId) {

    let ulFlag = 1; //当前项有子项时才创建子项的容器ul，防止创建空的<ul></ul>标签
    let itemUlObj;

    for (let i = 0, j = 0; i < itemJSON.length; i++) {
        if ((currentSelectedId == itemJSON[i].parent_id) && (itemJSON[i].is_ended != 1)) {
            //没有子分类的项不显示
            if (ulFlag) {
                itemUlObj = document.createElement("ul");
                ulFlag = 0;
            }

            let itemLiObj = document.createElement("li");

            itemLiObj.id = "itemLiId_" + itemJSON[i].id;
            itemLiObj.innerHTML = itemJSON[i].item_name;

            itemLiObj.addEventListener("click", function (e) {

                itemClicked(this, itemMenuDivObj, itemJSON);
                e.stopPropagation();    //阻止事件冒泡，点击内层li不会触发上层li的click事件
            });

            itemUlObj.id = "itemUlId_" + currentSelectedId;
            itemUlObj.appendChild(itemLiObj);
        }
        else if ((currentSelectedId == itemJSON[i].parent_id) && (1 == itemJSON[i].is_ended)) {
            showCurrentSelectedDetail(itemMenuDivObj, itemJSON, currentSelectedId);
        }
    }

    if (itemUlObj) {
        itemMenuDivObj.appendChild(itemUlObj);
    }
}

//菜单click事件
function itemClicked(currentClickLi, itemMenuDivObj, itemJSON) {
    let currentSelectedId = currentClickLi.id.split("_")[1];

    //检测当前点击菜单项的子菜单是否存在
    if (checkSubItem(currentSelectedId)) {
        itemMenuDivObj = document.getElementById("itemLiId_" + currentSelectedId);
        showItemInfo(itemMenuDivObj, itemJSON, currentSelectedId);
    }
}

//检测当前点击菜单项的子菜单是否存在
function checkSubItem(currentSelectedId) {

    let ulListObj = document.getElementById("itemMenu").getElementsByTagName("ul");

    for (let i = 0; i < ulListObj.length; i++) {
        if ((ulListObj[i].id.split("_")[1]) == currentSelectedId) {
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
    trObj.insertCell(0).innerHTML = "分类编号";
    trObj.insertCell(1).innerHTML = "分类名称";
    trObj.insertCell(2).innerHTML = "所属仓库";
    trObj.insertCell(3).innerHTML = "数量";
    trObj.insertCell(4).innerHTML = "操作";

    for (let i = 0, j = 0; i < itemJSON.length; i++) {
        if ((currentSelectedId == itemJSON[i].parent_id) && (1 == itemJSON[i].is_ended)) {
            let trObj = tableObj.insertRow();
            if (j % 2) {
                trObj.className = "row_odd";
            }
            else {
                trObj.className = "row_even";
            }
            trObj.insertCell(0).innerHTML = itemJSON[i].id;
            trObj.insertCell(1).innerHTML = itemJSON[i].item_name;

            for (var k = 0; k < Global_warehouseJSON.length; k++) {
                if (Global_warehouseJSON[k].warehouse_id == itemJSON[i].warehouse_id) {
                    break;
                }
            }
            trObj.insertCell(2).innerHTML = Global_warehouseJSON[k].warehouse_name;
            trObj.insertCell(3).innerHTML = itemJSON[i].item_count;
            trObj.insertCell(4).innerHTML = "<button id = 'edit' onclick = 'editItem(" + itemJSON[i].id + ")'>编辑</button>";

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
    trObj.insertCell(0).innerHTML = "分类编号";
    trObj.insertCell(1).innerHTML = "分类名称";
    trObj.insertCell(2).innerHTML = "所属仓库";
    trObj.insertCell(3).innerHTML = "数量";
    trObj.insertCell(4).innerHTML = " ";

    for (let i = 0, j = 0; i < itemJSON.length; i++) {
        if (1 == itemJSON[i].is_ended) {
            let trObj = tableObj.insertRow();
            if (j % 2) {
                trObj.className = "row_odd";
            }
            else {
                trObj.className = "row_even";
            }
            trObj.insertCell(0).innerHTML = itemJSON[i].id;
            trObj.insertCell(1).innerHTML = itemJSON[i].item_name;

            for (var k = 0; k < Global_warehouseJSON.length; k++) {

                if (Global_warehouseJSON[k].warehouse_id == itemJSON[i].warehouse_id) {

                    break;
                }
            }
            trObj.insertCell(2).innerHTML = Global_warehouseJSON[k].warehouse_name;
            trObj.insertCell(3).innerHTML = itemJSON[i].item_count;
            trObj.insertCell(4).innerHTML = "<button id = 'edit' onclick = 'editItem(" + itemJSON[i].id + ")'>编辑</button>";

            j++;
        }
    }
    itemDetailDivObj.innerHTML = "";
    itemDetailDivObj.appendChild(tableObj);
}

//编辑操作
function editItem(currentSelectedId) {

    let itemIndex;

    $("#editBox").show();
    $("#classSelect").empty();
    $("#warehouseSelect").empty();
    $("#IDSpan").text(currentSelectedId);

    //找到当前选择项在JSON中的位置，并保存下来
    for (let i = 0; i < Global_itemJSON.length; i++) {
        if (Global_itemJSON[i].id == currentSelectedId) {
            itemIndex = i;
            break;
        }
    }

    $("#nameInput").val(Global_itemJSON[itemIndex].item_name);
    $("#countInput").val(Global_itemJSON[itemIndex].item_count);

    //显示可选择的所有分类，默认直接上级分类为选择状态
    for (let i = 0; i < Global_itemJSON.length; i++) {
        if (0 == Global_itemJSON[i].is_ended) {
            let str = "<option value = '" + Global_itemJSON[i].id + "'>" + Global_itemJSON[i].item_name + "</option>";
            $("#classSelect").append(str);
        }

        if (Global_itemJSON[i].id == Global_itemJSON[itemIndex].parent_id) {
            $("#classSelect").find("option[value = " + Global_itemJSON[i].id + "]").attr("selected", true);
        }
    }

    //显示仓库，并默认选择当前仓库
    for (let j = 0; j < Global_warehouseJSON.length; j++) {
        let str = "<option value = '" + Global_warehouseJSON[j].warehouse_id + "'>";
        str += Global_warehouseJSON[j].warehouse_name + "</option>";

        $("#warehouseSelect").append(str);

        if (Global_itemJSON[itemIndex].warehouse_id == Global_warehouseJSON[j].warehouse_id) {
            $("#warehouseSelect").find("option[value = " + Global_warehouseJSON[j].warehouse_id + "]").attr("selected", true);
        }
    }
}

//保存
$("#saveButton").click(function () {
    alert("save");
});

//取消
$("#cancelButton").click(function () {
    alert("cancel");
    $("#editBox").hide();
});

//删除
$("#delButton").click(function () {

    let result = confirm("确认删除？");
    let itemID = $("#IDSpan").text();
    if(result){
        $.post(
            "deleteItemService.php",
            {"tableName": "item", "id": itemID},
            function (msg) {
                if(msg){
                    alert (msg);
                }
            });
    }
    $("#editBox").hide();
});

//判断字符串是否合法，合法返回true，不合法返回false------------
function checkInput(content, flag) {

    let isNumberReg = /^[1-9]+[0-9]*]*$/;

    if (1 == flag && (!isNumberReg.test(content))) {
        alert("输入的物品数量无效，请重新输入！");
        document.getElementById('itemCountInput').focus();
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

                    document.getElementById('itemNameInput').focus();
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

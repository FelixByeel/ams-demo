//页面加载初始化，默认显示所有分类信息，实现无限分类菜单
window.onload = function () {

    let itemMenuObj = document.getElementById('itemMenuDiv');      //获取加载li列表的ul对象

    let warehouseJSON;
    let itemJSON;

    loadAjaxGetData(itemMenuObj);

}

//ajax()方法加载分类数据,返回成功调用showItemInfo()初始化分类显示
function loadAjaxGetData(itemMenuObj) {

    $.ajax({
        type: "post",
        url: "searchItemService.php",
        data: { "tableName": "warehouse" },
        dataType: "json",
        cache: false,
        success: function (warehouseJSON_s) {
            warehouseJSON = warehouseJSON_s;
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
        success: function (itemJSON_s) {
            itemJSON = itemJSON_s;
            showItemInfo(itemMenuObj, itemJSON, 0);
            loadAllEndedItems(itemMenuObj, itemJSON);
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

            itemLiObj.id = "itemLiId_" + itemJSON[i].item_id;
            itemLiObj.innerHTML = itemJSON[i].item_name;

            itemLiObj.addEventListener("click", function (e) {

                itemClicked(this, itemJSON);
                e.stopPropagation();    //阻止事件冒泡，点击内层li不会触发上层li的click事件
            });

            itemUlObj.id = "itemUlId_" + currentSelectedId;
            itemUlObj.appendChild(itemLiObj);
        }
        else {
            showCurrentSelectedDetail(itemMenuObj, itemJSON, currentSelectedId);
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

//显示当前分类下的详细信息
function showCurrentSelectedDetail(itemMenuObj, itemJSON, currentSelectedId) {

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
            trObj.insertCell(0).innerHTML = itemJSON[i].item_id;
            trObj.insertCell(1).innerHTML = itemJSON[i].item_name;

            for (var k = 0; k < warehouseJSON.length; k++) {
                if (warehouseJSON[k].warehouse_id == itemJSON[i].warehouse_id) {
                    break;
                }
            }
            trObj.insertCell(2).innerHTML = warehouseJSON[k].warehouse_name;
            trObj.insertCell(3).innerHTML = itemJSON[i].item_count;
            trObj.insertCell(4).innerHTML = "<button id = 'edit' onclick = 'editItem(" + itemJSON[i].item_id + ")'>编辑</button>";

            j++;
        }
    }
    itemDetailDivObj.innerHTML = "";
    itemDetailDivObj.appendChild(tableObj);
}

//页面加载时显示详细信息
function loadAllEndedItems(itemMenuObj, itemJSON) {

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
            trObj.insertCell(0).innerHTML = itemJSON[i].item_id;
            trObj.insertCell(1).innerHTML = itemJSON[i].item_name;

            for (var k = 0; k < warehouseJSON.length; k++) {

                if (warehouseJSON[k].warehouse_id == itemJSON[i].warehouse_id) {

                    break;
                }
            }
            trObj.insertCell(2).innerHTML = warehouseJSON[k].warehouse_name;
            trObj.insertCell(3).innerHTML = itemJSON[i].item_count;
            trObj.insertCell(4).innerHTML = "<button id = 'edit' onclick = 'editItem(" + itemJSON[i].item_id + ")'>编辑</button>";

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
    for (let i = 0; i < itemJSON.length; i++) {
        if (itemJSON[i].item_id == currentSelectedId) {
            itemIndex = i;
            break;
        }
    }

    $("#itemNameInput").val(itemJSON[itemIndex].item_name);
    $("#itemCountInput").val(itemJSON[itemIndex].item_count);

    //显示可选择的所有分类，默认直接上级分类为选择状态
    for (let i = 0; i < itemJSON.length; i++) {
        if (0 == itemJSON[i].is_ended) {
            let str = "<option value = '" + itemJSON[i].item_id + "'>" + itemJSON[i].item_name + "</option>";
            $("#classSelect").append(str);
        }

        if (itemJSON[i].item_id == itemJSON[itemIndex].parent_id) {
            $("#classSelect").find("option[value = " + itemJSON[i].item_id + "]").attr("selected", true);
        }
    }

    //显示仓库，并默认选择当前仓库
    for (let j = 0; j < warehouseJSON.length; j++) {
        let str = "<option value = '" + warehouseJSON[j].warehouse_id + "'>";
        str += warehouseJSON[j].warehouse_name + "</option>";

        $("#warehouseSelect").append(str);

        if (itemJSON[itemIndex].warehouse_id == warehouseJSON[j].warehouse_id) {
            $("#warehouseSelect").find("option[value = " + warehouseJSON[j].warehouse_id + "]").attr("selected", true);
        }
    }
}

//保存
$("#saveButton").click(function () {

    let itemID = $("#IDSpan").text();
    let parentID = $("#classSelect").val();
    let warehouseID = $("#warehouseSelect").val();
    let itemName = $("#itemNameInput").val();
    let itemCount = $("#itemCountInput").val();

    itemName = itemName.replace(/(^\s*)|(\s*$)/g, "");
    itemCount = itemCount.replace(/(^\s*)|(\s*$)/g, "");
    tableName = "item";

    if (!checkInput(itemCount, 1)) return false;
    if (!checkInput(itemName, 0)) return false;
    alert(warehouseID);
    let itemData = {
            "tableName": tableName,
            "itemID": itemID,
            "itemName": itemName,
            "parentID": parentID,
            "warehouseID": warehouseID,
            "itemCount": itemCount
        };
    $.post(
        "updateItem.php",
        {"itemData": itemData},
        function (msg) {
            if (msg) {
                alert(msg);
            }
        });
    $("#editBox").hide();
});

//取消
$("#cancelButton").click(function () {

    $("#editBox").hide();
});

//删除(保留功能)
$("#delButton").click(function () {

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

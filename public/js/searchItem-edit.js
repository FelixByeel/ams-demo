//显示当前分类下的详细信息
function showCurrentSelectedDetail(itemJSON, currentSelectedId = 0) {

    let itemDetailDivObj = document.getElementById("itemDetail");
    let tableObj = document.createElement("table");
    let trObj = tableObj.insertRow();

    tableObj.className = "show-detail-table";
    trObj.className = "tr-title";

    let trStr = "<td class = 'code-column td-title'>编号</td>";
    trStr += "<td class = 'name-column td-title'>名称</td>";
    trStr += "<td class = 'warehouse-column td-title'>仓库</td>";
    trStr += "<td class = 'count-column td-title'>数量</td>";
    trStr += "<td class = 'action-column td-title'>操作</td>";

    trObj.innerHTML = trStr;

    for (let i = 0, j = 0; i < itemJSON.length; i++) {

        //根据currentSelectedId 显示，为0表示默认显示所有物品条目，否则显示当前所选择的currentSelectedId的下的所有最终物品条目
        if(currentSelectedId ? ((currentSelectedId == itemJSON[i].parent_id) && (1 == itemJSON[i].is_ended)) : (1 == itemJSON[i].is_ended)){
            let trObj = tableObj.insertRow();
            let trStr = "";
            if (j % 2) {
                trObj.className = "odd-row";
            }
            else {
                trObj.className = "even-row";
            }
            trStr += "<td class = 'code-column td-content'>"+ itemJSON[i].item_id +"</td>";
            trStr += "<td class = 'name-column td-content'>"+ itemJSON[i].item_name +"</td>";

            let k = 0;
            for (; k < warehouseJSON.length; k++) {
                if (warehouseJSON[k].warehouse_id == itemJSON[i].warehouse_id) {
                    break;
                }
            }

            if ('undefined' == typeof (warehouseJSON[k])) {
                trStr += "<td class = 'warehouse-column td-content'>无</td>";
            }
            else {
                trStr += "<td class = 'warehouse-column td-content'>"+ warehouseJSON[k].warehouse_name +"</td>";
            }
            trStr += "<td class = 'count-column td-content'>"+ itemJSON[i].item_count +"</td>";
            trStr += "<td class = 'action-column td-content'><button class = 'edit-button' onclick = 'editItem(" + itemJSON[i].id + ")'>编辑</button></td>";
            trObj.innerHTML = trStr;
            j++;
        }
    }
    itemDetailDivObj.innerHTML = "";
    itemDetailDivObj.appendChild(tableObj);
}

//---------------编辑操作 start--------------------
function editItem(currentSelectedId) {

    //保存当前要编辑的条目在item JSON中的位置。
    let itemIndex;

    //显示遮罩层
    $("#shadeBox").width("100%");
    $("#shadeBox").height("100%");

    //弹出窗口时，阻止页面滚动。
    document.documentElement.style.overflow = "hidden";

    //弹出编辑窗口，先清空原来的内容。然后显示当前要编辑的内容。
    $("#editBox").show();
    $("#classSelect").empty();
    $("#warehouseSelect").empty();
    $("#IDSpan").text(currentSelectedId);

    //找到当前选择项在JSON中的位置，并保存下来
    for (let i = 0; i < itemJSON.length; i++) {
        if (itemJSON[i].id == currentSelectedId) {
            itemIndex = i;
            break;
        }
    }

    $("#itemNameInput").val(itemJSON[itemIndex].item_name);
    $("#itemCountInput").val(0);
    $("#currentCountSpan").text(itemJSON[itemIndex].item_count);

    //显示可选择的所有分类，默认直接上级分类为选择状态
    for (let i = 0; i < itemJSON.length; i++) {
        if (0 == itemJSON[i].is_ended) {
            let str = "<option value = '" + itemJSON[i].id + "'>" + itemJSON[i].item_name + "</option>";
            $("#classSelect").append(str);
        }

        if (itemJSON[i].id == itemJSON[itemIndex].parent_id) {
            $("#classSelect").find("option[value = " + itemJSON[i].id + "]").attr("selected", true);
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
    let currentCount = $("#currentCountSpan").text();
    let itemCount = $("#itemCountInput").val();

    itemName = itemName.replace(/(^\s*)|(\s*$)/g, "");
    itemCount = itemCount.replace(/(^\s*)|(\s*$)/g, "");

    if (itemName == '') {
        alert("分类名称不能为空！");
        return false;
    }

    if (('' == itemCount) || (0 == itemCount)) {
        itemCount = 0;
    }

    if (itemCount[0] == '-') {
        if (!checkInput(itemCount.substr(1), 1)) {
            alert("数量输入错误！");
            return false;
        }
    }
    else {
        if (0 != itemCount && !checkInput(itemCount, 1)) {
            alert("数量输入错误！");
            return false;
        }
    }

    if (!checkInput(itemName, 0)){
        alert("名称输入错误！");
        return false;
    }

    let tableName = "item";
    let itemData = {
        "tableName": tableName,
        "itemID": itemID,
        "itemName": itemName,
        "parentID": parentID,
        "warehouseID": warehouseID,
        "itemCount": itemCount,
        "currentCount": currentCount
    };

    $.post(
        "searchItem-editService.php",
        {
            "itemData": itemData
        },
        function (msg) {
            if (msg) {
                alert(msg);
                initData();
            }
        });

    //关闭编辑窗口，关闭遮罩层，允许页面滚动
    $("#editBox").hide();
    $("#shadeBox").width("0");
    $("#shadeBox").height("0");

    //判断是否应该出现垂直滚动条，-4是考虑到浏览器边框
    if(document.documentElement.clientWidth < document.documentElement.offsetWidth - 4) {
        document.documentElement.style.overflowY = "scroll";
    }
});

//点击弹出窗口右上角X
function closePopLayer(){
    $("#editBox").hide();
    $("#shadeBox").width("0");
    $("#shadeBox").height("0");

    //判断是否应该出现垂直滚动条，-4是考虑到浏览器边框
    if(document.documentElement.clientWidth < document.documentElement.offsetWidth - 4) {
        document.documentElement.style.overflowY = "scroll";
    }
}

//删除(此为保留功能，暂不实现)
/*
$("#delButton").click(function () {

});
*/
//---------------编辑操作 end--------------------

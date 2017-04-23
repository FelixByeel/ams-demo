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
        if (currentSelectedId ? ((currentSelectedId == itemJSON[i].parent_id) && (1 == itemJSON[i].is_ended)) : (1 == itemJSON[i].is_ended)) {
            let trObj = tableObj.insertRow();
            let trStr = "";
            if (j % 2) {
                trObj.className = "odd-row";
            }
            else {
                trObj.className = "even-row";
            }
            trStr += "<td class = 'code-column td-content'>" + itemJSON[i].id + "</td>";
            trStr += "<td class = 'name-column td-content'>" + itemJSON[i].item_name + "</td>";

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
                trStr += "<td class = 'warehouse-column td-content'>" + warehouseJSON[k].warehouse_name + "</td>";
            }

            trStr += "<td class = 'count-column td-content'>" + itemJSON[i].item_count + "</td>";
            trStr += "<td class = 'action-column td-content'><button class = 'checkout-button' onclick = 'checkoutItem(" + itemJSON[i].id + ")'>出库</button></td>";
            trObj.innerHTML = trStr;
            j++;
        }
    }
    itemDetailDivObj.innerHTML = "";
    itemDetailDivObj.appendChild(tableObj);
}

//-------------------------------出库 start------------------------
function checkoutItem(currentSelectedId) {
    $("#checkOutPopLayer").show();

    //找到当前选择项在JSON中的位置，并保存下来
    for (var i = 0; i < itemJSON.length; i++) {
        if (itemJSON[i].id == currentSelectedId) {
            break;
        }
    }

    $("#IDSpan").text(currentSelectedId);
    $("#itemNameSpan").text(itemJSON[i].item_name);
    $("#itemCountInput").val(1);
    $("#itemSNInput").val("");
    $("#consumerCodeInput").val("");
    $("#computerBarcodeInput").val("");
}

//格式化输入内容为每隔num位加一个空格。例：num = 3, 格式化为：aaa bbb ccc.
$("#computerBarcodeInput").keyup(function () {

    let computerBarcode = $(this).val();
    let num = 3;

    //去除所有空格
    computerBarcode = computerBarcode.replace(/\s/g, "");

    //输入非数字时过滤掉。
    if (!checkInput(computerBarcode, 1)) {
        computerBarcode = computerBarcode.substring(0, computerBarcode.length - 1);
    }
    $("#computerBarcodeInput").val(DivideThreeDigit(computerBarcode, num));
});

//格式化字符串为 每隔num位加一个空格。例：num = 3, 格式化为：aaa bbb ccc.
function DivideThreeDigit(str, num) {

    let strTemp = '';
    let i = 0;
    while (i < str.length) {

        if (i && (i % num === 0)) {
            strTemp += ' ';
            strTemp += str[i];
        }
        else {
            strTemp += str[i]
        }
        i++;
    }
    return strTemp;
}

//将输入字母转为大写
$("#itemSNInput").keyup(function () {
    let itemSNStr = $("#itemSNInput").val().toUpperCase();
    $("#itemSNInput").val(itemSNStr);
});

//点击确认按钮
$("#confirmCheckoutButton").click(function () {
    let currentItemID = $("#IDSpan").text();
    let itemCount = $("#itemCountInput").val();
    let itemSN = $("#itemSNInput").val();
    let consumerCode = $("#consumerCodeInput").val();
    let computerBarcode = $("#computerBarcodeInput").val();
    computerBarcode = computerBarcode.replace(/\s/g, ""); //去掉空格分隔。

    if (0 == itemCount.length) {
        alert("物品数量不能为空！");
        $("#itemCountInput").focus();
        return;
    } else if (!checkInput(itemCount, 1)) {
        alert("请输入正确的数字!");
        $("#itemCountInput").focus();
        return;
    }

    if (!checkInput(itemSN, 0)) {
        $("#itemSNInput").focus();
        return;
    }

    if (0 == consumerCode.length) {
        alert("用户工号不能为空！");
        $("#consumerCodeInput").focus();
        return;
    } else if (!checkInput(consumerCode, 0)) {
        $("#consumerCodeInput").focus();
        return;
    }

    if (computerBarcode.length != 0 && !checkInput(computerBarcode, 1)) {
        alert("资产条码只能是数字！");
        $("#computerBarcodeInput").focus();
        return;
    }

    let checkOutRecord = {
        "itemID": currentItemID,
        "updateCount": itemCount,
        "itemSN": itemSN,
        "consumerCode": consumerCode,
        "computerBarcode": computerBarcode
    };

    $.post(
        "searchItem-checkoutService.php",
        { "checkOutRecord": checkOutRecord },
        function (msg) {
            if (msg['status_id']) {//操作成功，关闭弹出层
                alert(msg['info']);
                hideCheckOutPopLayer();
            } else {
                alert(msg['info']);
            }
        },
        'json'
    );
});

//隐藏出库表单
function hideCheckOutPopLayer() {
    $("#checkOutPopLayer").hide();
    $("#shadeBox").width("0");
    $("#shadeBox").height("0");

    //判断是否应该出现垂直滚动条，-4是考虑到浏览器边框
    if (document.documentElement.clientWidth < document.documentElement.offsetWidth - 4) {
        document.documentElement.style.overflowY = "scroll";
    }
}

//点击弹出窗口右上角X
function closePopLayer() {
    $("#checkOutPopLayer").hide();
    $("#shadeBox").width("0");
    $("#shadeBox").height("0");

    //判断是否应该出现垂直滚动条，-4是考虑到浏览器边框
    if (document.documentElement.clientWidth < document.documentElement.offsetWidth - 4) {
        document.documentElement.style.overflowY = "scroll";
    }
}

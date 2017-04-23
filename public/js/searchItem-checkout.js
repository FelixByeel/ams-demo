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
}

//格式化输入内容为每隔num位加一个空格。例：num = 3, 格式化为：aaa bbb ccc.
$("#computerBarcodeInput").keyup(function (){

        let computerBarcode = $(this).val();
        let num  = 3;

        //去除所有空格
        computerBarcode = computerBarcode.replace(/\s/g, "");

        //输入非数字时过滤掉。
        if(!checkInput(computerBarcode, 1)) {
            computerBarcode = computerBarcode.substring(0, computerBarcode.length - 1);
        }
        $("#computerBarcodeInput").val(DivideThreeDigit(computerBarcode, num));
});

//格式化字符串为 每隔num位加一个空格。例：num = 3, 格式化为：aaa bbb ccc.
function DivideThreeDigit(str, num) {

    let strTemp = '';
    let i = 0;
    while(i < str.length) {

        if(i && (i % num === 0)) {
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

$("#confirmCheckoutButton").click(function(){
    let currentItemID = $("#IDSpan").text();
    let itemCount = $("#itemCountInput").val();
    let itemSN = $("#itemSNInput").val();
    let consumerCode = $("#consumerCodeInput").val();
    let computerBarcode = $("#computerBarcodeInput").val();

    if(!checkInput(itemCount, 1)){
         alert("请输入正确的数字!");
         return;
    }


    console.log("  id:" + currentItemID);
    console.log("  count:" + itemCount);
    console.log("  SN:" + itemSN);
    console.log("  NO.:" + consumerCode);
    console.log("  Barcode:" + computerBarcode);
});

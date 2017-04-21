//显示当前分类下的详细信息
function showCurrentSelectedDetail(itemJSON, currentSelectedId = 0) {

    let itemDetailDivObj = document.getElementById("itemDetail");
    let tableObj = document.createElement("table");
    let trObj = tableObj.insertRow();

    tableObj.id = "showDetailTable";
    trObj.id = "trHead";
    trObj.insertCell(0).innerHTML = "分类编号";
    trObj.insertCell(1).innerHTML = "分类名称";
    trObj.insertCell(2).innerHTML = "所属仓库";
    trObj.insertCell(3).innerHTML = "数量";
    trObj.insertCell(4).innerHTML = "操作";

    for (let i = 0, j = 0; i < itemJSON.length; i++) {

        //let flag = currentSelectedId ? ((currentSelectedId == itemJSON[i].parent_id) && (1 == itemJSON[i].is_ended)) : (1 == itemJSON[i].is_ended);
        //if ((currentSelectedId == itemJSON[i].parent_id) && (1 == itemJSON[i].is_ended)) {
        //根据currentSelectedId 显示，为0表示默认显示所有物品条目，否则显示当前所选择的currentSelectedId的下的所有最终物品条目
        if(currentSelectedId ? ((currentSelectedId == itemJSON[i].parent_id) && (1 == itemJSON[i].is_ended)) : (1 == itemJSON[i].is_ended)){
            let trObj = tableObj.insertRow();
            if (j % 2) {
                trObj.className = "row_odd";
            }
            else {
                trObj.className = "row_even";
            }
            trObj.insertCell(0).innerHTML = itemJSON[i].item_id;
            trObj.insertCell(1).innerHTML = itemJSON[i].item_name;

            let k = 0;
            for (; k < warehouseJSON.length; k++) {
                if (warehouseJSON[k].warehouse_id == itemJSON[i].warehouse_id) {
                    break;
                }
            }

            if ('undefined' == typeof (warehouseJSON[k])) {
                trObj.insertCell(2).innerHTML = '无';
            }
            else {
                trObj.insertCell(2).innerHTML = warehouseJSON[k].warehouse_name;
            }

            trObj.insertCell(3).innerHTML = itemJSON[i].item_count;
            trObj.insertCell(4).innerHTML = "<button id = 'checkout' onclick = 'checkoutItem(" + itemJSON[i].id + ")'>出库</button>";

            j++;
        }
    }
    itemDetailDivObj.innerHTML = "";
    itemDetailDivObj.appendChild(tableObj);
}



//-------------------------------出库 start------------------------
function checkoutItem(itemID) {
    alert(itemID);
}

//显示当前分类下的详细信息
function showCurrentSelectedDetail(itemJSON, currentSelectedId = 0) {

    let itemDetailDivObj = document.getElementById("itemDetail");
    let tableObj = document.createElement("table");
    let trObj = tableObj.insertRow();

    tableObj.className = "show-detail-table";
    trObj.className = "tr-title";
    /*
    trObj.insertCell(0).innerHTML = "分类编号";
    trObj.insertCell(1).innerHTML = "分类名称";
    trObj.insertCell(2).innerHTML = "所属仓库";
    trObj.insertCell(3).innerHTML = "数量";
    trObj.insertCell(4).innerHTML = "操作";
    */
    let trStr = "<td class = 'code-column td-title'>编号</td>";
    trStr += "<td class = 'name-column td-title'>名称</td>";
    trStr += "<td class = 'warehouse-column td-title'>仓库</td>";
    trStr += "<td class = 'count-column td-title'>数量</td>";
    trStr += "<td class = 'action-column td-title'>操作</td>";

    trObj.innerHTML = trStr;

    for (let i = 0, j = 0; i < itemJSON.length; i++) {

        //let flag = currentSelectedId ? ((currentSelectedId == itemJSON[i].parent_id) && (1 == itemJSON[i].is_ended)) : (1 == itemJSON[i].is_ended);
        //if ((currentSelectedId == itemJSON[i].parent_id) && (1 == itemJSON[i].is_ended)) {
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
            //trObj.insertCell(0).innerHTML = itemJSON[i].item_id;
            //trObj.insertCell(1).innerHTML = itemJSON[i].item_name;
            trStr += "<td class = 'code-column td-content'>"+ itemJSON[i].item_id +"</td>";
            trStr += "<td class = 'name-column td-content'>"+ itemJSON[i].item_name +"</td>";

            let k = 0;
            for (; k < warehouseJSON.length; k++) {
                if (warehouseJSON[k].warehouse_id == itemJSON[i].warehouse_id) {
                    break;
                }
            }

            if ('undefined' == typeof (warehouseJSON[k])) {
                //trObj.insertCell(2).innerHTML = '无';
                trStr += "<td class = 'warehouse-column td-content'>无</td>";
            }
            else {
                //trObj.insertCell(2).innerHTML = warehouseJSON[k].warehouse_name;
                trStr += "<td class = 'warehouse-column td-content'>"+ warehouseJSON[k].warehouse_name +"</td>";
            }

            //trObj.insertCell(3).innerHTML = itemJSON[i].item_count;
            //trObj.insertCell(4).innerHTML = "<button id = 'edit' onclick = 'editItem(" + itemJSON[i].id + ")'>编辑</button>";
            trStr += "<td class = 'count-column td-content'>"+ itemJSON[i].item_count +"</td>";
            trStr += "<td class = 'action-column td-content'><button id = 'edit' onclick = 'checkoutItem(" + itemJSON[i].id + ")'>出库</button></td>";
            trObj.innerHTML = trStr;
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

//searchRecord

//页面加载
window.onload = function () {
    $("#contentWrapper").load("getSearchRecordService.php");
}

//点击搜索按钮
function searchRecord() {

    var searchConditions = getInputConditions();
    if (searchConditions) {
        $("#contentWrapper").load("getSearchRecordService.php", { "searchConditions": searchConditions });
    }
}

//获取输入的查询条件
function getInputConditions() {
    //获取输入的日期并转为时间戳
    var startTimeStr = $("#startDate").val();
    var endTimeStr = $("#endDate").val();

    var startTime = stringParseToTimestamp(startTimeStr + "T00:00:00"); //加“T”表示格式化为GMT标准时间，不加则格式化为本地时区时间，会相对GMT时间加上时区偏移量。如GMT+8。
    var endTime = stringParseToTimestamp(endTimeStr + "T00:00:00");

    //查询结果包含当前选择的日期，例：开始日期和结束日期为 2017-05-09,格式化为Unix时间戳为1494259200，表示“2017-05-09 00:00:00”，减1，则表示为"2017-05-08 23:59:59",加86400,则表示为"2017-05-10 00:00:00"
    if(startTime) {
        startTime -= 1;
    }

    if(endTime) {
        endTime += 86400;
    }

    //获取物品名称
    var itemName = $("#itemName").val();
    itemName = checkInputStr.trimSpace(itemName);
    if (!itemName.length) {
        itemName = '';
    } else if (str = checkInputStr.isExistSpecialChar(itemName)) {
        alert("物品名称不能含有：" + str);
        $("#itemName").focus();
        return false;
    }

    //获取处理类型
    var dealType = $("#dealType option:selected").val();
    //用户工号
    var consumerCode = $("#consumerCode").val();
    consumerCode = checkInputStr.trimSpace(consumerCode);
    if (!consumerCode.length) {
        consumerCode = '';
    }
    else if (str = checkInputStr.isExistSpecialChar(consumerCode)) {
        alert("用户工号中不能含有：" + str);
        $("#consumerCode").focus();
        return false;
    }
    //资产条码
    var computerBarcode = $("#computerBarcode").val();
    computerBarcode = checkInputStr.trimSpace(computerBarcode);
    if (!computerBarcode.length) {
        computerBarcode = '';
    }
    else if (!checkInputStr.isDigital(computerBarcode)) {
        alert("资产条码只能包含数字！");
        $("#computerBarcode").focus();
        return false;
    }
    //物品序列号
    var itemSN = $("#itemSN").val();
    itemSN = checkInputStr.trimSpace(itemSN);
    if (!itemSN.length) {
        itemSN = '';
    }
    else if (str = checkInputStr.isExistSpecialChar(itemSN)) {
        alert("物品序列号中不能含有：" + str);
        $("#itemSN").focus();
        return false;
    }
    //处理人工号
    var username = $("#username").val();
    username = checkInputStr.trimSpace(username);
    if (!username.length) {
        username = '';
    }
    else if (str = checkInputStr.isExistSpecialChar(username)) {
        alert("处理人工号中不能含有：" + str);
        $("#username").focus();
        return false;
    }

    var searchConditionsData = {
        "startTime": startTime,
        "endTime": endTime,
        "itemName": itemName,
        "dealType": dealType,
        "consumerCode": consumerCode,
        "computerBarcode": computerBarcode,
        "itemSN": itemSN,
        "username": username
    }

    return searchConditionsData;
}

//将日期格式字符串转为时间戳，如“2017-05-02T00:00:00”转为:1493654400,源字符串为空或格式不正确，返回0;
function stringParseToTimestamp(timeStr) {

    var timestamp = Date.parse(new Date(timeStr));  //返回以毫秒记的Unix时间戳
    timestamp = timestamp / 1000;
    return isNaN(timestamp) ? 0 : timestamp;
}


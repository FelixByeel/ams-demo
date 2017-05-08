//searchRecord
function searchRecord() {

    //获取输入的日期并转为时间戳
    var startTimeStr = $("#startDate").val();
    var endTimeStr = $("#endDate").val();
    var startTime = stringParseToTimestamp(startTimeStr);
    var endTime = stringParseToTimestamp(endTimeStr);

    //获取处理类型
    var dealType = $("#dealType option:selected").val();
    //用户工号
    var consumerCode = $("#consumerCode").val();
    //资产条码
    var computerBarcode = $("#computerBarcode").val();
    //物品序列号
    var itemSN = $("#itemSN").val();
    //处理人
    var username = $("#username").val();

}

//将日期格式字符串转为时间戳，如“2017-5-2 12:00:00”转为:1493697600,源字符串为空或格式不正确，返回0;
function stringParseToTimestamp(timeStr) {

    var timestamp = Date.parse(new Date(timeStr));
    timestamp = timestamp / 1000;                       //将上一步获取的毫秒格式时间戳转为以秒记的时间戳。
    return isNaN(timestamp) ? 0 : timestamp;
}

<?php
    //定义根目录，加载数据库相关文件
    define('APP_ROOT', dirname(dirname(__DIR__)).'/');
    require_once (APP_ROOT.'app/login/loginCheck.php');
?>
<!--查询出入库记录-->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../../public/css/searchRecord.css" rel="stylesheet">
    </head>
    <body>
        <div id = "bodyWrapper">
            <!--搜索块-->
            <div id = "searchWrapper" class = "search-box">
                <div class = "search-subbox search-group-one">
                    <label for = "startDate" class = "labelStyle">开始日期：
                        <input id = "startDate" class="search-date" type = "date"/>
                    </label>
                    <label for = "itemName" class = "labelStyle">物品名称：
                        <input id = "itemName" class="search-name" type = "text" maxlength="20"/>
                    </label>
                </div>

                <div class = "search-subbox search-group-two">
                    <label for = "endDate" class = "labelStyle">结束日期：
                        <input id = "endDate" class="search-date" type = "date" />
                    </label>
                    <label for = "itemSN" class = "labelStyle">物品序列号：
                        <input id = "itemSN" class="search-sn" type = "text" maxlength="20"/>
                    </label>
                </div>

                <div class = "search-subbox search-group-three">
                    <label for = "consumerCode" class = "labelStyle">用户工号：
                        <input id = "consumerCode" class="search-consumer-code" type = "text" maxlength="10"/>
                    </label>
                    <label for = "computerBarcode" class = "labelStyle">资产条码：
                        <input id = "computerBarcode" class="search-barcode" type = "text" maxlength="20"/>
                    </label>

                </div>

                <div class = "search-subbox search-group-four">
                    <label for = "dealType" class = "labelStyle">处理类型：
                        <select id = "dealType" class="search-type">
                            <option value = "全部">全部</option>
                            <option value = "出库" selected = "selected">出库</option>
                            <option value = "入库">入库</option>
                            <option value = "其他">其他</option>
                        </select>
                    </label>
                    <label for = "username" class = "labelStyle">处理人：
                        <input id = "username" class="search-username" type = "text" maxlength="15"/>
                    </label>
                </div>
                    <button id = "searchButton" class="search-button" onclick="searchRecord()">搜索</button>
                    <br/>
                    <button id = "resetButton" class="search-button reset-button" onclick="resetInput()">重置</button>
            </div>
            <div class = "search-more-box"><span id = "searchMoreContent" class = "search-more-content">更多<i id = "search-arrow-icon" class = "arrow"></i></span></div>
            <!--搜索结果显示-->
            <div id = "contentWrapper"></div>
        </div>
    <!--script-->
    <script src = "../../public/js/jquery-1.8.3/jquery.js"></script>
    <script src = "../../public/js/checkInputStr.js"></script>
    <script src = "../../public/js/searchRecord.js"></script>
    </body>
</html>

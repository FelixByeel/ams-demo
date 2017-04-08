<?php
    //定义根目录，加载数据库相关文件
    define('APP_ROOT', dirname(dirname(__DIR__)).'/');
    require_once (APP_ROOT.'app/login/loginCheck.php');
    require_once (APP_ROOT.'include/dbConfig.php');
    require_once (APP_ROOT.'include/Msqli.class.php');

    //验证用户权限
    if($_SESSION['role_group'] < 2) {
        die('当前用户无法进行此操作！');
    }

    //连接数据库
    $mysql          = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

    $itemData       = [];           //保存分类信息
    $warehouseData  = [];           //保存仓库信息

    //查询分类信息，并保存在$itemData[]中
    $column = array('item_id', 'item_name', 'parent_id', 'is_ended');
    $result = $mysql->select('item_t', $column);

    while($row = mysqli_fetch_assoc($result)){
        $itemData[] = $row;
    }
    //将查询的信息进行JSON转换，添加参数JSON_UNESCAPED_UNICODE解决中文乱码
    $itemData = json_encode($itemData,JSON_UNESCAPED_UNICODE);

    //查询仓库信息，并保存在$warehouseData[]中
    $result = $mysql->select('warehouse_t', array('warehouse_id', 'warehouse_name'));

    while($row = mysqli_fetch_assoc($result)){
        $warehouseData[] = $row;
    }
    $warehouseData = json_encode($warehouseData,JSON_UNESCAPED_UNICODE);
?>
<!--添加分类-->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<link href="../../public/css/addItem.css" rel="stylesheet" type="text/css" />
<script src = "../../public/js/jquery-1.8.3/jquery.js"></script>
</head>
<body>
    <div id = "shadeDiv"></div>
        <div id = 'bodyBox'>
            <div id = "warehouseBox" class = "itemDiv">
                <p>请选择备货仓库：</p>
                <div id = "warehouseList"></div>
                <button id = "addWarehouse" onclick = "showAddWarehouseDiv()" >添加仓库</button>
            </div>
            <div id = "itemBox" class = "itemDiv">
                <p>请选择上级分类：</p>
                <div id = "itemList"></div>
            </div>

            <div id = "inputBox" class = "itemDiv">
                <p>请输入分类（物品）名称：</p>
                <input id = "itemNameInput" type = "text"/>
                <!--
                <p>请输入物品数量：</p>
                <input id = "itemCountInput" type = "text"/>
                -->
            </div>
            <button id = "addItem" onclick = "addItem()">保存</button>
            <div id = "tips" ></div>
        </div>

        <!-- 弹出层-->
        <div id = "addWarehouseDiv" class = 'addWarehouseDivHide'>
            <span id = "closeDiv" onclick = "closePopLayer()">&times;</span>
            <p>仓库名称：</p>
            <input type="text" name="warehouseName" value=""/>
            <br />
            <button id = "saveWarehouseName">保存</button>
        </div>
</body>

<!--script-->
<script>

    window.onload = function(){
        let itemJsonStr         = '<?php echo $itemData; ?>';
        let itemJsonObj         = JSON.parse(itemJsonStr);              //获取分类信息，转换为JSON对象。

        let warehouseJsonStr    = '<?php echo $warehouseData; ?>';
        let warehouseJsonObj    = JSON.parse(warehouseJsonStr);         //获取仓库信息，转换为JSON对象

        let itemSelectId        = 0;                                    //初始分类为0
        let itemListObj         = document.getElementById('itemList');  //获取需要添加select的对象
        let itemSelectName      = -1;                                   //第一个分类select列表name属性的值

        let warehouseListObj    = document.getElementById('warehouseList');

        //显示仓库信息
        showWarehouseInfo(warehouseJsonObj, warehouseListObj);

        //调用显示下一级分类
        showSubItem(itemListObj, itemSelectId, itemSelectName, itemJsonObj);
    }

    //----------------------显示仓库信息--------------------------------------
    function showWarehouseInfo(warehouseJsonObj, warehouseListObj){

        if(!warehouseJsonObj.length){
            warehouseListObj.innerHTML = "未查询到仓库信息，请先添加仓库信息！";
        }
        else {

            let str = "";

            for(let i = 0; i < warehouseJsonObj.length; i++){

                if(0 == i) {
                    str +="<label><input type = 'radio' checked = 'checked' name = 'warehouse' value = '";
                    str += warehouseJsonObj[i].warehouse_id;
                    str +="'/>" + warehouseJsonObj[i].warehouse_name + "</label>";
                }
                else {
                    str +="<label><input type = 'radio' name = 'warehouse' value = '";
                    str += warehouseJsonObj[i].warehouse_id;
                    str +="'/>" + warehouseJsonObj[i].warehouse_name + "</label>";
                }
            }
            warehouseListObj.innerHTML = str;
        }
    }

    //---------------定义显示下一级分类方法--------------
    //return 当前创建成功的select的id即分类号(分类号表示第一级分类，第二级分类...)
    function showSubItem(itemListObj, itemSelectId, itemSelectName, itemJsonObj){

        let itemSelectObj = document.createElement('select');
        let itemOptionObj = document.createElement('option');

        //创建一个select时添加默认选择分类为空
        itemSelectObj.id = "itemSelectId_" + itemSelectId;
        itemSelectObj.name = "itemSelectName_" + itemSelectName;

        itemOptionObj.value= 'itemOption_0';
        itemOptionObj.text = "请选择一个分类";

        itemSelectObj.appendChild(itemOptionObj);

        //添加option
        for(let i = 0; i < itemJsonObj.length; i++){

            let itemOptionObj = document.createElement('option');

            //显示当前分类下的所有子项
            if(itemSelectId == itemJsonObj[i].parent_id){
               itemOptionObj.value = 'itemOption_' + itemJsonObj[i].item_id;
               itemOptionObj.text = itemJsonObj[i].item_name;
               itemSelectObj.appendChild(itemOptionObj);
           }
        }
        //为每个select绑定change事件
        itemSelectObj.addEventListener('change',function(){

            itemChanged(this, itemListObj,  itemJsonObj);
        });

        itemListObj.appendChild(itemSelectObj);
    }

    //-----------------select的change事件-------------------
    function itemChanged(choose, itemListObj, itemJsonObj){

        let currentSelectedId     = 0;    //保存要创建子分类的select的ID号码
        let currentSelectedName   = 0;    //保存要创建子分类的select的name号

        //子分类select的ID号码为当前选择项option的id号码
        currentSelectedId = (choose.options[choose.selectedIndex].value).split("_")[1];

        //获取当前select的ID号，当前select的ID号作为下一个创建的select的Name号，用来表示分类等级，name号一样，表示同一级分类
        currentSelectedName  = choose.id.split("_")[1];

        //当前选择分类项改变时，所属仓库显示同步
        changeWarehouse(currentSelectedId);

        //当前选择分类项改变时，下一级分类显示同步：
        //先移除当前分类下所有显示的子分类，然后通过showSubItem()显示为更换分类选择后的子分类
        removeSubItem(currentSelectedName);

        //判断是否选择了空项，默认第一项提示为空，选择空项时，不显示下一级分类，否则继续显示子分类
        if(0 != currentSelectedId){
            //获取到当前选择项在itemJsonObj中的位置
            for(let i = 0; i < itemJsonObj.length; i++) {
                if(currentSelectedId == itemJsonObj[i].item_id){
                    currentSelectedId = i;
                    break;
                }
            }

            //当前选择项的is_ended的值为0表示有子分类，显示子分类
            if( 0 == itemJsonObj[currentSelectedId].is_ended) {
                showSubItem(itemListObj, itemJsonObj[currentSelectedId].item_id, currentSelectedName, itemJsonObj);
            }
        }
    }

    //同步仓库显示
    function changeWarehouse(currentSelectedId){
    }

    //同一级分类select的name为共同上级分类select的id，同级分类在同一个select列表显示-----------------
    function  removeSubItem(currentSelectedName){

        let selectArr   = document.getElementsByTagName("select");      //获取当前已存在的select列表
        let selectName  = "itemSelectName_" + currentSelectedName;        //获取将要创建的select的Name属性

        //在selectArr列表中查找已存在的selectName，当找到时候，移除当前列表及其后面的同胞节点。
        //返回上层function时，重新创建下一级select，达到同级分类显示在同一个select列表
        for(let i = 0; i < selectArr.length; i++){
            if(selectArr[i].name == selectName){

                let selectId = selectArr[i].id;

                $("#" + selectId).nextAll().remove();
                $("#" + selectId).remove();
            }
        }
    }

    //---------------点击“添加”按钮-------------
    function addItem(){

        let itemName        = document.getElementById('itemNameInput').value;       //获取输入的分类名称
        //let itemCount       = document.getElementById('itemCountInput').value;      //获取输入的物品数量

        let warehouseId     = getWarehouseRadioValue();                             //存储当前选择的仓库信息
        let itemSelectId    = getItemSelectOptionValueList();                       //存储当前将要添加的分类的上一级分类ID

        let itemJSON;                                                               //构建JSON格式数据用来整体提交数据

        itemName            = itemName.replace(/(^\s*)|(\s*$)/g, "");
        //itemCount           = itemCount.replace(/(^\s*)|(\s*$)/g, "");              //去除输入内容中首尾的空格

        //验证输入数据的有效性
        if(!warehouseId) {
            alert("未查询到仓库信息，请先添加仓库信息！");
            return false;
        }

        if(0 != itemName.length && checkInput(itemName, 0)){
            //if((0 != itemCount.length && checkInput(itemCount, 1)) || (0 == itemCount.length)){
                itemJSON = {
                    "warehouse_id"  : warehouseId,
                    "parent_id"     : itemSelectId,
                    "item_name"     : itemName
                    //"item_count"    : itemCount
                };
            //}
            //else {
            //    return false;
            //}
        }
        else if(0 == itemName.length){
            alert("请先输入一个分类名称！");
            return false;
        }
        else {
            return false;
        }

        //提交数据
        if(!itemJSON){
            alert("输入的数据有误！");
            return false;
        }
        else{
            $("#tips").load("addItemService.php", {"itemData" : itemJSON}, function(msg){
                document.getElementById('itemNameInput').value = '';
                alert(msg);
            });
        }
    }

    //判断字符串是否合法，合法返回true，不合法返回false------------
    function checkInput(content, flag){

        let isNumberReg = /^[1-9]+[0-9]*]*$/;

        if(1 == flag && (!isNumberReg.test(content))){
            alert("输入的物品数量无效，请重新输入！");
            //document.getElementById('itemCountInput').focus();
            return false;
        }
        else if(0 == flag){

            let is_existC           = 0;            //is_existC用来跳出双层FOR循环
            //不接受下列字符的输入
            let specialCharacter    = new Array('~', '`', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-',
                    '=', '+', '[', ']', '{', '}', '\\', '|', ';', ':', '\'', '\"', ',', '<', '.', '>', '?',
                    '~', '·', '！', '＠', '＃', '￥', '％', '………', '＆', '＊', '（', '）', '——', '＋', '＝',
                    '【', '】', '｛', '｝', '、', '｜', '；', '：', '’', '“', '，', '《', '。', '》', '？', ' ', '　');

            for(let i = 0; i < content.length; i++) {
                for(let j = 0; j < specialCharacter.length; j++){
                    if(content[i] == specialCharacter[j]){
                        is_existC = 1;
                        if((' ' == specialCharacter[j]) || ('　' == specialCharacter[j])){
                            alert("分类名称中不能含有空格！");
                        }
                        else{
                            alert("分类名称中不能含有字符：" + specialCharacter[j]);
                        }

                        document.getElementById('itemNameInput').focus();
                        return false;
                    }
                }

                if(is_existC) return false;
            }

            if(is_existC) return false;
            else return true;
        }
        else {
            return true;
        }
    }

    //获取当前选择的仓库
    function getWarehouseRadioValue(){

        let radioObj = document.getElementsByName('warehouse');

        if(0 == radioObj.length){
            return false;
        }

        for(let i = 0; i < radioObj.length; i++){
            if(true == radioObj[i].checked){
                return radioObj[i].value;
            }
        }
    }

    //返回一个有效的上一级分类ID。返回0表示要添加的分类为顶层分类。
    function getItemSelectOptionValueList(){

        let selectArr   = document.getElementsByTagName("select");
        let val         = [];

        for(let i = 0; i < selectArr.length; i++){
            //有时候会莫名其妙出现几个<select></select>为空项的bug，导致获取选择的option时出现undefined，所以加了if判断，返回分类数组时，去除undefined项。
            let optionNumber = selectArr[i].options[selectArr[i].selectedIndex].value.split("_")[1];
            if((typeof(optionNumber) != "undefined") && (optionNumber != 0)){
                val[i] = selectArr[i].options[selectArr[i].selectedIndex].value.split("_")[1];
            }
        }
        //获取所有祖先分类ID，返回父分类ID
        return val.length ? val[val.length - 1] : 0;
    }

    //显示添加仓库弹出层
    function showAddWarehouseDiv() {
        document.getElementById('addWarehouseDiv').style.display = "block";

        document.getElementById('shadeDiv').style.width = '100%';
        document.getElementById('shadeDiv').style.height = '100%';
    }

    //关闭添加仓库弹出层
    function closePopLayer() {
        document.getElementById('addWarehouseDiv').style.display = "none";

        document.getElementById('shadeDiv').style.width = '0';
        document.getElementById('shadeDiv').style.height = '0';
    }

</script>
</html>



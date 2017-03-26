<?php
    define('APP_ROOT', dirname(dirname(__DIR__)).'/');
    require_once (APP_ROOT.'include/dbConfig.php');
    require_once (APP_ROOT.'include/Msqli.class.php');

    //连接数据库
    $mysql = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

    $itemData = [];         //保存分类信息
    $warehouseData = [];    //保存仓库信息

    //查询分类信息，并保存在$itemData[]中
    $result = $mysql->select('item_t', 'id, item_name, parent_id, is_ended');
    while($row = mysqli_fetch_assoc($result)){
        $itemData[] = $row;
    }

    //将查询的信息进行JSON转换，添加参数JSON_UNESCAPED_UNICODE解决中文乱码
    $itemData = json_encode($itemData,JSON_UNESCAPED_UNICODE);

    //查询仓库信息，并保存在$warehouseData[]中
    $result = $mysql->select('warehouse_t', 'warehouse_id, warehouse_name');
    while($row = mysqli_fetch_assoc($result)){
        $warehouseData[] = $row;
    }
    $warehouseData = json_encode($warehouseData,JSON_UNESCAPED_UNICODE);
?>
<!--添加分类-->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<script type = "text/javascript" src = "../../public/js/jquery-1.8.3/jquery.js"></script>
<script></script>
<style>
    select{
        width:199px;
        height:25px;
        margin-top:5px;
        
    }

    div{
        width:200px;
    }

</style>
</head>
<body>
    
    <div id = "warehouseBox">
        <p>请选择备货仓库：</p>
        <div id = "warehouseList"></div>
    </div>
    <div id = "itemBox">
        <p>请选择上级分类：</p>
        <div id = "itemList"></div>
    </div>

    <div id = "inputBox">
        <p>请输入分类（物品）名称：</p>
        <input id = "itemNameInput" type = "text"/>
        <p>请输入物品数量：</p>
        <input id = "itemCountInput" type = "text"/>
    </div>
    <button id = "addItem2" onclick = "addItem()">添加</button>
    <div id = "tips" ></div>
</body>

<!--script-->
<script>

    window.onload = function(){
        let itemJsonStr = '<?php echo $itemData; ?>'; 
        let itemJsonObj = JSON.parse(itemJsonStr);              //记录分类信息，转换为JSON对象。

        let warehouseJsonStr = '<?php echo $warehouseData; ?>';
        let warehouseJsonObj = JSON.parse(warehouseJsonStr);    //记录仓库信息，转换为JSON对象

        //定义分类列表显示相关变量
        let itemSelectId = 0;                                   //初始分类为0
        let itemListObj = document.getElementById('itemList');  //获取需要添加select的对象
        let itemSelectName = -1;                                //第一个分类select列表name属性的值

        //定义仓库列表显示相关变量
        let warehouseListObj = document.getElementById('warehouseList');


        //显示仓库信息
        showWarehouseInfo(warehouseJsonObj, warehouseListObj);
        
        //调用显示下一级分类
        addItemSelect(itemListObj, itemSelectId, itemSelectName, itemJsonObj);
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
    //return 当前创建成功的select的id即分类号(分类号表示第一级分类，第二季分类...)
    function addItemSelect(itemListObj, itemSelectId, itemSelectName, itemJsonObj){

        let itemSelectObj = document.createElement('select');
        let itemOptionObj = document.createElement('option');
        
        //创建一个select时的添加“请选择分类”项为默认值
        itemSelectObj.id = "itemSelectId_" + itemSelectId;
        itemSelectObj.name = "itemSelectName_" + itemSelectName;
        
        itemOptionObj.value= 'itemOption_0';
        itemOptionObj.text = "--无--";

        itemSelectObj.appendChild(itemOptionObj);

        //添加option
        for(let i = 0; i < itemJsonObj.length; i++){

            let itemOptionObj = document.createElement('option');
            
            //通过if判断是否需要显示分类项下面的最终项
            //if((itemSelectId == itemJsonObj[i].parent_id) && (0 == itemJsonObj[i].is_ended)){
            if(itemSelectId == itemJsonObj[i].parent_id){

               itemOptionObj.value = 'itemOption_' + itemJsonObj[i].id;
               itemOptionObj.text = itemJsonObj[i].item_name;
               itemSelectObj.appendChild(itemOptionObj);
           }
        }
        //为每个select绑定change事件
        itemSelectObj.addEventListener('change',function(){

            itemChange(this, itemListObj,  itemJsonObj);
        });

        itemListObj.appendChild(itemSelectObj);
        //return itemSelectId;
    }
    
    //-----------------select的change事件-------------------
    function itemChange(choose, itemListObj, itemJsonObj){

        let will_selectedId = 0;        //保存要创建子分类的select的ID号码
        let is_selectedId = 0;          //保存当前选择项的select的ID号
        let will_selectedName = 0;      //保存要创建子分类的select的name号

        //子分类select的ID号码为当前选择项option的id号码
        will_selectedId = (choose.options[choose.selectedIndex].value).split("_")[1];

        //获取当前select的ID号，当前select的ID号作为下一个创建的select的Name号，用来表示分类等级，name号一样，表示同一级分类
        will_selectedName  = choose.id.split("_")[1];

        //选择分类项改变时，下一级分类显示同步
        countSelect(will_selectedName);

        //判断是否选择了空项，默认第一项提示为空，选择空项时，不显示下一级分类，否则继续显示子分类
        //if(!(0 == will_selectedId)){
        if(0 != will_selectedId){
            //获取到当前选择项在itemJsonObj中的位置
            for(let i = 0; i < itemJsonObj.length; i++) {
                if(will_selectedId == itemJsonObj[i].id){
                    will_selectedId = i;
                    break;
                }
            }

            //当前选择项有子分类时，显示子分类
            if( 0 == itemJsonObj[will_selectedId].is_ended) {
                //existSelectList[existSelectListCount++] = addItemSelect(itemListObj, itemJsonObj[will_selectedId].id, will_selectedName, itemJsonObj);
                addItemSelect(itemListObj, itemJsonObj[will_selectedId].id, will_selectedName, itemJsonObj);
            }
        }
    }

    //同一级分类select的name为共同上级分类select的id，同级分类在同一个select列表显示-----------------
    function  countSelect(will_selectedName){
        let selectArr = document.getElementsByTagName("select");    //获取当前已存在的select列表
        let selectName = "itemSelectName_" + will_selectedName;     //获取将要创建的select的Name属性
        
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

        let itemName = document.getElementById('itemNameInput').value;      //获取输入的分类名称
        let itemCount = document.getElementById('itemCountInput').value;    //获取输入的物品数量

        let warehouseId = getWarehouseRadioValue();                         //存储当前选择的仓库信息
        let itemSelectId = getItemSelectOptionValueList();                  //存储当前将要添加的分类的上一级分类ID

        let item = "";

        //itemName = itemName.replace(/(^\s*)|(\s*$)/g, "");
        //itemCount = itemCount.replace(/(^\s*)|(\s*$)/g, "");              //去除输入内容中首尾的空格

        itemName = itemName.replace(/\s+/g,"");
        itemCount = itemCount.replace(/\s+/g,"");                           //去除输入内容中的所有空格

        if(!warehouseId) {
            alert("未查询到仓库信息，请先添加仓库信息！");
            return false;
        }

        if((0 == itemSelectId) && (0 == itemName.length)){

            alert("请先选择一个分类或者添加一个新的分类!");
            return false;
        }
        else if(0 != itemSelectId) {
            if(0 == itemCount && 0 == itemName){
                alert("请输入一个分类或者有效的物品数量！");
                return false;
            }
            if(0 != itemCount.length){
                if(!checkInput(itemCount)){
                    return false;
                }
            }
        }
        else if(0 == itemSelectId){
            if(0 != itemCount.length){
                if(!checkInput(itemCount)){
                    return false;
                }
            }
        }
        else {
            alert("输入的数据有误，请修改后再提交!");
            return false;
        }

        if(0 == itemName.length) {
            itemJSON = {
                "warehouse_id"  : warehouseId,
                "id"            : itemSelectId,
                "item_count"    : itemCount
            };
        }
        else{
            itemJSON = {
                "warehouse_id"  : warehouseId,
                "parent_id"     : itemSelectId,
                "item_name"     : itemName,
                "item_count"    : itemCount
            };
        }
        alert(itemJSON.item_name);
        //提交数据
        $("#tips").load("insertItemService.php", {"itemData" : itemJSON}, function(msg){
            alert("添加成功");
        });
    }

    //判断字符串是否为正整数------------
    function checkInput(content){

        var isNumber = /^[1-9]+[0-9]*]*$/; 

        if(!isNumber.test(content)){
            alert("输入的物品数量无效，请重新输入！");
            document.getElementById('itemCountInput').value = "";
            document.getElementById('itemCountInput').focus();
            return false;
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

    //返回一个有效的上一级分类ID。0表示要添加的分类为第一级分类。
    function getItemSelectOptionValueList(){
        let selectArr = document.getElementsByTagName("select");
        let val = [];

        for(let i = 0; i < selectArr.length; i++){
            //有时候会莫名其妙出现几个<select></select>为空项的bug，导致获取选择的option时出现undefined，所以加了if判断，返回分类数组时，去除undefined项。
            let optionNumber = selectArr[i].options[selectArr[i].selectedIndex].value.split("_")[1];
            if((typeof(optionNumber) != "undefined") && (optionNumber != 0)){

                val[i] = selectArr[i].options[selectArr[i].selectedIndex].value.split("_")[1];
            }
        }
        
        return val.length ? val[val.length - 1] : 0;
    }
</script>
</html>



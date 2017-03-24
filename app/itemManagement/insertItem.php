<?php
    define('APP_ROOT', dirname(dirname(__DIR__)).'/');
    require_once (APP_ROOT.'include/dbConfig.php');
    require_once (APP_ROOT.'include/Msqli.class.php');

    //连接数据库
    $mysql = new Msqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
/*
    $result = $mysql->select('user_t', '*','id = 1');
    while($row = mysqli_fetch_array($result)){
        echo $row['id'].'-'.$row['uid'].'-'.$row['username'].'-'.$row['userpwd'].'-'.$row['nick_name'].'-'.$row['role_group'].'-'.$row['is_enabled'].'-'.$row['last_time'];
    }
*/
    $itemData = [];         //保存分类信息
    $warehouseData = [];    //保存仓库信息

    //查询分类信息，并保存在$itemData[]中
    $result = $mysql->select('item_t', 'id, item_name, parent_id, is_ended');
    while($row = mysqli_fetch_assoc($result)){
        $itemData[] = $row;
    }
    $itemData = json_encode($itemData,JSON_UNESCAPED_UNICODE);

    //查询仓库信息，并保存在$warehouseData[]中
    $result = $mysql->select('warehouse_t', 'id, warehouse_name');
    while($row = mysqli_fetch_assoc($result)){
        $warehouseData[] = $row;
    }
    $warehouseData = json_encode($warehouseData,JSON_UNESCAPED_UNICODE);

    echo $warehouseData;
    echo $itemData;
?>
<!--添加分类-->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<script type="text/javascript" src="http://libs.baidu.com/jquery/1.9.1/jquery.min.js"></script>
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
</body>

<!---------script---------->
<script>

    window.onload = function(){
        let itemJsonStr = '<?php echo $itemData; ?>'; 
        let itemJsonObj = JSON.parse(itemJsonStr);              //记录分类信息，转换为JSON对象。

        let warehouseJsonStr = '<?php echo $warehouseData; ?>';
        let warehouseJsonObj = JSON.parse(warehouseJsonStr);    //记录仓库信息，转换为JSON对象

        //定义分类列表显示相关变量
        let itemSelectId = 0;                                   //初始分类为0
        let itemListObj = document.getElementById('itemList');    //获取需要添加select的对象
        let itemSelectName = -1;                                //第一个分类select列表Id

        //定义仓库列表显示相关变量
        let warehouseListObj = document.getElementById('warehouseList');


        //显示仓库信息
        showWarehouseInfo(warehouseJsonObj, warehouseListObj);
        
        //调用显示下一级分类
        addItemSelect(itemListObj, itemSelectId, itemSelectName, itemJsonObj);
    }

    //显示仓库信息--------------------------------------
    function showWarehouseInfo(warehouseJsonObj, warehouseListObj){
        let str ="<label><input type = 'radio' checked = 'checked' name = 'warehouse' value = '";
        str += warehouseJsonObj[0].id;
        str +="'/>" + warehouseJsonObj[0].warehouse_name + "</label>";
        for(let i = 1; i < warehouseJsonObj.length; i++){
            str +="<label><input type = 'radio' name = 'warehouse' value = '";
            str += warehouseJsonObj[i].id;
            str +="'/>" + warehouseJsonObj[i].warehouse_name + "</label>";
        }
        warehouseListObj.innerHTML = str;
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
        return itemSelectId;
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

        //判断是否选择了空项，默认第一项提示为空
        if(!(0 == will_selectedId)){
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

    //同级分类select的name为共同上级分类select的id，同级分类在同一个select列表显示-----------------
    function  countSelect(sname){
        let selectArr = document.getElementsByTagName("select");    //获取当前已存在的select列表
        let selectName = "itemSelectName_" + sname;                 //获取要创建的select的Name
        
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
        let warehouseValue = getWarehouseRadioValue();
        let itemSelectList = getItemSelectOptionValueList();

        let itemName = document.getElementById('itemNameInput').value;
        let itemCount = document.getElementById('itemCountInput').value;

        //alert(itemName);
        //alert(itemCount);
        //alert(warehouseValue);
        for(let i = 0; i < itemSelectList.length; i++){
            alert(itemSelectList[i]);
        }
    }
    
    //获取当前选择的仓库
    function getWarehouseRadioValue(){
        let radioObj = document.getElementsByName('warehouse');
        for(let i = 0; i < radioObj.length; i++){
            if(true == radioObj[i].checked){
                return radioObj[i].value;
            }
        }
    }

    //获取当前选择的分类
    function getItemSelectOptionValueList(){
        let selectArr = document.getElementsByTagName("select");
        let val = [];

        for(let i = 0; i < selectArr.length; i++){
            if((selectArr[i].options[selectArr[i].selectedIndex].value.split("_")[1])){
                val[i] = selectArr[i].options[selectArr[i].selectedIndex].value.split("_")[1];
            }
        }
        return val;
    }

</script>
</html>



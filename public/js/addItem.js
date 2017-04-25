//页面加载初始化，默认显示所有分类信息，实现无限分类菜单

var itemJsonObj;
var warehouseJsonObj;
window.onload = function () {

    initData();
}

//初始化内容
function initData() {

    //初始化仓库信息
    $.ajax({
        type: "post",
        url: "getItemService.php",
        data: { "tableName": "warehouse" },
        dataType: "json",
        cache: false,
        success: function (warehouseJsonObj_s) {
            let warehouseListObj    = document.getElementById('warehouseList');
            warehouseListObj.innerHTML = "";
            warehouseJsonObj = warehouseJsonObj_s;
            showWarehouseInfo(warehouseJsonObj, warehouseListObj);
            loadAjaxGetData();
        },
        error: function (msg, e) {
            alert("请求的数据发生异常：" + e);
        }
    });
}

//ajax()方法加载分类数据,返回成功调用showSubItem()初始化分类显示
function loadAjaxGetData() {

    $.ajax({
        type: "post",
        url: "getItemService.php",
        data: { "tableName": "item" },
        dataType: "json",
        cache: false,
        success: function (itemJsonObj_s) {
            let itemListObj         = document.getElementById('itemList');  //获取需要添加select的对象
            itemListObj.innerHTML = '';
            let itemSelectId        = 0;                                    //初始分类为0
            let itemSelectName      = -1;                                   //第一个分类select列表name属性的值
            itemJsonObj = itemJsonObj_s
            showSubItem(itemListObj, itemSelectId, itemSelectName, itemJsonObj);
        },
        error: function (msg, e) {
            alert("请求的数据发生异常：" + e);
        }
    });
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
                    str +="<label><input id = 'warehouseInput_" + warehouseJsonObj[i].warehouse_id + "' type = 'radio' checked = 'checked' name = 'warehouse' value = '";
                    str += warehouseJsonObj[i].warehouse_id;
                    str +="'/>" + warehouseJsonObj[i].warehouse_name + "</label>";
                }
                else {
                    str +="<label><input id = 'warehouseInput_" + warehouseJsonObj[i].warehouse_id + "' type = 'radio' name = 'warehouse' value = '";
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

        let currentSelectedItem = choose.options[choose.selectedIndex].value;

        if(currentSelectedItem.indexOf('-') != -1){
            currentSelectedItemIDArr = currentSelectedItem.split("-");
            currentSelectedId = currentSelectedItemIDArr[currentSelectedItemIDArr.length - 1];
        }
        else{
            currentSelectedId = choose.options[choose.selectedIndex].value.split('_')[1];

        }

        //获取当前select的ID号，当前select的ID号作为下一个创建的select的Name号，用来表示分类等级，name号一样，表示同一级分类
        currentSelectedName  = choose.id.split("_")[1];

        //当前选择分类项改变时，所属仓库显示同步
        setWarehouseRadio(choose);

        //当前选择分类项改变时，下一级分类显示同步：
        //先移除当前分类下所有显示的子分类，然后通过showSubItem()显示为更换分类选择后的子分类
        removeSubItem(currentSelectedName);

        //判断是否选择了空项，默认第一项提示为空，选择空项时，不显示下一级分类，否则继续显示子分类
        if(0 != currentSelectedId){
            //获取到当前选择项在itemJsonObj中的位置
            for(let i = 0; i < itemJsonObj.length; i++) {
                if(currentSelectedId == itemJsonObj[i].id){
                    currentSelectedId = i;
                    break;
                }
            }

            //当前选择项的is_ended的值为0表示有子分类，显示子分类
            if( 0 == itemJsonObj[currentSelectedId].is_ended) {
                showSubItem(itemListObj, itemJsonObj[currentSelectedId].id, currentSelectedName, itemJsonObj);
            }
        }
    }

    //根据选择分类，同步仓库显示状态
    function setWarehouseRadio(choose){

        if(choose.id == 'itemSelectId_0' && choose.options[choose.selectedIndex].value != 'itemOption_0'){
            let currentSelectedItemID = choose.options[choose.selectedIndex].value.split('_')[1];

            let inputRadioID;
            for(let i = 0; i < itemJsonObj.length; i++){
                if(itemJsonObj[i].id == currentSelectedItemID) {
                    if(itemJsonObj[i].warehouse_id == 0) {
                        return 0; //当前选择项的仓库ID为0.则终止同步仓库操作。
                    }
                    inputRadioID = 'warehouseInput_' +  itemJsonObj[i].warehouse_id;
                    break;
                }
            }
            document.getElementById(inputRadioID).checked = 'checked';
        }

        let warehouseArr = document.getElementsByName('warehouse');

        //先判断是否存在仓库信息。
        if(typeof(warehouseArr) != "undefined"){
            if(choose.id == 'itemSelectId_0' && choose.options[choose.selectedIndex].value == 'itemOption_0') {
                //仓库可选
                for(let i = 0; i < warehouseArr.length; i++){
                    warehouseArr[i].disabled = false;
                }
            }else{//仓库不可选
                for(let i = 0; i < warehouseArr.length; i++){
                    warehouseArr[i].disabled = true;
                }
            }
        }
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

        let warehouseId     = 0;                             //存储当前选择的仓库信息
        let itemSelectId    = getItemSelectOptionValueList();                       //存储当前将要添加的分类的上一级分类ID

        let itemJSON;                                                               //构建JSON格式数据用来整体提交数据

        itemName            = itemName.replace(/(^\s*)|(\s*$)/g, "");
        //itemCount           = itemCount.replace(/(^\s*)|(\s*$)/g, "");              //去除输入内容中首尾的空格
        if(itemSelectId == 0) {
            warehouseId = getWarehouseRadioValue();
        }else{
            parentID = itemSelectId.split('-')[0];
            for(let i = 0; i < itemJsonObj.length; i++){
                if(itemJsonObj[i].id == parentID) {
                    warehouseId = itemJsonObj[i].warehouse_id;
                }
            }
        }

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
                initData();
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
                            alert("名称中不能含有空格！");
                        }
                        else{
                            alert("名称中不能含有字符：" + specialCharacter[j]);
                        }

                        //document.getElementById('itemNameInput').focus();
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
            //let optionValueArr = selectArr[i].options[selectArr[i].selectedIndex].value.split('-');
            //let optionNumber = optionValueArr[optionValueArr.length - 1];

            if((typeof(optionNumber) != "undefined") && (optionNumber != 0)){
                //val[i] = selectArr[i].options[selectArr[i].selectedIndex].value.split("_")[1];
                val[i] = optionNumber;
            }
        }
        //获取所有祖先分类ID，返回父分类ID

        return val.length ? val[val.length - 1]: 0;
    }

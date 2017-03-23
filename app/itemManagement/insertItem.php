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
    $data = [];
    $result = $mysql->select('item_t', '*');
    while($row = mysqli_fetch_assoc($result)){
        $data[] = $row;
    }
    $data = json_encode($data,JSON_UNESCAPED_UNICODE);
    //echo $data;
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<script type="text/javascript" src="http://libs.baidu.com/jquery/1.9.1/jquery.min.js"></script>
</head>
<body>
    <div id = "itemBox"></div>
    <button id = "addItem2" onclick = "">添加</button>
</body>

<script>
    let existSelectListCount = 0;
    let existSelectList = [];                                   //记录已经存在的select列表

    window.onload = function(){
        let itemJsonStr = '<?php echo $data; ?>'; 
        let itemJsonObj = JSON.parse(itemJsonStr);              //从后台获取分类信息，转换称JSON对象。

        let itemSelectId = itemJsonObj[0].parent_id;
        let itemBoxObj = document.getElementById('itemBox');    //获取需要添加select的对象
        let itemSelectName = -1;

        //调用添加分类 功能

        createItemSelect(itemBoxObj, itemSelectId, itemSelectName, itemJsonObj);
    }

    //---------------定义添加分类方法功能--------------
    //return 当前创建成功的select的序号即分类号
    function createItemSelect(itemBoxObj, itemSelectId, itemSelectName, itemJsonObj){

        let itemSelectObj = document.createElement('select');
        let itemOptionObj = document.createElement('option');
        
        //创建一个select时的添加“请选择分类”项为默认值
        itemSelectObj.id = "itemSelectId_" + itemSelectId;
        itemSelectObj.name = "itemSelectName_" + itemSelectName;
        
        itemOptionObj.value= 'itemOption_0';
        itemOptionObj.text = "请选择分类";

        itemSelectObj.appendChild(itemOptionObj);

        //添加option
        for(let i = 0; i < itemJsonObj.length; i++){

            let itemOptionObj = document.createElement('option');

            if(itemSelectId == itemJsonObj[i].parent_id){
               itemOptionObj.value = 'itemOption_' + itemJsonObj[i].id;
               itemOptionObj.text = itemJsonObj[i].item_name;
               itemSelectObj.appendChild(itemOptionObj);
           }
        }
        //为每个select绑定change事件
        itemSelectObj.addEventListener('change',function(){

            itemChange(this, itemBoxObj,  itemJsonObj);
        });

        itemBoxObj.appendChild(itemSelectObj);
        return itemSelectId;
    }
    
    //-----------------select的change事件-------------------
    function itemChange(choose, itemBoxObj, itemJsonObj){

        let will_selectedId = 0;        //保存要创建子分类的select的ID号码
        let is_selectedId = 0;          //保存当前选择项的select的ID号
        let will_selectedName = 0;      //保存要创建子分类的select的name号

        //子分类select的ID号码为当前选择项option的id号码
        will_selectedId = (choose.options[choose.selectedIndex].value).split("_")[1];

        //获取当前select的ID号，当前select的ID号作为下一个创建的select的Name号，用来表示分类等级，name号一样，表示同一级分类
        will_selectedName  = choose.id.split("_")[1];

        //判断是否选择了空项，默认第一项提示为空
        if(0 == will_selectedId){
            return false;
        }

        countSelect(will_selectedName);
        /*
        //通过select的id序号，判断要创建的select是否已经存在，已存在时不再重复创建
        let i = 0;
        while(existSelectList[i]){
            
            if(existSelectList[i] == will_selectedId) {
                return false;
            }
            i++;
        }
        */
        //获取到当前选择项在itemJsonObj中的位置
        for(let i = 0; i < itemJsonObj.length; i++) {
            if(will_selectedId == itemJsonObj[i].id){
                will_selectedId = i;
                break;
            }
        }

        //当前选择项有子分类时，显示子分类
        if( 0 == itemJsonObj[will_selectedId].is_ended) {
            //existSelectList[existSelectListCount++] = createItemSelect(itemBoxObj, itemJsonObj[will_selectedId].id, will_selectedName, itemJsonObj);
            createItemSelect(itemBoxObj, itemJsonObj[will_selectedId].id, will_selectedName, itemJsonObj);
        }
    }

    //同级分类select的name为共同上级分类select的id，同级分类在同一个select列表显示，
    function  countSelect(sname){
        let selectArr = document.getElementsByTagName("select");    //获取当前已存在的select列表
        let selectName = "itemSelectName_" + sname;                 //获取要创建的select的Name

        //在selectArr列表中查找已存在的selectName，当找到时候，移除当前列表及其后面的同胞节点。
        //返回上层function时，重新创建下一级select，达到同级分类显示在同一个select列表
        for(let i = 0; i < selectArr.length; i++){
            if(selectArr[i].name == selectName){

                let selectId = selectArr[i].id;

                $(document).ready(function(){

                    $("#" + selectId).nextAll().remove();
                    $("#" + selectId).remove();
                });
            }
        }
    }
</script>
</html>



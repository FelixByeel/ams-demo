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
    echo $data;
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<script type="text/javascript" src="http://libs.baidu.com/jquery/1.9.1/jquery.min.js"></script>
</head>
<body>
    <div id = "itemBox"></div>
    <button id = "addItem0" onclick = "showTest()">测试</button>
    <button id = "addItem1" onclick = "addItem()">添加分类</button>
    <button id = "addItem2" onclick = "addNow()">添加</button>
</body>

<script>

    //响应添加分类操作
    function addItem(){
        let itemJsonStr = '<?php echo $data; ?>'; 
        let itemJsonObj = JSON.parse(itemJsonStr); //从后台获取分类信息，转换称JSON对象。

        //获取需要添加select的对象
        let itemBoxObj = document.getElementById('itemBox');
        let itemSelectId = 0;   //每次创建一个select，并为其添加ID
        let is_selectedId = 0; //每个option的ID

        //调用添加分类 功能
        createItemSelect(itemBoxObj, itemSelectId, itemJsonObj, is_selectedId);
    }

    //定义加分类方法功能
    function createItemSelect(itemBoxObj, itemSelectId, itemJsonObj, is_selectedId){

        let itemSelectObj = document.createElement('select');
        let itemOptionObj = document.createElement('option');

        //创建一个select时的添加“请选择分类”项为默认值
        itemSelectObj.id = "itemSelect" + itemSelectId++;
        itemOptionObj.value= 'itemOption_0';
        itemOptionObj.text = "请选择分类";

        itemSelectObj.appendChild(itemOptionObj);

        //添加option
        for(let i = 0; i < itemJsonObj.length; i++){

            let itemOptionObj = document.createElement('option');

            if(is_selectedId == itemJsonObj[i].parent_id){
               itemOptionObj.value = 'itemOption_' + itemJsonObj[i].id;
               itemOptionObj.text = itemJsonObj[i].item_name;
               itemSelectObj.appendChild(itemOptionObj);
           }
        }
        //为每个select绑定change事件
        itemSelectObj.addEventListener('change',function(){
            itemChoose(this, itemBoxObj, itemSelectId, itemJsonObj);
        });

        itemBoxObj.appendChild(itemSelectObj);
    }
    
    //select的change事件
    function itemChoose(choose, itemBoxObj, itemSelectId, itemJsonObj){
        is_selectedId = (choose.options[choose.selectedIndex].value).split("_")[1];

        //当前项没有子分类时停止创建select
        if(0 == itemJsonObj[is_selectedId - 1].is_ended) {
            //alert(is_selectedId);
            //alert(itemJsonObj[is_selectedId-1].item_name);
            createItemSelect(itemBoxObj, itemSelectId, itemJsonObj, is_selectedId);
        }
    }


    //测试数据
    function showTest(){
        let itemJsonStr = '<?php echo $data; ?>'; 
        let itemJsonObj = JSON.parse(itemJsonStr); //从后台获取分类信息，转换称JSON对象。

        for(let i = 0; i < itemJsonObj.length; i++){
           if(0 == itemJsonObj[i].is_ended){
               alert(itemJsonObj[i].is_ended);
           }
        }
    }

</script>
</html>


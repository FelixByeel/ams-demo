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
    $tableName = 'item_t';
    $col = '';
    
    //$result = $mysql->select($tableName, $col, $);
?>

<html>
<head>
<script>
    function onChange(){
        let selectObj = document.getElementById
    }
</script>
</head>
<body>
    <div id = "formBox">

        <label>选择分类</label>
        <select id = "item_0">
            <option value="0">无</option>
            <option value="1">硬盘</option>
            <option value="2">内存</option>
            <option value="3">电源</option>
            <option value="11">键盘</option>
        </select>

        <select id = "item_1">
            <option value="0">无</option>
            <option value="4">SSD硬盘</option>
            <option value="27">台式机硬盘</option>
            <option value="28">笔记本硬盘</option></option>
        </select>

        <label for = "itemName" >输入分类名称：</label>
        <input id = "itemName" name = "itemName" type = "text" />
    </div>
</body>

</html>



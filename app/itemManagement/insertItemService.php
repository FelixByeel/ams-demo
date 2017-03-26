<?php
/*
*处理接收的JSON数据，添加到数据库
*/
$itemData =  Array();

if(isset($_POST['itemData'])) {
    foreach ($_POST['itemData'] as $key => $value) {
        $itemData
    }
    $itemData = $_POST['itemData'];
}
else{
    die("请求异常！");
}

echo "长度".count($itemData);


foreach ($itemData as $key => $value) {
    if(empty($value))  {
        echo "<br />";
        echo '空值->'.$key.' : '.$value;
    }
    else{
        echo "<br />";
        echo $key.' : '.$value;
    }


}
echo "<br />";



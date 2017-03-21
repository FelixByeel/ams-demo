<?php
    define('APP_ROOT', dirname(dirname(__DIR__)).'/');
    require_once (APP_ROOT.'include/dbConfig.php');
    require_once (APP_ROOT.'include/Msqli.class.php');

    
?>
<div id = "formBox">
    <label for = "itemName" >分类名称：</label>
    <input id = "itemName" name = "itemName" type = "text" />
    <label>上级分类</label>
    <select id = "parentItem" name = "parentItem" >
        <option value="noParent">----无----</option>
        <option value="1">----1----</option>
    </select>
</div>


<?php
//已有登陆状态，自动跳转主页
session_start();

if(isset($_SESSION['uid']) && !empty($_SESSION['uid'])){
    echo "
    <script language='javascript' type='text/javascript'>
        window.location.href='main.php';
    </script>
    ";
}
?>
<!Doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title>登录</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<script type="text/javascript" src="public/js/jquery-1.8.3/jquery.js"></script>
<script src = "public/js/cookie.js"></script>
<script src = "public/js/index.js"></script>
<style type = "text/css">
html,body{
    margin:0;
    padding:0;
    text-align:center;
    font-family:"Microsoft YaHei", "Varela Round", Arial,"SimHei", Helvetica, sans-serif;
    background-color:#ddd;
}

#login_box{
    width:100%;
    margin-top:10%;
    box-shadow:1px 1px 20px 10px #000;
    -webkit-box-shadow:1px 1px 20px 10px #000;
    background-color:#0B7781;
}

p{
    padding-top:20px;
    color:#FFF;
    font-size:2em;
}
#errorinfo{
    display:block;
    color:#FE5F55;
}

#form_box{
    width:200px;
    margin:0 auto;
    padding-bottom:30px;
}

input{
    list-style-type:none;
    border-radius:3px;
    -webkit-border-radius:3px;
    border:1px solid #444;
    font-family:"Microsoft YaHei", "Varela Round", Arial,"SimHei", Helvetica, sans-serif;
    color:#000;
    background-color:#DDD;
}

#username,#userpwd{
    width:200px;
    height:25px;
    line-height:25px;
    font-size:14px;
}

#login_btn{

    margin-top:20px;
    appearance:none;
    -webkit-appearance:none;
    width:200px;
    height:30px;
    border:1px solid #F79F79;
    box-shadow:1px 1px 1px 1px #000000 ;
    -webkit-box-shadow:1px 1px 1px 1px #000000;
    border-radius: 5px;
    font-size:1em;
    background-color:#F79F79;
    color:#000;
    font-family:"Microsoft YaHei", "Varela Round", Arial,"SimHei", Helvetica, sans-serif;
}

#login_btn:hover{
    cursor:pointer;
}

#login_btn:active{
    position:relative;
    top:1px;
}

label,#forget{
    font-size:14px;
    color:#FFF;
}
a#forget:hover{
    color:#00BBFF;
}
</style>
</head>
<body>
<div id = "login_box">
    <p>资产管理系统</p>
    <hr style="width:99%"/>
    <span id="errorinfo"></span>
        <div id = "form_box">
                <br/>
                <input id = "username" name="username" type = "text" placeholder="用户名" value=""/>
                <br/>
                <br/>
                <input id = "userpwd" name= "userpwd" type = "password" placeholder="密码" value=""/>
                <br/>
                <br/>
                <label>
                    <input id = "rememberpwd" name = "rememberpwd" type="checkbox"/>记住密码
                </label>
            <button id = "login_btn" onclick = "login_submit()">登&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;录</button>
        </div>
</div>
</body>
</html>

<?php
//登录验证
session_start();

if(!isset($_SESSION['uid']) || empty($_SESSION['uid'])){
    die("<span>请登陆后操作！3秒后自动跳转。</span><a href = 'index.php'>未跳转？点击这里。</a>
    <script language='javascript' type='text/javascript'>
        var timer = setInterval('goToLogin()', 3000);
        function goToLogin () {
            window.location.href='index.php';
        }
    </script>
    ");
}
?>
<!DOCTYPE html>
<html>
<head>
<title>资产管理系统</title>
<META http-equiv="Content-Type" content="text/html;  charset=UTF-8;">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="0">
<link href="public/css/main.css" rel="stylesheet" type="text/css" />
<link href="public/css/defaultPage.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="public/js/jquery-1.8.3/jquery.js"></script>
<script type="text/javascript" src="public/js/defaultPage.js"></script>
<script>

//show profile box
function showProfileBox() {
    let obj = document.getElementById('profile_box');
    obj.style.display = "block";
}
function hideProfileBox() {
    let obj = document.getElementById('profile_box');
    obj.style.display = "none";
}

$(document).ready(function(){

    $("#welcome").click(function () {
        showProfileBox();
    });

    $("#profile_box").mouseleave(function(){
        hideProfileBox();
    });

    $("#profile_box").click(function(){
        hideProfileBox();
    });
});

//创建tab
function createTab(tab_id,title){
    var obj = document.getElementById("tab_ul");
    var tab = document.createElement("li");

    tab.id = "tab_"+tab_id;

    if(!document.getElementById(tab.id)){
        //tab.innerHTML ="<span class = 'tab_title' onmouseover = 'showCompleteTitle();' title = '" + title + "'>" + title + "</span>" +"<span"+" id ='close_"+tab_id+"' class = 'close'>&times;</span>";
        tab.innerHTML ="<span class = 'tab_title'>" + title + "</span>" + "<span" + " id ='close_" + tab_id + "' class = 'close' title = '关闭'>&times;</span>";
        obj.appendChild(tab);
        $('#'+tab.id).attr("class","tab_enable");
        $('#'+tab.id).siblings().attr("class","tab_disable");
    }
    else{
        $('#'+tab.id).attr("class","tab_enable");
        $('#'+tab.id).siblings().attr("class","tab_disable");
    }
}

// 创建内容页
function createDiv(div_id,div_url){
    var obj = document.getElementById("content");
    var con = document.createElement("div");
    con.id = "con_"+div_id;
    if(!document.getElementById(con.id)){
        obj.appendChild(con);
        $("#"+con.id).attr("class","div_enable");

        let frameName = "framePage_" + div_id;
        $("#"+con.id).append("<iframe style = 'border:0px; position: absolute; width:100%; height:100%' name = '" + frameName +"'></iframe>");
        window.open(div_url, frameName);
        //$("#"+con.id).load(div_url,{"con":div_id});
        $("#"+con.id).siblings().attr("class","div_disable");
    }
    else{
        $("#"+con.id).attr("class","div_enable");
        $("#"+con.id).siblings().attr("class","div_disable");
    }
}
//调用 function createTab(param,param) 和 function createDiv(param,param) 生成tab页面
function  loadCon(con_id,con_url,con_title){
    $(document).ready(function(){
        createTab(con_id,con_title);
        createDiv(con_id,con_url);
    });
}

//切换TAB
$(document).ready(function(){
    $("#tab_ul").on("click","li",function(){
        var tab_id = this.id;
        var con = "con_";
        var con_id = con + tab_id.substr(4);

        $("#" + tab_id).attr("class","tab_enable");
        $("#" + tab_id).siblings().attr("class","tab_disable");

        $("#"+con_id).attr("class","div_enable");
        $("#"+con_id).siblings().attr("class","div_disable");
    });
});

//关闭TAB
$(document).ready(function(){
    $("#tab_ul").on("click","span",function(){
        var close_id = this.id;
        var str_id = close_id.substr(6);
        var tab_id = "tab_" + str_id;
        var con_id = "con_" + str_id;
        if(this.id.substr(0,5) =='close' ){

            if($("#" + con_id).is(":hidden")){
                $("#" + tab_id).remove();
                $("#" + con_id).remove();
            }
            else{
                $("#" + tab_id).prev().attr("class","tab_enable");
                $("#" + con_id).prev().attr("class","div_enable")
                $("#" + tab_id).remove();
                $("#" + con_id).remove();
            }

        }
    });
});
</script>

</head>
<body>
    <div id = "bodybox">
        <!--头部信息-->
        <div id = "headbox">
            <span id="title">资产管理系统-AMS</span>
            <span id="welcome">
                    <?php  echo $_SESSION['nick_name'];?>，欢迎。
            </span>
            <div id = "profile_box" >
                <ul id = "profile_box_ul">
                    <li onClick = "loadCon('modify-password','app/userManagement/modify-password.php','修改密码');">修改密码</li>
                    <li onClick = "location.href='app/login/logOff.php'">注销</li>
                </ul>
            </div>
        </div>

        <!-- 导航栏-->
        <div id = "nav_box">
                <ul id = "nav_ul">

                    <?php
                        if (($_SESSION['username'] == 'admin') && $_SESSION['role_group'] == 99) {
                            echo "
                                <li class = 'nav_li' onclick = \"loadCon('user-management', 'app/userManagement/userManagement.php', '用户管理');\">用户管理</li>
                                <li class = 'nav_li' onclick = \"loadCon('set-default-password', 'app/userManagement/set-default-password.php', '密码重置');\">密码重置</li>
                            ";
                        }
                        else{

                            echo "
                                <li class = 'nav_li' onclick = \"loadCon('searchRecord','app/recordManagement/searchRecord.php','记录查询');\">记录查询</li>
                                <li class = 'nav_li' onclick = \"loadCon('searchItem-checkout','app/itemManagement/searchItem-checkout.php','库存查询');\">库存查询</li>
                            ";

                            if ($_SESSION['role_group'] >= 2) {
                                echo "
                                    <li class = 'nav_li' onclick = \"loadCon('searchItem-edit','app/itemManagement/searchItem-edit.php','分类管理');\">分类管理</li>
                                    <li class = 'nav_li' onclick = \"loadCon('addItem','app/itemManagement/addItem.php','添加分类');\">添加分类</li>
                                    <li class = 'nav_li' onclick = \"loadCon('addwarehouse','app/warehouseManagement/addWarehouse.php','仓库管理');\">仓库管理</li>
                                ";
                            }
                        }
                    ?>
                </ul>
        </div>

        <!-- 主体内容区 -->
        <div id="main_box">
            <!--TAB标签box-->
            <div id = "tab_box">
                <ul id = "tab_ul">
                    <li id = "tab_default" class = "tab_enable"><span class="tab_title default-page-title" >首页</span><span class = "refresh" title = "刷新" onclick = "loadAllPage()">&#8631;</span></li>
                </ul>
            </div>
            <!-- 内容 -->
            <div id = "content">
                <!--default page start-->
                <div id = "con_default" class = "default-page div_enable">
                    <div class = "checkout-chart-box">
                        <p class = "checkout-chart-title title">出库物品数量统计</p>
                        <div id = "itemSelectBox" class = "item-select-box"></div>
                        <div id = "loadChart" style = "display:hidden"></div>
                        <img id = "checkoutChart" class = "checkout-chart">
                    </div>
                    <div class = "recent-checkout-box">
                        <p class = "recent-checkout-title title">最近出库记录</p>
                        <div id = "recentCheckout" class = "recent-checkout"></div>
                    </div>
                    <div class = "item-count-warning-box">
                        <p class = "item-count-warning-title title">库存预警</p>
                        <div id = "itemCountWarning" class = "item-count-warning"></div>
                    </div>
                </div>
                <!--default page end-->
            </div>
        </div>
        <!--footer-->
        <div id = "footer">&copy; <?php echo date('Y'); ?>.</div>
    </div>
</body>
</html>


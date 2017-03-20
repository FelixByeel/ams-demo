<?php
//登录验证

//加载连接数据库相关文件。

if(isset($_POST['username']) && isset($_POST['userpwd'])){
    $username = $_POST['username'];
    $password = $_POST['userpwd'];
    //$tb_name = 'user_info';
    //$condition = "username = '$username' and password = '$password'";
    $sql = "select * from user_info where username = '$username' and password = '$password'";
    $result = mysql_query($sql);
    //验证成功
    if($row = mysql_fetch_array($result)){
        //检测是否记住密码，设置相应cookie
        if($_POST['rememberpwd']){
            $domain = ($_SERVER['HTTP_HOST'] != 'localhost')?$_SERVER['HTTP_HOST']:false;
            setrawcookie("name",$username,time()+365*24*3600,'/',$domain,false);
            setrawcookie("pwd",$password,time()+365*24*3600,'/',$domain,false);
            setrawcookie("check",1,time()+365*24*3600,'/',$domain,false);
            
        }
        else{
            setrawcookie("name",'',time()-365*24*3600,'/',$domain,false);
            setrawcookie("pwd",'',time()-365*24*3600,'/',$domain,false);
            setrawcookie("check",'',time()-365*24*3600,'/',$domain,false);
        }
        
        session_start();
        $_SESSION['username'] = $row['username'];
        $_SESSION['password'] = $row['password'];
        $_SESSION['userflag'] = $row['userflag'];
        
        echo "<script language='javascript' type='text/javascript'> 
                window.location.href='app/welcome.php';
              </script>";
    }
    else{
        //验证失败
        echo '用户名或密码不正确！';
    }
}
else{
    //未登录
    echo "<script language='javascript' type='text/javascript'> 
            window.location.href='../../';
          </script>";
}

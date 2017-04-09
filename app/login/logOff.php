<?php
//注销session
session_start();
unset($_SESSION['username']);
unset($_SESSION['nick_name']);
unset($_SESSION['role_group']);
unset($_SESSION['uid']);
echo "<script language='javascript' type='text/javascript'> 
        window.location.href='../../';
      </script>";

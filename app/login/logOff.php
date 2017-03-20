<?php
//注销session
session_start();
unset($_SESSION['username']);
unset($_SESSION['password']);
unset($_SESSION['userflag']);
echo "<script language='javascript' type='text/javascript'> 
        window.location.href='../../';
      </script>";

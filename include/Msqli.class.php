<?php
/*
*@Purpose:      封装常用CURD操作
*@Class Name:   Msqli
*@Author:       felix
*@Date:         2017-02-16
*
*@Param $host
*@Param $port
*@Param $db_name
*@Param $username
*@Param $userpwd
*
*/

class Msqli{
    private $host       = 'localhost';
    private $db_name    = 'ams_db';
    private $port       = '3306';
    private $username   = 'root';
    private $userpwd    = '123456';
    private $con        = null;
    private $error      = null;
    private $errno      = null;
    private $result     = null;

    public function __construct($host,$username,$userpwd,$db_name,$port) {
        $this->host     = $host;
        $this->username = $username;
        $this->userpwd  = $userpwd;
        $this->db_name  = $db_name;
        $this->port     = $port;
        $this->con = mysqli_connect($this->host,$this->username,$this->userpwd,$this->db_name,$this->port) or die('连接失败');
    }

    //get error information
    public function getError() {
        return mysqli_error();
    }
    public function getErrno() {
        return mysqli_errno();
    }

    //execute query and return result
    public function query($sql) {

        $this->result =  mysqli_query($this->con,$sql);

        if($this->result){
            return $this->result;
        }
        else {
            return false;
        }
    }

    //select
    public function select($tablename, $column, $condition = '') {
        if(empty($condition)) {
            $sql = "select $column from $tablename";
        }
        else {
            $sql = "select $column from $tablename where $condition";
        }
        //echo $sql;
        return $this->query($sql);
    }

    //insert
    public function insert($tablename, $column, $value) {

        $sql = "insert into $tablename($column)values($value)";

        $this->query($sql);
    }

    //update
    public function update($tablename, $col_val, $condition = '') {
        if(empty($condition)) {
            $sql = "update $tablename set $col_val";
        }
        else {
            $sql = "update $tablename set $col_val where $condition";
        }
        $this->query($sql);
    }

    //delete
    public function delete($tablename,$condition) {

            $sql = "delete from $tablename where $condition";
            $this->query($sql);
    }

    //check input
    public function checkInput($str){
        $str = mysqli_real_escape_string($str);
        return $str;
    }
}

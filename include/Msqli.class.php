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

    //return $this->con
    public function getLink(){
        return $this->con;
    }

    //get mysqli_affected_rows
    public function getAffectedRows(){
        return mysqli_affected_rows($this->con);
    }
    //get error information
    public function getError() {
        return mysqli_error($this->con);
    }
    public function getErrno() {
        return mysqli_errno($this->con);
    }

    //execute query and return result
    public function query($sql) {

        $this->result =  mysqli_query($this->con,$sql);

        return $this->result;
    }

    //select
    public function select($tablename, $column, $condition = '') {

        $sql = "select ";

        foreach ($column as $key => $value) {
            $sql .= $value.',';
        }

        $sql = rtrim($sql, ',');
        $sql .= " from $tablename";

        if (!empty($condition)) {
           $sql .= " where $condition";
        }

        //echo '<br />当前SQL语句：'.$sql;
        return $this->query($sql);
    }

    //insert
    public function insert($tablename, $itemData) {

        $sql = "insert into $tablename(";

        foreach ($itemData as $key => $value) {
            $sql .= $key.',';
        }

        $sql = rtrim($sql, ',');
        $sql .= ") values(";

        foreach ($itemData as $key => $value) {
            $sql .= "'".$value."'".',';
        }

        $sql = rtrim($sql, ',');
        $sql .= ")";

        echo '<br />当前SQL语句：'.$sql;
        return $this->query($sql);
    }

    //update
    public function update($tablename, $col_val, $condition = '') {

        $sql = "update $tablename set ";

        foreach ($col_val as $key => $value) {
            $sql .= "$key = '$value',";
        }

        $sql = rtrim($sql, ',');

        if(!empty($condition)) {
            $sql .= " where $condition";
        }

        echo '<br />当前SQL语句：'.$sql;
        return $this->query($sql);
    }

    //delete
    public function delete($tablename,$condition) {

        $sql = "delete from $tablename where $condition";
        return $this->query($sql);
    }
}

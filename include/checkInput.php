<?php
//过滤一些特殊符号
function inputFilter($str){
    $specialCharacter  = array('~', '`', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '=', '+', '[', ']', '{', '}', '\\', '|', ';', ':', '\'', '\"', ',', '<', '.', '>', '?');

    foreach ($specialCharacter as $key => $value) {
        $str = str_replace($value, '', $str);
    }
    return $str;
}

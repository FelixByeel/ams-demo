<?php

function inputFilter($str){
    $specialCharacter  = array('~', '`', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '=', '+', '[', ']', '{', '}', '\\', '|', ';', ':', '\'', '\"', ',', '<', '.', '>', '?');

    foreach ($specialCharacter as $key => $value) {
        $str = str_replace($value, '', $str);
    }
}

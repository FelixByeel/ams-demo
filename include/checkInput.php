<?php
/**
*过滤一些特殊符号。
*
*找到特殊字符时返回该字符，否则返回false。
*
*@param string str
*/

function checkInput($str){
    $specialCharacter  = array('~', '`', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')',
                '=', '+', '[', ']', '{', '}', '\\', '|', ';', ':', '\'', '\"', ',', '<', '.', '>', '?',
                '~', '·', '！', '＠', '＃', '￥', '％', '………', '＆', '＊', '（', '）', '——', '＋', '＝',
                '【', '】', '｛', '｝', '、', '｜', '；', '：', '’', '“', '，', '《', '。', '》', '？', ' ', '　');

    foreach ($specialCharacter as $key => $value) {
        $result = strpos($str, $value);
        if ($result !== false) {
            break;
        }
    }

    if($result !== false){
        if((' ' == $str[$result]) || ('　' == $str[$result])){
            return '空格';
        }
        else{
            return $str[$result];
        }
    }
    else {
        return $result;
    }
}

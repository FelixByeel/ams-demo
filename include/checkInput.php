<?php
//过滤一些特殊符号,找到特殊字符，返回该字符，否则返回false
function checkInput($str){
    $specialCharacter  = array('~', '`', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-',
                '=', '+', '[', ']', '{', '}', '\\', '|', ';', ':', '\'', '\"', ',', '<', '.', '>', '?',
                '~', '·', '！', '＠', '＃', '￥', '％', '………', '＆', '＊', '（', '）', '——', '＋', '＝',
                '【', '】', '｛', '｝', '、', '｜', '；', '：', '’', '“', '，', '《', '。', '》', '？', ' ', '　');

    foreach ($specialCharacter as $key => $value) {
        $result = strpos($str, $value);
    }

    if($result){
        if((' ' == $specialCharacter[$result]) || ('　' == $specialCharacter[$result])){
            return '空格';
        }
        else{
            return $specialCharacter[$result];
        }
    }
    else {
        return $result;
    }
}

<?php
//过滤一些特殊符号
function checkInput($str){
    $specialCharacter  = array('~', '`', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-',
                '=', '+', '[', ']', '{', '}', '\\', '|', ';', ':', '\'', '\"', ',', '<', '.', '>', '?',
                '~', '·', '！', '＠', '＃', '￥', '％', '………', '＆', '＊', '（', '）', '——', '＋', '＝',
                '【', '】', '｛', '｝', '、', '｜', '；', '：', '’', '“', '，', '《', '。', '》', '？', ' ', '　');

    foreach ($specialCharacter as $key => $value) {
        $result = strpos($str, $value);
    }

    if($result){
        if((' ' == $specialCharacter[j]) || ('　' == $specialCharacter[j])){
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

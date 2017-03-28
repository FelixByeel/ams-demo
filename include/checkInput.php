<?php
//过滤一些特殊符号
function checkInput($str){
    $specialCharacter  = array('~', '`', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_',
                '=', '+', '[', ']', '{', '}', '\\', '|', ';', ':', '\'', '\"', ',', '<', '.', '>', '?',
                '~', '·', '！', '＠', '＃', '￥', '％', '………', '＆', '＊', '（', '）', '——', '＋', '＝',
                '【', '】', '｛', '｝', '、', '｜', '；', '：', '’', '“', '，', '《', '。', '》', '？');

    foreach ($specialCharacter as $key => $value) {
        $result = strpos($str, $value);
    }

    if($result){
        return $specialCharacter[$result];
    }
    else {
        return $result;
    }
}

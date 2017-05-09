/**
 * 字符串验证方法
 *
 *
 */
var checkInputStr = {

    //去除首尾所有空格
    trimSpace : function (str) {
        return str.replace(/(^\s*)|(\s*$)/g, "");
    },

    //是否为由数字组成的字符串，数字返回true,含有非数字返回false。
    isDigital : function (str) {
        var reg=/^[0-9]*$/;//匹配整数
        return reg.test(str);
    },

    //检测是否存在下列特殊字符，返回该特殊字符。未查找到特殊字符，返回false。
    isExistSpecialChar : function (str) {
        var specialCharacter = new Array('~', '`', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-',
                '=', '+', '[', ']', '{', '}', '\\', '|', ';', ':', '\'', '\"', ',', '<', '.', '>', '?',
                '~', '·', '！', '＠', '＃', '￥', '％', '………', '＆', '＊', '（', '）', '——', '＋', '＝',
                '【', '】', '｛', '｝', '、', '｜', '；', '：', '’', '“', '，', '《', '。', '》', '？', ' ', '　');

        for(var i = 0; i < specialCharacter.length; i++) {
            if(str.indexOf(specialCharacter[i]) != -1) {
                if(' ' == specialCharacter[i] || '　'　== specialCharacter[i]) {
                    return "空格";
                }else {
                    return specialCharacter[i];
                }
            }
        }
        return false;
    }
}


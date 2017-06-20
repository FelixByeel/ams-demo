/**
 * 字符串去除空格和字符串验证
 */
var checkInputStr = {

    /**
     * (method) trimSpace (str : string) : string 去除字符串首尾空格，返回处理后的字符串。
     */
    trimSpace : function (str) {
        return str.replace(/(^\s*)|(\s*$)/g, "");
    },

    /**
     * (method) isDigital (str : string) : boolean 验证字符串是否全部为数字，含有非数字返回false，否则返回true。
     */
    isDigital : function (str) {
        var reg=/^[0-9]*$/;
        return reg.test(str);
    },

    /**
     * (method) isExistSpaecialChar (str : string) : string | false 检测是否存在特殊字符，未查找到特殊字符，返回false，否则返回该特殊字符。
     */
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


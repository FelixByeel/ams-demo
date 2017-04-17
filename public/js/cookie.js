//设置cookie
function setCookie(name, value, days, path) {
    var name = escape(name);
    var value = escape(value);
    var expires = new Date();

    expires.setTime(expires.getTime() + days * 24 * 3600000);
    path = path == "" ? "" : ";path=" + path;
    _expires = (typeof hours) == "string" ? "" : ";expires=" + expires.toUTCString();
    document.cookie = name + "=" + value + _expires + path;
}

//获取cookie
function getCookieValue(name) {
    var name = escape(name);
    var allCookies = document.cookie;

    name += "=";

    var namePosition = allCookies.indexOf(name);

    if(namePosition != -1){
        var start = namePosition + name.length; 
        var end = allCookies.indexOf(";", start);

        if(end == -1) {
            end = allCookies.length;
        }

        var value = allCookies.substring(start, end);
        return value;
    }
    else return "";
}

//删除cookie
function deleteCookie(name, path) {
    var name = escape(name);
    var expires = new Date(0);

    path = path == "" ? "" : ";path=" + path;
    document.cookie = name + "=" + ";expires=" + expires.toUTCString() + path;
}

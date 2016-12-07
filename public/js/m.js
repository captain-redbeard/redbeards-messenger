/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 06-Dec-2016
 * Made Date: 28-Nov-2016
 * Author: Hosvir
 * 
 * */
 
function r(m, p, a, c, cb) {
    var x;
    window.XMLHttpRequest ? x=new XMLHttpRequest() : x=new ActiveXObject("Microsoft.XMLHTTP");
    x.onreadystatechange=function(){ if(x.readyState==4 && x.status==200) m ? cb(x.responseText, c) : cb(JSON.parse(x.responseText)); }
    x.open(m ? "POST" : "GET", p, true);
    x.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    x.send(a);
}

function a() {
    var g, c;
    g = document.getElementById('g');
    c = document.getElementById('c');
    r(false, "C/" + (g.innerHTML != null ? g.innerHTML : '') + "/" + (c.innerHTML != null ? c.innerHTML : ''), null, null, b);
}

function b(v) {
    if(v != null && v != "0") {
        var i, k, s, j, r, y, x, z;
        k = document.getElementById('l');
        s = document.getElementsByClassName("re");
        for(i = 0; i < s.length; i++) s[i].outerHTML='';
        for(i = 0; i < v.c.length; i++) {
            x = v.c[i];
            y = x.a != '' ? (x.a + ' - ') : '';
            document.getElementById('h').insertAdjacentHTML('afterbegin', '<a href="conversations/display/' + x.g + '/' + x.c + '#l"><div class="f"><div class="i">' + y + x.u + '</div><div class="cc j fr" data-md="">' + h(x.d) + ' &nbsp;<a href="delete-conversation/' + x.c + '"><div class="g id" alt="Delete Conversation" title="Delete Conversation" style="width: 10px; height: 10px;"></div></a></div><div class="z"></div></a>');
        }
        for(i = 0; i < v.m.length; i++) {
            j = v.m[i];
            r = j.f == true ? 'fr' : 'fl';
            z = j.f == true ? 's' : 'r';
            k.insertAdjacentHTML('beforebegin', '<div class="o ' + r + '"><div class="q ' + z + '">' + j.m + '</div><div class="z j aa ' + r + '" data-md="' + j.r + '">' + j.d + '</div></div><div class="p"></div>');
        }
        e("l");
    }
    a();
}

function c() {
    var a = document.getElementById('m');
    if(a.value.length > 0 && !/^\s+$/.test(a.value)) {
        r(true, "S", "g=" + document.getElementById('g').innerHTML + "&c=" + document.getElementById('c').innerHTML + "&m=" + a.value, a.value, d);
        a.value = '';
        a.select();
    }
    return false;
}

function d(v, x) {
    if(v == 1) {
        document.getElementById('l').insertAdjacentHTML('beforebegin', '<div class="re o fr"><div class="q s fr">' + x.replace(new RegExp('\n', 'g'), '<br/>') + '</div><div class="z j aa fr">now</div></div><div class="re p"></div>');
        e("l");
    }
}

function e(h) {
    var e = document.getElementById(h);
    if(e != null) e.scrollIntoView();
}

function f(v) {
    var p, l, n, g, t, i;
    p = ["second", "minute", "hour", "day", "week", "month", "year"];
    l = [60, 60, 24, 7, 4.35, 12, 10];
    n = Math.floor(new Date() / 1000);
    v = Math.floor(new Date(v) / 1000);
    g = n - v;
    t = "ago";    
    for(i = 0; g >= l[i] && i < p.length; i++) g /= l[i];
    g = Math.round(g);
    if(g != 1) p[i] += "s";
    return g + " " + p[i] + " " + t;
}

function g() {
    var t, j, i;
    t = document.getElementsByClassName("z j");
    j = 0;
    for(i = t.length-1; i > 0 && j < 20; i--) { t[i].innerHTML=f(t[i].getAttribute('data-md')); j++; }
}

function h(v) {
    var t, d, a, b, c, e, f;
    t = new Date().toLocaleString('en-GB', { hour12: true });
    d = new Date(v).toLocaleString('en-GB', { hour12: true });
    a = d.split("/");
    c = t.split("/");
    b = d.toUpperCase().split(",")[1].split(":");
    e = i(b[0].trim()) + ":" + b[1] + " " + b[2].split(" ")[1];
    f = i(a[0]) + "/" + i(a[1]);
    return ((i(c[0]) + "/" + i(c[1])) == f) ? e : f + " " + e;
}

function i(v) {
    return v.length > 1 ? v : "0" + v;
}

e("l");
a();
setInterval(g, 30000);

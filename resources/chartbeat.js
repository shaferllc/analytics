(function(){var g=void 0,h=true,i=null,k=false,n,o=this;
    function aa(a){var b=typeof a;if(b=="object")if(a){if(a instanceof Array)return"array";else if(a instanceof Object)return b;var c=Object.prototype.toString.call(a);if(c=="[object Window]")return"object";if(c=="[object Array]"||typeof a.length=="number"&&typeof a.splice!="undefined"&&typeof a.propertyIsEnumerable!="undefined"&&!a.propertyIsEnumerable("splice"))return"array";if(c=="[object Function]"||typeof a.call!="undefined"&&typeof a.propertyIsEnumerable!="undefined"&&!a.propertyIsEnumerable("call"))return"function"}else return"null";
    else if(b=="function"&&typeof a.call=="undefined")return"object";return b}function ba(a,b){function c(){}c.prototype=b.prototype;a.M=b.prototype;a.prototype=new c;a.prototype.constructor=a};function ca(){}
    function da(a,b,c){switch(typeof b){case "string":ea(b,c);break;case "number":c.push(isFinite(b)&&!isNaN(b)?b:"null");break;case "boolean":c.push(b);break;case "undefined":c.push("null");break;case "object":if(b==i){c.push("null");break}if(aa(b)=="array"){var d=b.length;c.push("[");for(var e="",f=0;f<d;f++)c.push(e),da(a,b[f],c),e=",";c.push("]");break}c.push("{");d="";for(e in b)Object.prototype.hasOwnProperty.call(b,e)&&(f=b[e],typeof f!="function"&&(c.push(d),ea(e,c),c.push(":"),da(a,f,c),d=","));
    c.push("}");break;case "function":break;default:throw Error("Unknown type: "+typeof b);}}var fa={'"':'\\"',"\\":"\\\\","/":"\\/","\u0008":"\\b","\u000c":"\\f","\n":"\\n","\r":"\\r","\t":"\\t","\u000b":"\\u000b"},ga=/\uffff/.test("\uffff")?/[\\\"\x00-\x1f\x7f-\uffff]/g:/[\\\"\x00-\x1f\x7f-\xff]/g;function ea(a,b){b.push('"',a.replace(ga,function(a){if(a in fa)return fa[a];var b=a.charCodeAt(0),e="\\u";b<16?e+="000":b<256?e+="00":b<4096&&(e+="0");return fa[a]=e+b.toString(16)}),'"')};var ha={scroll:5E3,keydown:5E3,mousemove:5E3,resize:5E3,mousedown:5E3,focus:5E3,pageload:5E3};function ia(){for(var a="",b=0;b<16;b++)a+=Math.random();return a}function ja(a,b){var c="",d=ka(encodeURIComponent(a));d.splice(b||5,d.length);p(d,function(a){if(a==0)a="A";else{a>>>=0;for(var b="",d;a>0;)d=a%64,b="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-".charAt(d)+b,a>>>=6;a=b}c+=a});return c}
    function ka(a){a+=String.fromCharCode(128);for(var b=[1518500249,1859775393,2400959708,3395469782],c=1732584193,d=4023233417,e=2562383102,f=271733878,j=3285377520,l=[],m,s,w,E,F,U=Math.ceil((a.length/4+2)/16),P=[U],y=0,t;y<U;y++){P[y]=[];for(m=0;m<16;m++)P[y][m]=a.charCodeAt(y*64+m*4)<<24|a.charCodeAt(y*64+m*4+1)<<16|a.charCodeAt(y*64+m*4+2)<<8|a.charCodeAt(y*64+m*4+3)}y=(a.length-1)*8;a=U-1;P[a][14]=Math.floor(y/Math.pow(2,32));P[a][15]=y&4294967295;for(y=0;y<U;y++){for(t=0;t<16;t++)l[t]=P[y][t];
    for(t=16;t<80;t++)l[t]=(l[t-3]^l[t-8]^l[t-14]^l[t-16])<<1|(l[t-3]^l[t-8]^l[t-14]^l[t-16])>>>31;a=c;m=d;s=e;w=f;E=j;for(t=0;t<80;t++)F=Math.floor(t/20),F=(a<<5|a>>>27)+(F==0?m&s^~m&w:F==1?m^s^w:F==2?m&s^m&w^s&w:m^s^w)+E+b[F]+l[t]&4294967295,E=w,w=s,s=m<<30|m>>>2,m=a,a=F;c=c+a&4294967295;d=d+m&4294967295;e=e+s&4294967295;f=f+w&4294967295;j=j+E&4294967295}return[c,d,e,f,j]}
    function la(a){var b=o.navigator,c=o.window.screen,d=[b.userAgent,b.platform,(new Date).getTimezoneOffset(),c.width+c.height+c.colorDepth];p(b.plugins,function(a){d.push(a.name+a.description+a.filename+a[0].type)});b=o.performance;d=d.concat([b&&b.now?b.now():"",document.title,o.location.href,q(),ia()]);return d.concat(a||[]).join()}function r(a,b,c){var d=Array.prototype.slice,e=d.call(arguments,2);return function(){return a.apply(b,e.concat(d.call(arguments)))}}
    function u(a){return typeof a!=="undefined"}function v(a){return a&&a.replace(/^www\./,"")}function ma(){var a=o.location.hostname;return a&&a.replace(/^www[0-9]*\./,"")}var na=/^((https?\:)?(\/\/))/i;function oa(a){return a&&a.replace(na,"")}var pa=/^((https?\:)?(\/\/))?[^\/]*/;function qa(a){return a.replace(pa,"")}var ra=/\#.*/;function sa(a){return a.replace(ra,"")}var ta=/\?[^\#]*/;function ua(a){return a.toLowerCase()}var va=/\/+((\?|\#).*)?$/;function wa(a){return a.replace(va,"$1")}
    function xa(a){return a&&a.replace(ta,"")}function ya(a){var b=a.match(/\?(.*)$/ig)?a.match(/\?(.*)$/ig)[0].slice(1).replace(/#(.*)?/ig,"").split("&"):[],c=a.match(/#(.*)$/ig)?a.match(/#(.*)$/ig)[0]:"",a=a.match(/[#|\?](.*)?/ig)?a.slice(0,a.search(/[#|\?](.*)?/ig)):a,b=za(b,function(a){return a.search("utm")!==0});b.length>0&&(a+="?"+b.join("&"));return a+c}function Aa(a,b){if(a===b)return 0;var c=i;p([ua,wa,oa,qa,sa,xa],function(d,e){a=d(a);b=d(b);if(a===b)return c=e+1,k});return c}
    function x(a,b,c,d){a.addEventListener?a.addEventListener(b,c,!!d):a.attachEvent&&a.attachEvent("on"+b,c)}function Ba(a,b){var c=o;c.removeEventListener?c.removeEventListener(a,b,k):c.detachEvent&&c.detachEvent("on"+a,b)}function Ca(a){return typeof a==="number"}function z(a){return typeof a==="string"}function Da(a){return Object.prototype.toString.call(a)==="[object Array]"}function Ea(a){a=new Date(+a);return Date.UTC(a.getFullYear(),a.getMonth(),a.getDate())}
    function q(){return(new Date).getTime()}function Fa(){return o.location.protocol==="http:"?"http:":"https:"}function A(a){return!!a&&a.constructor===Object}function Ga(a,b){if(!A(a)||!A(b))return a===b;var c=0,d=h;p(a,function(a,e){c++;return d=a===b[e]});if(!d)return k;var e=0;p(b,function(){e++});return c===e}function p(a,b){if(A(a)===h)for(var c in a){if(a.hasOwnProperty(c)&&b(a[c],c)===k)break}else{c=0;for(var d=a.length;c<d;c++)if(b(a[c],c)===k)break}}
    function Ha(a,b){var c=[];p(a,function(a,e){var f=b(a,e);c.push(f)});return c}function Ia(a,b){for(var c=0,d=a.length;c<d;c++)if(b(a[c]))return c;return-1}function Ja(a){for(var b=[51448,50924,33715,53046,66244,66059,16698],c=k,d=0,e=b.length;d<e;d++)if(Ga(b[d],a)){c=h;break}return c}function za(a,b){var c=[];p(a,function(a){b(a)&&c.push(a)});return c}function Ka(a){if(a){var b=[];da(new ca,a,b);a=encodeURIComponent(b.join(""))}else a="";return a}
    function La(a,b){if(a===b)return 0;if(a.length===0)return b.length;if(b.length===0)return a.length;for(var c=[],d=0,e=b.length;d<=e;d++)c[d]=[d];for(var f=0,j=a.length;f<=j;f++)c[0][f]=f;for(var l,m,s,d=1;d<=e;d++)for(f=1;f<=j;f++)l=d-1,m=f-1,s=c[l][m],b.charAt(l)==a.charAt(m)?c[d][f]=s:(m=c[d][m]+1,l=c[l][f]+1,s+=2,c[d][f]=Math.min(m,l,s));return c[b.length][a.length]}function Ma(){if(Na!==g)return Na;var a=o.navigator.userAgent;return Na=a.indexOf("AppleWebKit")>0&&a.indexOf("FBIOS")>0}var Na;
    function Oa(){}var Pa=o.setInterval,Qa=o.clearInterval,Ra=o.setTimeout,Sa=o.clearTimeout;function Ta(a){switch(a){case g:return"undefined";case "":return"empty";default:return a}}function Ua(){for(var a=document.domain,b=a.split("."),c=window.location.protocol==="https:"?"; Secure":"",d=0;d<b.length-1&&document.cookie.indexOf("_cbt=_cbt")==-1;)a=b.slice(-1-++d).join("."),document.cookie="_cbt=_cbt; domain="+a+c;document.cookie="_cbt=; expires=Thu, 01 Jan 1970 00:00:01 GMT; domain="+a+c;return a}
    var Va=h;function Wa(a,b,c){if(c)return a;return b&&b[Xa]&&Va&&(c=Ua(),b[Xa]!==c)?a+"_"+ma().split(".")[0]:a}function Ya(a){if(!Ja(a))return"";a="";try{var b=o.googletag.pubads();if(b){var c=b.getSlots(),d={};p(c,function(a){a=a.getAdUnitPath();d[a]?d[a]+=1:d[a]=1});a=Ha(Object.keys(d),function(a){return a+"||"+d[a]}).join(",")}}catch(e){}return encodeURIComponent(a)};function Za(a){var b={};a&&(a.charAt(0)=="?"&&(a=a.substring(1)),a=a.replace(/\+/g," "),p(a.split(/[&;]/g),function(a){a=a.split("=");b[decodeURIComponent(a[0])]=decodeURIComponent(a[1])}));return b}function $a(a,b){var c="",d=o.location.href.match(/[^?]+[?]([^#]+).*/);if(d){var d=Za(d[1]),e=b||a;d[e]&&(c=d[e])}return encodeURIComponent(c)}function ab(a,b){return!b?h:a==="http:"&&b==="80"||a==="https:"&&b==="443"}
    function bb(a){var b=[];p(cb,function(c){var d=a[c];u(d)&&(Da(d)?p(d,function(a){b.push(c+"="+a)}):A(d)?p(d,function(a,c){b.push(c+"="+a)}):((d+"").length||c=="r")&&b.push(c+"="+d))});b.push("_");return b.join("&")}
    function db(a){var b={hostname:"",pathname:"",search:"",protocol:"",port:"",hash:""};if(!a)return b;var c=document.createElement("a"),d=o.location;if(!/^https?:/.test(a)&&a.indexOf("javascript:")!==0&&a.indexOf("app:")<0)if(a.indexOf("//")===0)a=d.protocol+a;else if(a.indexOf("/")===0)var e=ab(d.protocol,d.port)?"":d.port,a=d.protocol+"//"+d.hostname+(e?":"+e:"")+a;else{var e=document.baseURI||d.href,f=e.indexOf("?");f===-1&&(f=e.indexOf("#"));if(f===-1)f=e.length;f=e.lastIndexOf("/",f);a=f===-1?
    "/"+a:e.substr(0,f)+"/"+a}c.href=a;b.hostname=c.hostname;b.pathname=c.pathname;b.search=c.search;b.protocol=c.protocol;b.port=c.port;b.hash=c.hash;if(b.pathname.charAt(0)!=="/")b.pathname="/"+b.pathname;if(b.hostname==="")b.hostname=d.hostname;if(b.protocol==="")b.protocol=d.protocol;if(b.protocol==="javascript:")b.pathname="",b.hostname="",b.port="",b.hash="";if(ab(b.protocol,b.port)||b.port==="0")b.port="";return b}
    function eb(a){var b=a.protocol;a.hostname&&(b+="//"+a.hostname,a.port&&(b+=":"+a.port));return b+a.pathname+a.search+a.hash};function fb(a,b,c,d){b=b||"*";c=c||document;if("querySelectorAll"in c)return c.querySelectorAll(b+"["+(a+(d?'="'+d+'"':""))+"]");for(var e=[],b=c.getElementsByTagName(b),c=b.length,f="";c--;)(f=b[c].getAttribute(a))&&(!d||f===d)&&e.push(b[c]);return e}function gb(a,b,c){c=c||"";if(a===g)a=k;else{var d;if(!(d=c===""&&a.getAttribute(b)))if(d=a.getAttribute(b))d=a.getAttribute(b)===c;a=d?a:a===document.body?k:gb(a.parentNode,b,c)}return a}
    function hb(a,b,c){a="page"+a+"Offset";b="scroll"+b;if(c&&(c=fb("data-cb-scroll-element"))&&c.length)return c[0][b];if(Ca(o[a]))return o[a];else if(document.body&&document.body[b])return document.body[b];else if(document.documentElement[b])return document.documentElement[b];return 0}function ib(a){var b=document,b=b[b.compatMode==="CSS1Compat"?"documentElement":"body"]["client"+a]||0;window["inner"+a]&&(b=Math.min(window["inner"+a],b));return b}
    function jb(a){a="scroll"+a;return Math.max(document.body[a],document.documentElement[a])||0}function kb(a,b,c){a.ownerDocument||(a=a.correspondingUseElement);if(!a||!a.ownerDocument)return i;var d=a.ownerDocument.documentElement,e=0,f=u(c)?c+1:-1;z(b)?(b=b.toLowerCase(),c=function(a){return(a=a.nodeName)&&a.toLowerCase()===b}):c=b;for(;a&&a!==d&&e!==f;){if(c(a))return a;a=a.parentNode;e++}return i}
    function lb(a){return a.nodeName&&a.nodeName.toLowerCase()==="a"&&(!a.namespaceURI||a.namespaceURI==="http://www.w3.org/1999/xhtml")}function mb(a){a=a||window.event;return!a?i:kb(a.target||a.srcElement,lb,10)}function nb(a,b){var c=document.createElement(a);p(b,function(a,b){c.setAttribute(b,a)});return c}function ob(){return o.document.readyState==="complete"||o.document.readyState!=="loading"&&!o.document.documentElement.doScroll}
    function pb(a){function b(){if(o.document.addEventListener||o.event.type==="load"||o.document.readyState==="complete")o.document.addEventListener?(o.document.removeEventListener("DOMContentLoaded",b,k),o.removeEventListener("load",b,k)):(o.document.detachEvent("onreadystatechange",b),o.detachEvent("onload",b)),a()}ob()?a():o.document.addEventListener?(o.document.addEventListener("DOMContentLoaded",b,k),o.addEventListener("load",b,k)):(o.document.attachEvent("onreadystatechange",b),o.attachEvent("onload",
    b))};function qb(){this.G={};this.$a=1}function B(a,b,c,d){a.$a++;a.G[b]=a.G[b]||{};a.G[b][a.$a]=[c,d];return a.$a}function rb(a,b){if(z(b))a.G[b]=g,delete a.G[b];else if(Ca(b)){var c=h;p(a.G,function(a){p(a,function(e,f){if(parseInt(f,10)===b)return a[f]=g,delete a[f],c=k});return c})}}qb.prototype.Q=function(a,b){if(this.G[a]){var c=arguments.length>1?Array.prototype.slice.call(arguments,1):[];p(this.G[a],function(a){var b;a&&a.length===2&&(b=a[0],a=a[1],b.apply(a,c))})}};
    qb.prototype.addEventListener=function(a,b){var c=B(this,a,b);b._cbEventId=c};qb.prototype.removeEventListener=function(a,b){this.G[a]&&this.G[a][b._cbEventId]&&this.G[a][b._cbEventId][0]===b&&rb(this,b._cbEventId)};var C=new qb,sb="a";var D={};
    D.D=function(){D.La?D.wa("pageload"):(D.Db=[{target:o,event:"scroll"},{target:document.body,event:"keydown"},{target:document.body,event:"mousemove"},{target:o,event:"resize"},{target:document.body,event:"mousedown"}],D.Da=i,D.aa=i,D.Ka={},D.ob={},p(D.Db,function(a){var b=a.event;x(a.target,b,function(a){D.wa.call(D,b,a)})}),B(C,"f",function(){D.wa("focus")}),D.wa("pageload"),x(document.body,"click",function(a){(a=mb(a))&&C.Q("c",a)},h),x(document.body,"contextmenu",function(a){(a=mb(a))&&C.Q("r",
    a)}),D.La=h)};D.Ib=function(){var a,b=D.ob.keydown;if(b===g)return k;b=q()-b;return b<=(a||15E3)&&b>=0};D.cb=100;D.wa=function(a,b){if(!b)b=window.event;if(b&&a==="keydown"){var c=b.keyCode?b.keyCode:b.which;if(c===32||c>36&&c<41)a="scroll"}D.tc(a);if(D.Da===i)D.wb(a);else if(!D.aa||ha[D.aa]<ha[a])D.aa=a};D.tc=function(a){D.ob[a]=q()};D.wb=function(a){D.Da=Ra(D.Nb,D.cb);C.Q(sb);D.Ka[a]!==i&&Sa(D.Ka[a]);D.jc(a)};
    D.jc=function(a){var b=D.Ka;b[a]=Ra(function(){Sa(b[a]);delete b[a];var c=k;p(b,function(){c=h;return k});c||C.Q("i")},ha[a]+D.cb)};D.Nb=function(){Sa(D.Da);D.Da=i;if(D.aa)D.wb(D.aa),D.aa=i};var tb,ub,vb,wb,xb;(function(){var a,b;p(["","moz","o","ms","webkit"],function(c){a=(c+"Hidden").charAt(0).toLowerCase()+(c+"Hidden").slice(1);if(typeof o.document[a]==="boolean")return b=c,k});b!==g&&(wb=a,xb=(b+"VisibilityState").charAt(0).toLowerCase()+(b+"VisibilityState").slice(1),vb=b+"visibilitychange")})();var yb=k;function zb(){yb=xb&&o.document[xb]==="prerender"?h:k}function Ab(){ub=h;C.Q("f")}function Bb(){ub=k;C.Q("b")}
    function Cb(a,b,c){o.addEventListener?o.addEventListener(a,c,k):o.document.attachEvent&&o.document.attachEvent(b,c)}function Db(){var a=h;!Ma()&&o.document.hasFocus&&(a=o.document.hasFocus());var b=k;wb&&(b=o.document[wb]);return a&&!b}function Eb(){Db()?Ab():Bb()}function Fb(a){zb();if(yb){var b=k,c=function(){b||(zb(),yb||(b=h,a(),o.window.setTimeout(function(){o.document.removeEventListener(vb,c,k)},100)))};o.document.addEventListener(vb,c,k)}else a()};function G(){this.a=o._sf_async_config||{};this.jb=r(this.rb,this)}G.prototype.D=function(){this.ka=0};G.prototype.rb=function(){};G.prototype.Aa=function(a){if(!yb){var b=this.jb,c;c=new Image(1,1);if(b)c.onerror=b;c.src=a}};G.prototype.pa=function(){this.jb=i};var H="path",I="domain",Gb="useCanonical",Hb="useCanonicalDomain",J="title",Ib="virtualReferrer",Xa="cookieDomain";function K(a,b,c){a[b]=a[b]||c}function Jb(a,b){for(var c=o[a]||[];c.length;)b(c.shift());o[a]={push:b}}function Kb(a){p(document.getElementsByTagName("script"),function(b){if(typeof b.src==="string"&&b.src.match(/chartbeat.js/))return b=Za(b.src.split("?")[1]),K(a,"uid",b.uid),K(a,I,b.domain),k})}function Lb(a,b){return a[b]?encodeURIComponent(a[b]):""}
    function Mb(a){var b={};p(a,function(a,d){d.charAt(0)=="_"&&(b[d]=a)});return b}function Nb(a){if(Ob(a))return"";var a=a[Xa],b=Ua();a&&a!==ma()&&a!==b&&(Va=k,o.console.warn("Invalid cookieDomain (must be set to current domain or root domain), defaulting to root domain."));return a&&Va?a:b}function Ob(a){return a&&a.noCookies?h:k};var L={};L.Lb=function(a,b){try{L.create("_cb_test","1",1,a,b);var c=L.q("_cb_test");L.remove("_cb_test",a,b);return c==="1"}catch(d){return k}};L.q=function(a,b){var c=o._sf_async_config;if(Ob(c))return"";var a=Wa(a,c,b)+"=",d="";p(document.cookie.split(";"),function(b){for(;b.charAt(0)===" ";)b=b.substring(1,b.length);if(b.indexOf(a)===0)return d=b.substring(a.length,b.length),k});return d};
    L.create=function(a,b,c,d,e,f){var j=o._sf_async_config;if(Ob(j))return"";a=Wa(a,j,f);f=new Date;f.setTime(q()+c*1E3);a=a+"="+b+("; expires="+f.toUTCString())+("; path="+d)+(window.location.protocol==="https:"?"; Secure":"")+(e?"; domain="+e:"");return document.cookie=a};L.remove=function(a,b,c,d){return L.q(a,d)?L.create(a,"",-86400,b,c,d):""};var M={};M.B=function(a){var b=o._sf_async_config;if(!a&&b&&b.noCookies)return i;if(M.B.Ha!==g)return M.B.Ha;var a=q()+"",c,d;try{if((d=o.localStorage).setItem("_cb_ls_test",a),c=d.getItem("_cb_ls_test")===a,d.removeItem("_cb_ls_test"),c)return M.B.Ha=d}catch(e){}return M.B.Ha=i};M.q=function(a){var b=M.B();if(!b)return"";var c=b.getItem(a+"_expires");return c&&(c=+c,!isNaN(c)&&q()>c)?(M.remove(a),""):b.getItem(a)||""};
    M.create=function(a,b,c){var d=M.B();if(d){var e=new Date;e.setTime(q()+c*1E3);try{d.setItem(a,b),d.setItem(a+"_expires",e.getTime())}catch(f){}}};M.remove=function(a){var b=M.B();b&&(b.removeItem(a),b.removeItem(a+"_expires"))};function Pb(a,b,c,d){this.za=a||"";this.S=b||"/";this.gb=d||{};this.da=(this.Oa=Ob(this.gb))?"":c||Ua();this.Ob=M.B()!==i||L.Lb(this.S,this.da);this.zb=k}n=Pb.prototype;n.isSupported=function(){return this.Ob};n.create=function(a,b,c,d){this.Oa||(a=d?a:this.za+a,(M.B()?M:L).create(a,b,c,this.S,this.da),M.B()&&L.create(a,b,c,this.S,this.da))};
    n.update=function(a,b,c,d,e,f,j){a=d?a:this.za+a;e=z(e)?e:"::";d=(d=this.q(a,h))?d.split(e):[];if(j&&d.length){var l=j(b),m=Ia(d,function(a){return j(a)===l});m!==-1&&d.splice(m,1)}d.push(b);for(Ca(f)&&d.length>f&&d.splice(0,d.length-f);d.length>1&&d.join(e).length>4E3;)d.shift();this.create(a,d.join(e),c,h)};
    n.q=function(a,b){if(this.Oa)return"";var a=b?a:this.za+a,c=(M.B()?M:L).q(a);!c&&M.B()&&(c=L.q(a));if(c){this.remove(a,b,"",h);var d=L.q(a),e=this.gb[Xa]&&Va,f=ma(),e=e&&f!==Ua();if(c===d&&!e)return d;(f=a!=="_chartbeat2"||!d?k:+d.split(".")[2]<1647357868E3?h:k)&&L.remove(a,this.S,this.da);if(e){if(d)return f?c:d;d=L.q(a,h);return c!==d?c:""}if(d)return f?c:d}return c};n.remove=function(a,b,c,d){if(c!=="")c=this.da;a=b?a:this.za+a;(M.B()?M:L).remove(a,this.S,c);M.B()&&L.remove(a,this.S,c,d)};var Qb={cb_rec:h,fbclid:h,ia_share_url:h,gclid:h,dclid:h,gbraid:h,wbraid:h,gclsrc:h,gdfp_req:h,_gl:h,_ga:h,_hsenc:h,_hsmi:h,msclkid:h,lcid:h,sfmc_id:h,sfmc_sub:h,tblci:h,twclid:h,guccounter:h,guce_referrer:h,"pure360.trackingid":h,dicbo:h,addata:h,zip:h,zipcode:h,regi_id:h,segment_id:h,user_id:h,campaign_id:h,sessionid:h,uuid:h,email:h,token:h,req_token:h,paymentredirectuuid:h,authid:h,auth_id:h,auth:h};var Rb=/[A-Za-z0-9-_.'+]+(@|%40)\w+([-.]\w+)*\.\w+([-.]\w+)*/g;function Sb(a){var b=i;if(a&&(b=Tb(),b=!b?b:N(b.pathname)+Ub(b.search)+b.hash))return b;var c=o.location,b=N(c.pathname),a=c.search||"",d=/[?&#]/;if(!a||a.length===1&&d.test(a))return b;a=a.replace(/PHPSESSID=[^&]+/,"");d=/&utm_[^=]+=[^&]+/ig;(c=d.exec(c.search))&&(a=a.replace(d,""));d=/\?utm_[^=]+=[^&]+(.*)/i;(c=d.exec(a))&&(a=a.replace(d,c[1]!=""?"?"+c[1]:""));a=Ub(a);a=N(a);return b+a}
    function Vb(a){return a&&(a=Tb(),a=!a?a:a.hostname)?a:o.location.hostname}function Tb(){var a=Wb();return!a?a:db(a.href)}function Wb(){var a=i;p(document.getElementsByTagName("link"),function(b){if(b.rel=="canonical")return a=b,k});return a}for(var Xb={},Yb=0;Yb<81;Yb++)Xb["0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-._~!$&'()*+,;=:@[]".charCodeAt(Yb)]=h;function Zb(a,b){if(a==="%")return"%25";var c=parseInt(b,16);return Xb[c]?String.fromCharCode(c):"%"+b.toUpperCase()}
    function N(a){if(!z(a))return a;a=a.replace(/%([0-9A-Fa-f]{2})?/g,Zb);a=a.replace(/[^0-9A-Za-z\-._~!$&'()*+,;=:@\/\[\]?#%]+/g,encodeURIComponent);return a=a.replace(Rb,"")}function Ub(a){var b=a;/[?&#]/.test(a[0])&&(b=a.substring(1));a=b.split("&").filter(function(a){a=a.split("=");return a.length<=1?k:Qb[a[0].toLowerCase()]?k:Rb.exec(a[1])?k:h});return a.length<1?"":"?"+a.join("&")}function $b(a){if(a){var b=a.split("?");return b.length>1?(a=Ub(b[1]))&&a.length?N(b[0]+a):N(b[0]):N(a)}return i};function O(a,b){var c=Q();return u(b)&&!u(c[a])?b:c[a]}function Q(){u(o._cb_shared)||(o._cb_shared={});return o._cb_shared};var R={Fb:{IDLE:0,Ec:1,xc:2,Eb:3},J:0};R.D=function(){if(!R.La)B(C,sb,R.Ub,R),B(C,"i",R.Xb,R),B(C,"f",R.Wb,R),B(C,"b",R.Vb,R),R.La=h};R.Sb=function(){return R.J};R.Ub=function(){R.J===1?R.P(3):R.J===0&&R.P(2)};R.Xb=function(){R.J===3?R.P(1):R.J===2&&R.P(0)};R.Wb=function(){(R.J===0||R.J===2)&&R.P(3)};R.Vb=function(){R.J===3?R.P(2):R.J===1&&R.P(0)};R.P=function(a){R.J=a;C.Q("s",a)};function ac(a,b){this.Ja=a||g;this.Qa=b||g;this.ga=this.ca=0;this.$b=r(this.ac,this);this.yb=this.ba=i}n=ac.prototype;n.D=function(){this.ga=this.ca=0;this.ba=i;this.yb=B(C,"s",this.mb,this);this.mb(R.Sb())};n.mb=function(a){Qa(this.ba);this.ba=i;if(a===R.Fb.Eb)this.ba=Pa(this.$b,1E3)};n.ac=function(){if(this.Ja===g||this.Ja())this.ca++,this.ga++,this.Qa&&this.Qa()};n.terminate=function(){Qa(this.ba);this.ba=i;rb(C,this.yb)};n.pa=function(){this.terminate();this.Qa=this.Ja=g};function S(){G.call(this);this.T=[];this.k=new Pb(this.pc,this.a.cookiePath||"/",Nb(this.a),this.a);this.k.remove("_SUPERFLY_nosample");this.F=new ac;this.Sa=r(this.ya,this);x(o,"pagehide",this.Sa);this.ua=k;bc(r(this.D,this))}ba(S,G);function bc(a){pb(function(){Fb(a)})}n=S.prototype;n.Xa=h;n.Ua=k;
    n.D=function(){S.M.D.call(this);this.sa=this.X=0;this.la=q();this.Wa=ja(cc(this));this.Va=h;this.Ma=72E5;if(this.Xa){var a=+this.a.sessionLength;if(!isNaN(a))this.Ma=a*6E4}a=O("d");if(!a){var a=[],b=this.k.q("_chartbeat2",h);b.length>0&&(a=b.split("."));a.length>6&&(a=[]);var b=q(),c=this.k.q("_cb",h);c.length>0?a[1]=a[1]||b:c=a[0];a[0]="";var d=b-+(a[1]||0),e=b-+(a[2]||0);Q().n=c&&d>18E5&&e<2592E6?0:1;var d=a[4],f=parseInt(a[5],10)||0;if(!(e=e>18E5))if(dc(this))e=k;else{var e=T(this),e=decodeURIComponent(e),
    e=ec(e)||e,j;j=fc(this);j=decodeURIComponent(j);j=ec(j)||j;e=e!==j}!d||e?(d=ja(cc(this)),a[4]=d,f=1,this.k.remove("_cb_svref",h)):f+=1;a[5]=f;f="1";e=a&&+a[2];d=a&&a[3];if(a&&e&&d)if(f=Math.abs((Ea(b)-Ea(e))/864E5)){f=Math.min(f,16)-1;for(e="";f--;)e+=0;f=(d+e+"1").slice(-16)}else f=d;d=f;c||(c=this.a.utoken||ja(cc(this),3),a[1]=b);a[2]=b;a[3]=d;this.a.utoken=this.na;this.k.create("_cb",c,34128E3,h);this.k.create("_chartbeat2",a.join("."),34128E3,h);a[0]=c;Q().d=a}this.Hc=a.join(".");var l;c=+a[1];
    d=+a[2];if((b=a[3])&&c&&d)l=(Math.min((Math.abs((Ea(d)-Ea(c))/864E5)||0)+1,16,b.length)-1).toString(16),l+=("0000"+parseInt(b,2).toString(16)).slice(-4);this.lb=l;this.dc=O("n",1);this.na=a[0];this.oc=a[4];this.lc=a[5];this.mc=fc(this);this.k.create("_cb_svref",fc(this),1800,h);this.F.D();R.D();D.D();tb||(ub=Db(),vb&&o.document.addEventListener&&o.document.addEventListener(vb,Eb,k),Cb("focus","onfocusin",Ab),Cb("blur","onfocusout",Bb),ub&&Ab(),tb=h);this.ha=0;this.Hb=B(C,sb,this.fc,this);this.Ua=
    h;if(this.Ra)Ra(this.Ra,0),this.Ra=i};n.Ea=function(){if(!this.k.q("_SUPERFLY_lockout"))this.Ua?!ob()&&!this.ua?(this.xa=r(this.Za,this),x(o,"load",this.xa)):this.Za():this.Ra=r(this.Ea,this)};n.Za=function(){this.Ta=gc();D.D();var a=r(this.ia,this);this.nb=Pa(a,15E3);this.ia()};
    n.ia=function(){var a=this.F.ga,a=this.a.reading&&+this.a.reading||a>0;this.sa<this.X&&!a?this.sa++:Ma()&&!a?this.sa++:(a?this.X=0:hc(this),this.sa=0,this.T.push(0),this.T.length>18&&this.T.shift(),this.Xa&&q()-this.la>=this.Ma?this.terminate():this.Z())};n.rb=function(){this.T.push(1);var a=0;p(this.T,function(b){a+=b});a<3?(this.Va=h,hc(this)):(this.terminate(),this.k.create("_SUPERFLY_lockout","1",600))};n.ya=function(){};n.fc=function(){var a=ic(this);this.ha=Math.max(this.ha,a)};
    function ic(a){return Math.floor(hb("Y","Top",!!a.a.scrollElement))}function hc(a){var b=a.X,b=b?Math.min(b*2,16):1;a.X=b}n.qa=function(){this.ya();this.terminate()};n.Ba=function(){this.ua=h;this.Ua=k;this.D();this.Ea()};function dc(a){if(a.a[Ib])return h;if(jc(a))return k;a=document.referrer.indexOf("://"+o.location.hostname+"/");return a!=-1&&a<9}
    function jc(a){var b=a.a.referrerOverrideQueryParam,c="";p(window.location.search.substr(1).split("&"),function(a){a=a.split("=");if(a.length==2&&a[0]==b&&a[1])return c=decodeURIComponent(a[1]).replace(/\+/g," "),k});c&&c.indexOf("::")==-1&&(c="external::"+c);return c}function T(a){a=a.a[Ib]||jc(a);if(!a&&(a=document.referrer||"")&&!/^(android-)?app:/.test(a)){var b=db(a);if(b.protocol==="http:"||b.protocol==="https:")b.pathname=N(b.pathname),a=eb(b)}return encodeURIComponent(a)}
    function kc(a){a=a.a[J].slice(0,200);return encodeURIComponent(a)}function cc(a){a=[T(a),jb("Width"),jb("Height")];return la(a)}function lc(a){var b=[],c=a.k.q("_chartbeat4");c&&(p(c.split("::"),function(a){b.push(encodeURIComponent(a))}),a.k.remove("_chartbeat4"));return b}function fc(a){var b=a.k.q("_cb_svref",h)||T(a)||"null";b==="null"&&(b=dc(a)?"internal":"external",a.k.create("_cb_svref",b,1800,h));return b}function ec(a){a=a.match(/^https?:\/\/([^\/]*)/);return!a?i:a[1]}
    function gc(){var a=o.performance&&o.performance.timing;if(!a||a.navigationStart==0)return-1;var b=a.navigationStart,a=a.loadEventStart;return Ca(b)&&b?Ca(a)&&a>b?a-b:q()-b:-1}n.terminate=function(){this.F.terminate();rb(C,this.Hb);this.xa!==g&&Ba("load",this.xa);Qa(this.nb);Q().d=g;Q().n=g};n.pa=function(){this.terminate();Ba("pagehide",this.Sa);this.Sa=this.xa=this.k=this.T=this.a=i;this.F.pa();this.F=i;S.M.pa.call(this)};function mc(a){for(var b=O("m")||[];b.length;)a(b.shift());a={push:a};Q().m=a};function nc(a,b,c){var d=a.offsetLeft,e=a.offsetTop,f=oc(a);d+=f.x;e+=f.y;for(var j=k,l=c?0:Math.floor(hb("X","Left",g)),m=c?0:Math.floor(hb("Y","Top",g)),f=a.offsetParent;a&&a!==b&&a!==document.body;){if(a===f)f=oc(a),d+=a.offsetLeft+f.x,e+=a.offsetTop+f.y,f=a.offsetParent;c||(d-=a.scrollLeft,e-=a.scrollTop);if(pc(a,["position"]).position==="fixed"){j=h;break}a=a.parentElement}c||(d-=b?b.scrollLeft:l,e-=b?b.scrollTop:m);j&&(d+=l,e+=m);return{x:d,y:e}}
    function pc(a,b){var c={},d,e;o.getComputedStyle?d=o.getComputedStyle(a,i):a.currentStyle?e="currentStyle":a.style&&(e="style");p(b,function(b){c[b]=d?d[b]||d.getPropertyValue(b):a[e][b]});return c}var qc=/matrix(3d)?\((.*)\)/;function oc(a){var b={x:0,y:0},a=pc(a,["transform"]).transform;if(!z(a))return b;var c=a.match(qc);if(!c)return b;var a=c[2].split(", "),d;u(c[1])?(c=12,d=13):(c=4,d=5);b.x=parseInt(a[c],10);b.y=parseInt(a[d],10);return b};function rc(){var a=o.location.href,a=a.replace(/-{2,}/g,"-"),a=db(a);a.pathname=N(a.pathname);return a}
    function sc(a){var b=L.q("_chartbeat6");if(!b)return i;var b=b.split("::"),c,d;if(b.length===1)c=b[0].split(","),d=0;else{var e,f=rc(),j=eb(f);p(b,function(a,b){var f=a.split(","),m=La(j,decodeURIComponent(f[0]));if(m===0)return c=f,d=b,k;if(e===g||m<e)e=m,c=f,d=b})}b.splice(d,1);L.create("_chartbeat6",b.join("::"),60,a.path?a.path:"/",a.domain?a.domain:"");var a=[],b=decodeURIComponent(c[0]),f=decodeURIComponent(c[1]),l=c[2];c.splice(0,3);for(var m=c.length/3,s=0;s<m;s++){var w=s*3+2;a.push({Fc:b,
    origin:f,N:l,$:w<c.length?c[w]:"",Pb:c[s*3],uc:c[s*3+1]})}return a}function tc(a){var b=a.topStorageDomain,a=a[I],c=o.location.hostname;return b?b:uc(c,a)?a:c.replace(/^www\./,"")}function uc(a,b){if(a===b)return h;for(var c=b.split("."),d=a.split(".");c.length;){if(d.length===0)return k;if(c.pop()!==d.pop())return k}return h}function vc(a,b,c,d,e){this.Rb=a;this.Qb=b;this.vc=c;this.N=d;this.$=e}
    function wc(a){var b=a.d,c=i;if(b!==i){var d={};b.s&&p(b.s,function(a,b){d[b]=typeof a==="string"?{fb:a,$:""}:{fb:a.chosenVariant,$:a.specificLocation}});c={vb:d,N:b.g,pb:b.m}}return{status:a.s,data:c,code:a.c,message:a.m}};function xc(a,b){for(var c=b||document.documentElement,d=[],e=i,f=a,j,l,m,s,w,E;f&&f!==c;){j=f.nodeName.toLowerCase();e=f;l=e.nodeName;if((f=f.parentNode)&&f!==document.documentElement){m=f.children;s=0;for(w=0,E=m.length;w<E;w++){if(e===m[w]){j+="["+(1+w-s)+"]";break}m[w].nodeName!==l&&s++}}d.unshift(j)}return d.join("/")};function V(){this.pc="_t_";this.ib=this.xb=Oa;S.call(this)}ba(V,S);n=V.prototype;
    n.D=function(){V.M.D.call(this);Kb(this.a);var a=!!this.a[Gb],b=Vb(!!this.a[Hb]&&a);K(this.a,"mabServer","mabping.chartbeat.net");K(this.a,J,document.title);K(this.a,I,b);this.a[H]=this.a[H]?$b(this.a[H]):Sb(a);this.fa=v(b);this.a[I]=v(this.a[I]);this.sc=tc(this.a);this.ea=this.ta=k;this.kb=[];var c=this,a=sc({domain:"."+this.sc,path:this.a.cookiePath||"/"});if(a!==i)this.ta=h,p(a,function(a){c.kb.push(new vc(a.origin,a.Pb,a.uc,a.N,a.$))});this.qb=0;this.Ca=i;mc(r(this.Zb,this))};
    n.Z=function(){var a=this.F.ca,b=yc(this),c,d=this;this.ta&&p(this.kb,function(e){c=b+"&x="+e.Qb+"&v="+e.vc+"&ml="+e.N+"&xo="+e.Rb+"&e="+a+"&sl="+e.$;d.Aa(c)});!this.ea&&this.Ca&&zc(this,this.Ca);this.ea=h};n.Za=function(){this.Ta=gc();D.D();if(this.ta){var a=r(this.ia,this);this.nb=Pa(a,500)}this.ia()};n.ia=function(){var a,b;this.ea?(a=this.F.ca,b=this.qb*15,a-b>=15&&(this.Z(),this.qb+=1),a>=45&&this.terminate()):this.Z()};
    function yc(a){var b=a.a;return Fa()+"//"+b.mabServer+"/ping/mab?h="+encodeURIComponent(b[I])+"&p="+encodeURIComponent(b[H])+"&d="+encodeURIComponent(a.fa)+"&u="+a.na+"&c="+Math.round((q()-a.la)/600)/100+"&V=147"}n.ya=function(){};n.qc=function(a){a=wc(a);this.ea?zc(this,a):this.Ca=a};
    function zc(a,b){var c=yc(a),d=b.status,e=b.data;if(d=="s"&&e!==i){var f=r(a.Aa,a);A(e.vb)&&p(e.vb,function(a,b){f(c+"&x="+b+"&v="+a.fb+"&ml="+e.N+"&sl="+a.$+"&e=-1")});Da(e.pb)?p(e.pb,function(a){f(c+"&me=3&ml="+e.N+"&x="+a)}):Da(e.cc)&&p(e.cc,function(a){f(c+"&me=5&ml="+e.N+"&x="+a)})}else d=="e"&&a.Aa(c+"&me="+b.code)}
    n.Zb=function(a,b){for(var c=0,d=arguments.length;c<d;c++){var a=arguments[c],e=a.shift();e==="t"?this.qc.apply(this,a):e==="v"?this.ub.apply(this,a):e==="sv"?this.kc.apply(this,a):e==="ev"&&this.ub.apply(this,a)}};n.qa=function(){this.ib();V.M.qa.call(this)};n.Ba=function(){Q().m=[];this.xb();V.M.Ba.call(this)};n.kc=function(a){this.xb=a};n.ub=function(a){this.ib=a};n.terminate=function(){this.Gc=g;this.Ca=i;this.ea=this.ta=g;V.M.terminate.call(this)};Nb(o._sf_async_config||{});if(!L.q("cb_optout")&&!o.pSUPERFLY_mab){var Ac=new V,Bc={};Bc.evps=r(Ac.qa,Ac);Bc.svps=r(Ac.Ba,Ac);o.pSUPERFLY_mab=Bc;Ac.Ea()};var Cc="engagedSeconds",Dc={Cc:"id",Bc:Cc,yc:"campaignId",zc:"creativeId",Dc:"placementId",Ac:"element"};var Ec,Fc,W;
    function Gc(a){if(a.origin==="https://chartbeat.com"&&(a=String(a.data),a.indexOf("_cb_hud_version=")===0)){var b=a.substr(16);Hc();if(b!=="NONE")b=b.indexOf("HUD2.")===0?b.substr(5):"OLD",a="https://static.chartbeat.com/js/inpage.js",b!=="OLD"&&(a="https://static2.chartbeat.com/frontend_ng/hud/hud-inpage/hud-inpage-"+b+".js"),b=u(g)?g:{},b.src=a,a=nb("script",b),a.setAttribute("type","text/javascript"),b=document.head||document.getElementsByTagName("head")[0],u(g)?g.appendChild(a):b&&b.appendChild(a)}}
    function Hc(){Sa(Fc);Fc=g;Ba("message",Gc);W&&W.parentNode&&W.parentNode.removeChild(W);W=g};var Ic=/^https?:\/\/([^/]*\.)?chartbeat\.com\/publishing\/hud2\/launch\//;function Jc(){var a=o._sf_async_config&&o._sf_async_config.domain;if(a&&!Ec){Ec=h;a="https://chartbeat.com/publishing/hud2/versioninfo/?host="+encodeURIComponent(a);x(o,"message",Gc);Fc=Ra(Hc,1E4);var b=u(g)?g:{};b.src=a;a=nb("iframe",b);a.style.display="none";u(g)?g.appendChild(a):document.body&&document.body.appendChild(a);W=a}}
    function Kc(){var a=M.B(h);return a?(a.setItem("_cb_ip","1"),a.removeItem("_cb_hud_collapsed"),h):k}function Lc(a){/[\/.]chartbeat\.com$/.test(a.origin)&&String(a.data).indexOf("_cb_ip")==0&&Kc()&&(a.source.postMessage(1,a.origin),pb(Jc),Ba("message",Lc))};function Mc(a,b){this.Cb=b;this.Bb=a[Gb];this.Ab=a[Hb];this.O=a[H];this.rc=a[J];this.hb=a[I];this.wc=a[Ib];this.eb=(this.H=Tb())?this.H.hostname:"";this.oa=this.H?N(this.H.pathname)+this.H.search+this.H.hash:"";this.tb=Sb(k);this.ic=Vb(k);var c=fb("property","meta",i,"og:url");this.ja=(this.Y=c&&c.length?db(c[0].content):i)?N(this.Y.pathname)+this.Y.search+this.Y.hash:"";this.ec=this.Y?this.Y.hostname:"";this.Ia=document.title||"";var d;if((c=fb("property","meta",i,"og:title"))&&c.length)d=c[0].content;
    this.va=d;var e;if((d=fb("property","meta",i,"twitter:title"))&&d.length)e=d[0].content;this.ma=e;e=!!this.Bb;d=this.Bb!==g;var c=!!this.Ab,f=this.Ab!==g,j=!!this.H,l;l=v(this.eb);var m=v(this.ic);l=l===m;var m=!!this.ja,s=!this.H?k:this.oa===this.ja,w=this.oa===this.tb,E;E=this.oa;var F=xa(this.tb);E=E===F;var F=!!this.Ia,U=!!this.va,P=!!this.ma,y=this.Ia===this.va,t=!this.ma?k:this.Ia===this.ma,Vc=!this.va||!this.ma?k:this.va===this.ma,Wc=!!this.rc,Xc=!!this.O,Yc=this.O?this.O.charAt(0)!=="/":k,
    Zc=!this.H?k:this.O===this.oa,$c=!this.ja?k:this.O===this.ja,ad=!this.H?k:this.hb===v(this.eb),bd=!this.ja?k:this.hb===v(this.ec),cd=!!this.Cb,dd=this.Cb?k:!!this.wc,ed=Fa()==="https:",fd=!!XMLHttpRequest,$;this.O?($=ya(this.O),$=$!==xa($)):$=k;e=[e,d,c,f,j,l,m,s,w,E,F,U,P,y,t,Vc,Wc,Xc,Yc,Zc,$c,ad,bd,cd,dd,ed,fd,$];d=1;for(f=c=0;f<e.length;f++)c|=e[f]&&d,d<<=1;this.Mb=("00000000"+c.toString(16)).slice(-8)};function X(){"postMessage"in window&&x(o,"message",r(this.Yb,this));S.call(this);Jb("_cbq",r(this.sb,this))}ba(X,S);
    X.prototype.D=function(){X.M.D.call(this);var a=this.na;Q().u=a;a=this.Wa;Q().t=a;this.bc=new Mc(this.a,this.ua);this.Ga=i;Kb(this.a);var a=!!this.a[Gb],b=Vb(!!this.a[Hb]&&a);K(this.a,"pingServer","ping.chartbeat.net");K(this.a,J,document.title);K(this.a,I,b);this.a[H]=this.a[H]?$b(this.a[H]):Sb(a);this.fa=v(b);this.a[I]=v(this.a[I]);this.Jb=B(C,"c",this.gc,this);this.Kb=B(C,"r",this.hc,this);this.U=i};X.prototype.Gb=function(a){this.Ga=a};
    X.prototype.ya=function(){this.k.update("_chartbeat4",["t="+this.Wa,"E="+this.F.ca,"x="+ic(this),"c="+Math.round((q()-this.la)/600)/100,"y="+jb("Height"),"w="+ib("Height")].join("&"),60,g,g,1)};var cb="h,p,u,d,g,g0,g1,g5,g3,g4,g6,n,nc,f,c,x,m,y,o,w,j,R,W,I,E,e,v,r,vp,K,l1,KK,PA,b,A,_c,_m,_x,_y,_z,_s,t,V,z,i,L,tz,l,,sn,C,sv,sr,sd,im".split(",");n=X.prototype;
    n.Z=function(a){this.ka++;var b={};b.g=this.a.uid;b.g0=Lb(this.a,"sections")||"No%20Section";b.g1=Lb(this.a,"authors")||"No%20Author";b.g2=Lb(this.a,"zone");b.g3=Lb(this.a,"sponsorName");b.g4=Lb(this.a,"type");!this.a.noCookies&&this.k.isSupported()?b.n=this.dc:b.nc=1;b.c=Math.round((q()-this.la)/600)/100;b.E=this.F.ca;var c=ic(this);this.ha=Math.max(this.ha,c);b.x=c;b.m=this.ha;b.y=jb("Height");b.o=jb("Width");b.w=ib("Height");b.b=this.Ta>0?this.Ta:"";if(this.lb&&!this.a.noCookies&&this.k.isSupported())b.f=
    this.lb;b[""]=Mb(this.a);b.t=this.Wa;b.V=147;b.tz=(new Date).getTimezoneOffset();b.sn=this.ka;b.sv=this.oc;b.sr=this.mc;b.sd=this.lc;c=this.F.ga;b.h=encodeURIComponent(this.a[I]);b.p=encodeURIComponent(this.a[H]);b.u=this.na;b.d=encodeURIComponent(this.fa);b.j=Math.round((this.X+2)*15E3/1E3);b.R=0;b.W=0;b.I=0;D.Ib()?b.W=1:this.a.reading&&+this.a.reading||c>0||b.c<0.09?b.R=1:b.I=1;b.e=c;c=dc(this);if(this.Va){this.Va=k;if(c)this.U=Nc(this);b.i=kc(this);var d=this.a.hudTrackable;d!==g&&(b.L=d?"1":"0")}if(c){if(this.U){if(b.v=
    encodeURIComponent(this.U.path),b.K=Oc(this.U),this.U.Pa>1)b.l1=this.U.Pa}else b.v=T(this);this.ua&&(b.vp=1)}else b.r=T(this);c=Ta(b.v);Q().v=c;c=Ta(b.r);Q().r=c;b.A=this.Ga?this.Ga:"";b._c=$a("utm_campaign",this.a.campaignTag);b._m=$a("utm_medium",this.a.mediumTag);b._x=$a("utm_source",this.a.sourceTag);b._y=$a("utm_content",this.a.contentTag);b._z=$a("utm_term",this.a.termTag);b.im=this.bc.Mb;d=this.a;c=d.idSync;if(ka(d[I]).toString()!==[-2029634429,-1659526092,-2053164062,-1348054445,1670716250].toString())c=
    c?Ka(c):"";else{if(d=window.OBR&&window.OBR.extern&&window.OBR.extern.pvId)c=c?c:{},c.obr=d;c=Ka(c)}b._s=c;b.z=lc(this);b.C=this.a.mobileApp?1:"";b.KK=a?Oc(a):"";a=this.k;c=a.zb;a.zb=k;b.l=c?1:"";this.F.ga=0;if(this.a.alias)b.PA=encodeURIComponent(this.a.alias);else{a=Pc(o.location.href);if(a.search.length>0)a.search=Ub(a.search);b.PA=encodeURIComponent(eb(a))}if(a=o.location.href.match(/[^?]+[?]([^#]+).*/))a=Za(a[1]),a.cb_rec&&(b.g5=encodeURIComponent(a.cb_rec));b.g6=Ya(this.a.uid);this.Aa(Fa()+
    "//"+this.a.pingServer+"/ping?"+bb(b))};
    n.Yb=function(a){var b=this.a,c=b.playerdomain||this.fa;if(oa(a.origin)===oa(c))if(c=a.data,z(c)&&c.indexOf("cbqpush::")===0)a=c.split("::"),a.length==3&&(a=a.slice(1),a[0].indexOf("_")===0&&this.sb(a));else if(c=="cbdata?"){var c="&u="+O("u"),d="&t="+O("t"),e="&v="+O("v"),f="&r="+O("r"),b="domain="+encodeURIComponent(b[I])+"&uid="+encodeURIComponent(b.uid)+"&path="+encodeURIComponent(b[H])+"&title="+kc(this)+"&referrer="+T(this)+"&internal="+(dc(this)?"1":"0")+"&subdomain="+encodeURIComponent(this.fa)+
    c+d+e+f+"&utoken="+this.na;a.source.postMessage(b,"*")}};n.sb=function(a){var b=a[0],a=a[1];if(b==="_demo"&&this.a._demo)this.a._demo=this.a._demo+"%2C"+a;else if(a!==this.a[b]&&(this.a[b]=a,this.X=0,this.ka>0&&!this.ra)){var c=this.ka,d=this;this.ra=Ra(function(){d.ra=i;d.ka===c&&d.Z()},1E3)}};function Pc(a){a=a.replace(/-{2,}/g,"-");a=db(a);a.pathname=N(a.pathname);return a}n.gc=function(a){Qc(this,a,"c")};n.hc=function(a){Qc(this,a,"r")};
    function Qc(a,b,c){if(a.Xa&&q()-a.la>=a.Ma)a.terminate();else{var d=b.href||"",d=Pc(d);if(d.protocol.indexOf("http")===0){var e=d.hostname!==o.location.hostname,d=eb(d),f=kb(b,function(a){return a.id}),j=xc(b,f),l="";a.a.moduleAttribute&&(l=(l=gb(b,a.a.moduleAttribute))?l.getAttribute(a.a.moduleAttribute):"",l=l.replace(/::/g,"-").substr(0,40));f?(j&&(j="/"+j),j="*[@id='"+f.id+"']"+j,f=xc(b)):f=j;j=ja(j);f=ja(f);b=nc(b,g,h);c={left:b.x,top:b.y,path:a.a[H],href:d,bb:j,Fa:f,ab:"",Ya:c,Pa:0,Tb:a.a[I],
    Na:l};a.Z(c);e||a.k.update("_chartbeat5",Rc(c),60,k,g,3,function(a){a=a.split("|");return a[2]+"|"+a[3]})}}}function Oc(a){return[a.left,a.top,a.bb,encodeURIComponent(a.ab),a.Ya,encodeURIComponent(a.href),a.Fa,a.Na].join("::")}function Rc(a){var b=encodeURIComponent(a.ab),b=b.replace(/-/g,"%2D");b.length>512&&(b="");return[a.left,a.top,encodeURIComponent(a.path),encodeURIComponent(a.href),a.bb,b,a.Ya,a.Fa,encodeURIComponent(a.Tb),a.Na].join("|")}
    function Nc(a){var b=a.k.q("_chartbeat5");if(!b)return i;var c=b.split("::"),b=c.length,d,e=i,f,j=Pc(o.location.href),l=eb(j);p(c,function(a,b){var c=a.split("|"),j=decodeURIComponent(c[3]);if(j){j=Aa(l,j);if(j===0)return d=c,e=b,k;else if(j===i)return h;if(f===g||j<f)f=j,d=c,e=b}});if(e===i)return i;c.splice(e,1);a.k.create("_chartbeat5",c.join("::"),60);(a=d[5])?(a=a.replace(/%2D/g,"-"),a=decodeURIComponent(a)):a="";return{left:d[0],top:d[1],path:decodeURIComponent(d[2]),href:decodeURIComponent(d[3]),
    bb:d[4]||"",ab:"",Fa:d.length>7?d[7]:"",Ya:d.length>6?d[6]:"c",Pa:b,Na:d[9]?d[9]:""}}n.terminate=function(){rb(C,this.Jb);rb(C,this.Kb);Sa(this.ra);this.ra=i;X.M.terminate.call(this)};function Sc(a){var b=Tc;return function(c,d){if(!yb){b();var e=o._sf_async_config,f="",j=e[H],j=oa(j);/^\//.test(j)&&(f=v(o.location.hostname));e[Ib]=Fa()+"//"+f+j;if(z(c))e[H]=N(c),d&&(e[J]=d);else if(A(c)){var l=["authors","sections",J,H,Ib];p(c,function(a,b){if(Ia(l,function(a){return a===b})!==-1||b.indexOf("_")===0)e[b]=b===H?N(a):a})}a()}}};if(!L.q("cb_optout")&&!o.pSUPERFLY){var Uc=new X,Y={};o.pSUPERFLY=Y;var gd=o.pSUPERFLY_mab,Z=o.pSUPERFLY_pub,hd=[];gd&&hd.push(gd);if(Z)hd.push(Z),Z.addEngagedAdFilter&&(Y.addEngagedAdFilter=Z.addEngagedAdFilter),Z.refreshAd&&(Y.refreshAd=Z.refreshAd),Z.registerGptSlot&&(Y.registerGptSlot=Z.registerGptSlot),Jb("_cba",function(a){a()});var Tc=function(){Uc.qa();p(hd,function(a){a.evps()})};Y.virtualPage=Sc(function(){Uc.Ba();p(hd,function(a){a.svps()})});Y.endTracking=Tc;Y.activity=r(Uc.Gb,Uc);Uc.Ea();
    Ic.exec(document.referrer)&&Kc();var id=M.B(h);if(!id?0:id.getItem("_cb_ip")){var jd=o.location;(!/^(.+[.])?chartbeat\.com$/.test(jd.hostname)?0:/^\/publishing\/(overlay|hud|mab)\//.test(jd.pathname))||pb(Jc)}else x(o,"message",Lc)};})();
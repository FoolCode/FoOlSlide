/**
 * jQuery.Preload - Multifunctional preloader
 * Copyright (c) 2008 Ariel Flesler - aflesler(at)gmail(dot)com
 * Dual licensed under MIT and GPL.
 * Date: 3/25/2009
 * @author Ariel Flesler
 * @version 1.0.8
 */
;(function($){var h=$.preload=function(c,d){if(c.split)c=$(c);d=$.extend({},h.defaults,d);var f=$.map(c,function(a){if(!a)return;if(a.split)return d.base+a+d.ext;var b=a.src||a.href;if(typeof d.placeholder=='string'&&a.src)a.src=d.placeholder;if(b&&d.find)b=b.replace(d.find,d.replace);return b||null}),data={loaded:0,failed:0,next:0,done:0,total:f.length};if(!data.total)return finish();var g=$(Array(d.threshold+1).join('<img/>')).load(handler).error(handler).bind('abort',handler).each(fetch);function handler(e){data.element=this;data.found=e.type=='load';data.image=this.src;data.index=this.index;var a=data.original=c[this.index];data[data.found?'loaded':'failed']++;data.done++;if(d.enforceCache)h.cache.push($('<img/>').attr('src',data.image)[0]);if(d.placeholder&&a.src)a.src=data.found?data.image:d.notFound||a.src;if(d.onComplete)d.onComplete(data);if(data.done<data.total)fetch(0,this);else{if(g&&g.unbind)g.unbind('load').unbind('error').unbind('abort');g=null;finish()}};function fetch(i,a,b){if(a.attachEvent&&data.next&&data.next%h.gap==0&&!b){setTimeout(function(){fetch(i,a,1)},0);return!1}if(data.next==data.total)return!1;a.index=data.next;a.src=f[data.next++];if(d.onRequest){data.index=a.index;data.element=a;data.image=a.src;data.original=c[data.next-1];d.onRequest(data)}};function finish(){if(d.onFinish)d.onFinish(data)}};h.gap=14;h.cache=[];h.defaults={threshold:2,base:'',ext:'',replace:''};$.fn.preload=function(a){h(this,a);return this}})(jQuery);




/**
 * jQuery.LocalScroll - Animated scrolling navigation, using anchors.
 * Copyright (c) 2007-2009 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * Date: 3/11/2009
 * @author Ariel Flesler
 * @version 1.2.7
 **/
;(function($){var l=location.href.replace(/#.*/,'');var g=$.localScroll=function(a){$('body').localScroll(a)};g.defaults={duration:1e3,axis:'y',event:'click',stop:true,target:window,reset:true};g.hash=function(a){if(location.hash){a=$.extend({},g.defaults,a);a.hash=false;if(a.reset){var e=a.duration;delete a.duration;$(a.target).scrollTo(0,a);a.duration=e}i(0,location,a)}};$.fn.localScroll=function(b){b=$.extend({},g.defaults,b);return b.lazy?this.bind(b.event,function(a){var e=$([a.target,a.target.parentNode]).filter(d)[0];if(e)i(a,e,b)}):this.find('a,area').filter(d).bind(b.event,function(a){i(a,this,b)}).end().end();function d(){return!!this.href&&!!this.hash&&this.href.replace(this.hash,'')==l&&(!b.filter||$(this).is(b.filter))}};function i(a,e,b){var d=e.hash.slice(1),f=document.getElementById(d)||document.getElementsByName(d)[0];if(!f)return;if(a)a.preventDefault();var h=$(b.target);if(b.lock&&h.is(':animated')||b.onBefore&&b.onBefore.call(b,a,f,h)===false)return;if(b.stop)h.stop(true);if(b.hash){var j=f.id==d?'id':'name',k=$('<a> </a>').attr(j,d).css({position:'absolute',top:$(window).scrollTop(),left:$(window).scrollLeft()});f[j]='';$('body').prepend(k);location=e.hash;k.remove();f[j]=d}h.scrollTo(f,b).trigger('notify.serialScroll',[f])}})(jQuery);




/**
 * jQuery.ScrollTo - Easy element scrolling using jQuery.
 * Copyright (c) 2007-2009 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * Date: 5/25/2009
 * @author Ariel Flesler
 * @version 1.4.2
 *
 * http://flesler.blogspot.com/2007/10/jqueryscrollto.html
 */
;(function(d){var k=d.scrollTo=function(a,i,e){d(window).scrollTo(a,i,e)};k.defaults={axis:'xy',duration:parseFloat(d.fn.jquery)>=1.3?0:1};k.window=function(a){return d(window)._scrollable()};d.fn._scrollable=function(){return this.map(function(){var a=this,i=!a.nodeName||d.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!i)return a;var e=(a.contentWindow||a).document||a.ownerDocument||a;return d.browser.safari||e.compatMode=='BackCompat'?e.body:e.documentElement})};d.fn.scrollTo=function(n,j,b){if(typeof j=='object'){b=j;j=0}if(typeof b=='function')b={onAfter:b};if(n=='max')n=9e9;b=d.extend({},k.defaults,b);j=j||b.speed||b.duration;b.queue=b.queue&&b.axis.length>1;if(b.queue)j/=2;b.offset=p(b.offset);b.over=p(b.over);return this._scrollable().each(function(){var q=this,r=d(q),f=n,s,g={},u=r.is('html,body');switch(typeof f){case'number':case'string':if(/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(f)){f=p(f);break}f=d(f,this);case'object':if(f.is||f.style)s=(f=d(f)).offset()}d.each(b.axis.split(''),function(a,i){var e=i=='x'?'Left':'Top',h=e.toLowerCase(),c='scroll'+e,l=q[c],m=k.max(q,i);if(s){g[c]=s[h]+(u?0:l-r.offset()[h]);if(b.margin){g[c]-=parseInt(f.css('margin'+e))||0;g[c]-=parseInt(f.css('border'+e+'Width'))||0}g[c]+=b.offset[h]||0;if(b.over[h])g[c]+=f[i=='x'?'width':'height']()*b.over[h]}else{var o=f[h];g[c]=o.slice&&o.slice(-1)=='%'?parseFloat(o)/100*m:o}if(/^\d+$/.test(g[c]))g[c]=g[c]<=0?0:Math.min(g[c],m);if(!a&&b.queue){if(l!=g[c])t(b.onAfterFirst);delete g[c]}});t(b.onAfter);function t(a){r.animate(g,j,b.easing,a&&function(){a.call(this,n,b)})}}).end()};k.max=function(a,i){var e=i=='x'?'Width':'Height',h='scroll'+e;if(!d(a).is('html,body'))return a[h]-d(a)[e.toLowerCase()]();var c='client'+e,l=a.ownerDocument.documentElement,m=a.ownerDocument.body;return Math.max(l[h],m[h])-Math.min(l[c],m[c])};function p(a){return typeof a=='object'?a:{top:a,left:a}}})(jQuery);


/*
 * jQuery BBQ: Back Button & Query Library - v1.2.1 - 2/17/2010
 * http://benalman.com/projects/jquery-bbq-plugin/
 * 
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function($,p){var i,m=Array.prototype.slice,r=decodeURIComponent,a=$.param,c,l,v,b=$.bbq=$.bbq||{},q,u,j,e=$.event.special,d="hashchange",A="querystring",D="fragment",y="elemUrlAttr",g="location",k="href",t="src",x=/^.*\?|#.*$/g,w=/^.*\#/,h,C={};function E(F){return typeof F==="string"}function B(G){var F=m.call(arguments,1);return function(){return G.apply(this,F.concat(m.call(arguments)))}}function n(F){return F.replace(/^[^#]*#?(.*)$/,"$1")}function o(F){return F.replace(/(?:^[^?#]*\?([^#]*).*$)?.*/,"$1")}function f(H,M,F,I,G){var O,L,K,N,J;if(I!==i){K=F.match(H?/^([^#]*)\#?(.*)$/:/^([^#?]*)\??([^#]*)(#?.*)/);J=K[3]||"";if(G===2&&E(I)){L=I.replace(H?w:x,"")}else{N=l(K[2]);I=E(I)?l[H?D:A](I):I;L=G===2?I:G===1?$.extend({},I,N):$.extend({},N,I);L=a(L);if(H){L=L.replace(h,r)}}O=K[1]+(H?"#":L||!K[1]?"?":"")+L+J}else{O=M(F!==i?F:p[g][k])}return O}a[A]=B(f,0,o);a[D]=c=B(f,1,n);c.noEscape=function(G){G=G||"";var F=$.map(G.split(""),encodeURIComponent);h=new RegExp(F.join("|"),"g")};c.noEscape(",/");$.deparam=l=function(I,F){var H={},G={"true":!0,"false":!1,"null":null};$.each(I.replace(/\+/g," ").split("&"),function(L,Q){var K=Q.split("="),P=r(K[0]),J,O=H,M=0,R=P.split("]["),N=R.length-1;if(/\[/.test(R[0])&&/\]$/.test(R[N])){R[N]=R[N].replace(/\]$/,"");R=R.shift().split("[").concat(R);N=R.length-1}else{N=0}if(K.length===2){J=r(K[1]);if(F){J=J&&!isNaN(J)?+J:J==="undefined"?i:G[J]!==i?G[J]:J}if(N){for(;M<=N;M++){P=R[M]===""?O.length:R[M];O=O[P]=M<N?O[P]||(R[M+1]&&isNaN(R[M+1])?{}:[]):J}}else{if($.isArray(H[P])){H[P].push(J)}else{if(H[P]!==i){H[P]=[H[P],J]}else{H[P]=J}}}}else{if(P){H[P]=F?i:""}}});return H};function z(H,F,G){if(F===i||typeof F==="boolean"){G=F;F=a[H?D:A]()}else{F=E(F)?F.replace(H?w:x,""):F}return l(F,G)}l[A]=B(z,0);l[D]=v=B(z,1);$[y]||($[y]=function(F){return $.extend(C,F)})({a:k,base:k,iframe:t,img:t,input:t,form:"action",link:k,script:t});j=$[y];function s(I,G,H,F){if(!E(H)&&typeof H!=="object"){F=H;H=G;G=i}return this.each(function(){var L=$(this),J=G||j()[(this.nodeName||"").toLowerCase()]||"",K=J&&L.attr(J)||"";L.attr(J,a[I](K,H,F))})}$.fn[A]=B(s,A);$.fn[D]=B(s,D);b.pushState=q=function(I,F){if(E(I)&&/^#/.test(I)&&F===i){F=2}var H=I!==i,G=c(p[g][k],H?I:{},H?F:2);p[g][k]=G+(/#/.test(G)?"":"#")};b.getState=u=function(F,G){return F===i||typeof F==="boolean"?v(F):v(G)[F]};b.removeState=function(F){var G={};if(F!==i){G=u();$.each($.isArray(F)?F:arguments,function(I,H){delete G[H]})}q(G,2)};e[d]=$.extend(e[d],{add:function(F){var H;function G(J){var I=J[D]=c();J.getState=function(K,L){return K===i||typeof K==="boolean"?l(I,K):l(I,L)[K]};H.apply(this,arguments)}if($.isFunction(F)){H=F;return G}else{H=F.handler;F.handler=G}}})})(jQuery,this);
/*
 * jQuery hashchange event - v1.2 - 2/11/2010
 * http://benalman.com/projects/jquery-hashchange-plugin/
 * 
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function($,i,b){var j,k=$.event.special,c="location",d="hashchange",l="href",f=$.browser,g=document.documentMode,h=f.msie&&(g===b||g<8),e="on"+d in i&&!h;function a(m){m=m||i[c][l];return m.replace(/^[^#]*#?(.*)$/,"$1")}$[d+"Delay"]=100;k[d]=$.extend(k[d],{setup:function(){if(e){return false}$(j.start)},teardown:function(){if(e){return false}$(j.stop)}});j=(function(){var m={},r,n,o,q;function p(){o=q=function(s){return s};if(h){n=$('<iframe src="javascript:0"/>').hide().insertAfter("body")[0].contentWindow;q=function(){return a(n.document[c][l])};o=function(u,s){if(u!==s){var t=n.document;t.open().close();t[c].hash="#"+u}};o(a())}}m.start=function(){if(r){return}var t=a();o||p();(function s(){var v=a(),u=q(t);if(v!==t){o(t=v,u);$(i).trigger(d)}else{if(u!==t){i[c][l]=i[c][l].replace(/#.*/,"")+"#"+u}}r=setTimeout(s,$[d+"Delay"])})()};m.stop=function(){if(!n){r&&clearTimeout(r);r=0}};return m})()})(jQuery,this);

/*

    AnythingZoomer
    a jQuery Plugin
    
    by: Chris Coyier
    http://css-tricks
    
    Version: 1.0
    
    Note: You can do whatever the heck you want with this.

*/

(function($) {
  
    $.anythingZoomer = {
    
        defaults: {
            smallArea: "#small",
            largeArea: "#large",
            zoomPort: "#overlay",
            mover: "#mover",
            expansionSize: 30,
            speedMultiplier: 1.5
            
        }
            
    }
    
    $.fn.extend({
        anythingZoomer:function(config) {
        
            var config = $.extend({}, $.anythingZoomer.defaults, config); 
            
            var wrap = $(this);
        
            var smallArea = $(config.smallArea);
            var largeArea = $(config.largeArea);
            var zoomPort = $(config.zoomPort);
            var mover = $(config.mover);
            
            var expansionSize = config.expansionSize;
            var speedMultiplier = config.speedMultiplier;
            
            function setup(smallArea, largeArea, wrap, zoomPort, mover, expansionSize, speedMultiplier) {
            
                smallArea
                    .show();
                    
                zoomPort
                    .fadeIn();
                    
                mover
                    .css({
                        width: mover.data("origWidth"),
                        height: mover.data("origHeight"),
                        overflow: "hidden",
                        position: "absolute"
                    })

                wrap
                    .css({
                        width: "auto"
                    })
            		.hover(function(){
            		     mover.fadeIn();
            		})
            		.mousemove(function(e){
            			
            			var x = e.pageX - smallArea.offset().left;
            			var y = e.pageY - smallArea.offset().top;
            			        			
            			if ( (x < -expansionSize) || (x > smallArea.width() + expansionSize) || (y < -expansionSize) || (y > smallArea.height() + expansionSize) ) {
            			     mover.fadeOut(100);
            			}
            							
            			mover.css({
            				top: y - 50,
            				left: x - 50
            			});
            			
            			largeArea.css({
            			
            			    left: (-(e.pageX - smallArea.offset().left)*speedMultiplier)+expansionSize,
            			    top: (-(e.pageY - smallArea.offset().top)*speedMultiplier)+expansionSize
            			
            			});
            			
            		})
            		.dblclick(function() {
            		
                        expand(smallArea, largeArea, wrap, zoomPort, mover, expansionSize, speedMultiplier);
            
            		});
            
            };
            
            function expand(smallArea, largeArea, wrap, zoomPort, mover, expansionSize, speedMultiplier) {
            
                  smallArea
        		      .hide(); 
        		      
        		  zoomPort
        		      .hide();       		      
        		  
        		  mover
        		      .fadeIn()
        		      .data("origWidth", mover.width())
        		      .data("origHeight", mover.height())
        		      .css({
        		          position: "static",
        		          height: "auto",
        		          width: "auto",
        			      overflow: "visible"
        		      });
        		      
        		  wrap
        		      .css({
        		          width: "100%"
        		      })
        		      .unbind()
        		      .dblclick(function(){
        		          setup(smallArea, largeArea, wrap, zoomPort, mover, expansionSize, speedMultiplier);
        		      });
        		      
        		      
        		  largeArea   
        		      .css({
        		          left: 0,
        		          top: 0,
        		          width: largeArea.data("origWidth")
        		      });
        		              
            };
            
            mover
		      .data("origWidth", mover.width())
		      .data("origHeight", mover.height());
		    
		    // Because the largeArea is often hidden, the width() function returns zero, take width from CSS instead  
		    largeArea
		      .data("origWidth", largeArea.css("width"));

            setup(smallArea, largeArea, wrap, zoomPort, mover, expansionSize, speedMultiplier);
        
            return this;
        
        }
        
    });
  
})(jQuery);


/*
https://github.com/balupton/History.js

Copyright (c) 2011, Benjamin Arthur Lupton
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
  Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
  Neither the name of Benjamin Arthur Lupton nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
if ( typeof window.JSON === 'undefined' ) {
	var JSON;JSON||(JSON={}),function(){function str(a,b){var c,d,e,f,g=gap,h,i=b[a];i&&typeof i=="object"&&typeof i.toJSON=="function"&&(i=i.toJSON(a)),typeof rep=="function"&&(i=rep.call(b,a,i));switch(typeof i){case"string":return quote(i);case"number":return isFinite(i)?String(i):"null";case"boolean":case"null":return String(i);case"object":if(!i)return"null";gap+=indent,h=[];if(Object.prototype.toString.apply(i)==="[object Array]"){f=i.length;for(c=0;c<f;c+=1)h[c]=str(c,i)||"null";e=h.length===0?"[]":gap?"[\n"+gap+h.join(",\n"+gap)+"\n"+g+"]":"["+h.join(",")+"]",gap=g;return e}if(rep&&typeof rep=="object"){f=rep.length;for(c=0;c<f;c+=1)d=rep[c],typeof d=="string"&&(e=str(d,i),e&&h.push(quote(d)+(gap?": ":":")+e))}else for(d in i)Object.hasOwnProperty.call(i,d)&&(e=str(d,i),e&&h.push(quote(d)+(gap?": ":":")+e));e=h.length===0?"{}":gap?"{\n"+gap+h.join(",\n"+gap)+"\n"+g+"}":"{"+h.join(",")+"}",gap=g;return e}}function quote(a){escapable.lastIndex=0;return escapable.test(a)?'"'+a.replace(escapable,function(a){var b=meta[a];return typeof b=="string"?b:"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4)})+'"':'"'+a+'"'}function f(a){return a<10?"0"+a:a}"use strict",typeof Date.prototype.toJSON!="function"&&(Date.prototype.toJSON=function(a){return isFinite(this.valueOf())?this.getUTCFullYear()+"-"+f(this.getUTCMonth()+1)+"-"+f(this.getUTCDate())+"T"+f(this.getUTCHours())+":"+f(this.getUTCMinutes())+":"+f(this.getUTCSeconds())+"Z":null},String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(a){return this.valueOf()});var cx=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,escapable=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,gap,indent,meta={"\b":"\\b","\t":"\\t","\n":"\\n","\f":"\\f","\r":"\\r",'"':'\\"',"\\":"\\\\"},rep;typeof JSON.stringify!="function"&&(JSON.stringify=function(a,b,c){var d;gap="",indent="";if(typeof c=="number")for(d=0;d<c;d+=1)indent+=" ";else typeof c=="string"&&(indent=c);rep=b;if(b&&typeof b!="function"&&(typeof b!="object"||typeof b.length!="number"))throw new Error("JSON.stringify");return str("",{"":a})}),typeof JSON.parse!="function"&&(JSON.parse=function(text,reviver){function walk(a,b){var c,d,e=a[b];if(e&&typeof e=="object")for(c in e)Object.hasOwnProperty.call(e,c)&&(d=walk(e,c),d!==undefined?e[c]=d:delete e[c]);return reviver.call(a,b,e)}var j;text=String(text),cx.lastIndex=0,cx.test(text)&&(text=text.replace(cx,function(a){return"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4)}));if(/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,"@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,"]").replace(/(?:^|:|,)(?:\s*\[)+/g,""))){j=eval("("+text+")");return typeof reviver=="function"?walk({"":j},""):j}throw new SyntaxError("JSON.parse")})}();
}

(function(a,b){function d(a,d){var e=d.__amplify__?JSON.parse(d.__amplify__):{};c.addType(a,function(f,g,h){var i=g,j=(new Date).getTime(),k,l;if(!f){i={};for(f in e)k=d[f],l=k?JSON.parse(k):{expires:-1},l.expires&&l.expires<=j?(delete d[f],delete e[f]):i[f.replace(/^__amplify__/,"")]=l.data;d.__amplify__=JSON.stringify(e);return i}f="__amplify__"+f;if(g===b){if(e[f]){k=d[f],l=k?JSON.parse(k):{expires:-1};if(l.expires&&l.expires<=j)delete d[f],delete e[f];else return l.data}}else if(g===null)delete d[f],delete e[f];else{l=JSON.stringify({data:g,expires:h.expires?j+h.expires:null});try{d[f]=l,e[f]=!0}catch(m){c[a]();try{d[f]=l,e[f]=!0}catch(m){throw c.error()}}}d.__amplify__=JSON.stringify(e);return i})}JSON.stringify=JSON.stringify||JSON.encode,JSON.parse=JSON.parse||JSON.decode;var c=a.store=function(a,b,d,e){var e=c.type;d&&d.type&&d.type in c.types&&(e=d.type);return c.types[e](a,b,d||{})};c.types={},c.type=null,c.addType=function(a,b){c.type||(c.type=a),c.types[a]=b,c[a]=function(b,d,e){e=e||{},e.type=a;return c(b,d,e)}},c.error=function(){return"amplify.store quota exceeded"};for(var e in{localStorage:1,sessionStorage:1})try{window[e].getItem&&d(e,window[e])}catch(f){}window.globalStorage&&(d("globalStorage",window.globalStorage[window.location.hostname]),c.type==="sessionStorage"&&(c.type="globalStorage")),function(){var a=document.createElement("div"),d="amplify",e;a.style.display="none",document.getElementsByTagName("head")[0].appendChild(a),a.addBehavior&&(a.addBehavior("#default#userdata"),a.load(d),e=a.getAttribute(d)?JSON.parse(a.getAttribute(d)):{},c.addType("userData",function(f,g,h){var i=g,j=(new Date).getTime(),k,l,m;if(!f){i={};for(f in e)k=a.getAttribute(f),l=k?JSON.parse(k):{expires:-1},l.expires&&l.expires<=j?(a.removeAttribute(f),delete e[f]):i[f]=l.data;a.setAttribute(d,JSON.stringify(e)),a.save(d);return i}f=f.replace(/[^-._0-9A-Za-z\xb7\xc0-\xd6\xd8-\xf6\xf8-\u037d\u37f-\u1fff\u200c-\u200d\u203f\u2040\u2070-\u218f]/g,"-");if(g===b){if(f in e){k=a.getAttribute(f),l=k?JSON.parse(k):{expires:-1};if(l.expires&&l.expires<=j)a.removeAttribute(f),delete e[f];else return l.data}}else g===null?(a.removeAttribute(f),delete e[f]):(m=a.getAttribute(f),l=JSON.stringify({data:g,expires:h.expires?j+h.expires:null}),a.setAttribute(f,l),e[f]=!0);a.setAttribute(d,JSON.stringify(e));try{a.save(d)}catch(n){m===null?(a.removeAttribute(f),delete e[f]):a.setAttribute(f,m),c.userData();try{a.setAttribute(f,l),e[f]=!0,a.save(d)}catch(n){m===null?(a.removeAttribute(f),delete e[f]):a.setAttribute(f,m);throw c.error()}}return i}))}(),d("memory",{})})(this.amplify=this.amplify||{});
(function(a,b){var c=a.History=a.History||{},d=a.jQuery;if(typeof c.Adapter!="undefined")throw new Error("History.js Adapter has already been loaded...");c.Adapter={bind:function(a,b,c){d(a).bind(b,c)},trigger:function(a,b){d(a).trigger(b)},onDomLoad:function(a){d(a)}},typeof c.init!="undefined"&&c.init()})(window);
(function(a,b){"use strict";var c=a.console||b,d=a.document,e=a.navigator,f=a.amplify||!1,g=a.setTimeout,h=a.clearTimeout,i=a.setInterval,j=a.JSON,k=a.History=a.History||{},l=a.history;j.stringify=j.stringify||j.encode,j.parse=j.parse||j.decode;if(typeof k.init!="undefined")throw new Error("History.js Core has already been loaded...");k.init=function(){if(typeof k.Adapter=="undefined")return!1;typeof k.initCore!="undefined"&&k.initCore(),typeof k.initHtml4!="undefined"&&k.initHtml4();return!0},k.initCore=function(){if(typeof k.initCore.initialized!="undefined")return!1;k.initCore.initialized=!0,k.options=k.options||{},k.options.hashChangeInterval=k.options.hashChangeInterval||100,k.options.safariPollInterval=k.options.safariPollInterval||500,k.options.doubleCheckInterval=k.options.doubleCheckInterval||500,k.options.storeInterval=k.options.storeInterval||1e3,k.options.busyDelay=k.options.busyDelay||250,k.options.debug=k.options.debug||!1,k.options.initialTitle=k.options.initialTitle||d.title,k.debug=function(){(k.options.debug||!1)&&k.log.apply(k,arguments)},k.log=function(){var a=typeof c!="undefined"&&typeof c.log!="undefined"&&typeof c.log.apply!="undefined",b=d.getElementById("log"),e,f,g;if(a){var h=Array.prototype.slice.call(arguments);e=h.shift(),typeof c.debug!="undefined"?c.debug.apply(c,[e,h]):c.log.apply(c,[e,h])}else e="\n"+arguments[0]+"\n";for(f=1,g=arguments.length;f<g;++f){var i=arguments[f];if(typeof i=="object"&&typeof j!="undefined")try{i=j.stringify(i)}catch(k){}e+="\n"+i+"\n"}b?(b.value+=e+"\n-----\n",b.scrollTop=b.scrollHeight-b.clientHeight):a||alert(e);return!0},k.getInternetExplorerMajorVersion=function(){var a=k.getInternetExplorerMajorVersion.cached=typeof k.getInternetExplorerMajorVersion.cached!="undefined"?k.getInternetExplorerMajorVersion.cached:function(){var a=3,b=d.createElement("div"),c=b.getElementsByTagName("i");while((b.innerHTML="<!--[if gt IE "+ ++a+"]><i></i><![endif]-->")&&c[0]);return a>4?a:!1}();return a},k.isInternetExplorer=function(){var a=k.isInternetExplorer.cached=typeof k.isInternetExplorer.cached!="undefined"?k.isInternetExplorer.cached:Boolean(k.getInternetExplorerMajorVersion());return a},k.emulated={pushState:!Boolean(a.history&&a.history.pushState&&a.history.replaceState&&!/ Mobile\/([1-7][a-z]|(8([abcde]|f(1[0-8]))))/i.test(e.userAgent)&&!/AppleWebKit\/5([0-2]|3[0-2])/i.test(e.userAgent)),hashChange:Boolean(!("onhashchange"in a||"onhashchange"in d)||k.isInternetExplorer()&&k.getInternetExplorerMajorVersion()<8)},k.enabled=!k.emulated.pushState,k.bugs={setHash:Boolean(!k.emulated.pushState&&e.vendor==="Apple Computer, Inc."&&/AppleWebKit\/5([0-2]|3[0-3])/.test(e.userAgent)),safariPoll:Boolean(!k.emulated.pushState&&e.vendor==="Apple Computer, Inc."&&/AppleWebKit\/5([0-2]|3[0-3])/.test(e.userAgent)),ieDoubleCheck:Boolean(k.isInternetExplorer()&&k.getInternetExplorerMajorVersion()<8),hashEscape:Boolean(k.isInternetExplorer()&&k.getInternetExplorerMajorVersion()<7)},k.isEmptyObject=function(a){for(var b in a)return!1;return!0},k.cloneObject=function(a){var b,c;a?(b=j.stringify(a),c=j.parse(b)):c={};return c},k.getRootUrl=function(){var a=d.location.protocol+"//"+(d.location.hostname||d.location.host);if(d.location.port||!1)a+=":"+d.location.port;a+="/";return a},k.getBaseHref=function(){var a=d.getElementsByTagName("base"),b=null,c="";a.length===1&&(b=a[0],c=b.href.replace(/[^\/]+$/,"")),c=c.replace(/\/+$/,""),c&&(c+="/");return c},k.getBaseUrl=function(){var a=k.getBaseHref()||k.getBasePageUrl()||k.getRootUrl();return a},k.getPageUrl=function(){var a=k.getState(!1,!1),b=(a||{}).url||d.location.href,c=b.replace(/\/+$/,"").replace(/[^\/]+$/,function(a,b,c){return/\./.test(a)?a:a+"/"});return c},k.getBasePageUrl=function(){var a=d.location.href.replace(/[#\?].*/,"").replace(/[^\/]+$/,function(a,b,c){return/[^\/]$/.test(a)?"":a}).replace(/\/+$/,"")+"/";return a},k.getFullUrl=function(a,b){var c=a,d=a.substring(0,1);b=typeof b=="undefined"?!0:b,/[a-z]+\:\/\//.test(a)||(d==="/"?c=k.getRootUrl()+a.replace(/^\/+/,""):d==="#"?c=k.getPageUrl().replace(/#.*/,"")+a:d==="?"?c=k.getPageUrl().replace(/[\?#].*/,"")+a:b?c=k.getBaseUrl()+a.replace(/^(\.\/)+/,""):c=k.getBasePageUrl()+a.replace(/^(\.\/)+/,""));return c.replace(/\#$/,"")},k.getShortUrl=function(a){var b=a,c=k.getBaseUrl(),d=k.getRootUrl();k.emulated.pushState&&(b=b.replace(c,"")),b=b.replace(d,"/"),k.isTraditionalAnchor(b)&&(b="./"+b),b=b.replace(/^(\.\/)+/g,"./").replace(/\#$/,"");return b},k.store=f?f.store("History.store")||{}:{},k.store.idToState=k.store.idToState||{},k.store.urlToId=k.store.urlToId||{},k.store.stateToId=k.store.stateToId||{},k.idToState=k.idToState||{},k.stateToId=k.stateToId||{},k.urlToId=k.urlToId||{},k.storedStates=k.storedStates||[],k.savedStates=k.savedStates||[],k.getState=function(a,b){typeof a=="undefined"&&(a=!0),typeof b=="undefined"&&(b=!0);var c=k.getLastSavedState();!c&&b&&(c=k.createStateObject()),a&&(c=k.cloneObject(c),c.url=c.cleanUrl||c.url);return c},k.getIdByState=function(a){var b=k.extractId(a.url);if(!b){var c=k.getStateString(a);if(typeof k.stateToId[c]!="undefined")b=k.stateToId[c];else if(typeof k.store.stateToId[c]!="undefined")b=k.store.stateToId[c];else{for(;;){b=String(Math.floor(Math.random()*1e3));if(typeof k.idToState[b]=="undefined"&&typeof k.store.idToState[b]=="undefined")break}k.stateToId[c]=b,k.idToState[b]=a}}return b},k.normalizeState=function(a){if(!a||typeof a!="object")a={};if(typeof a.normalized!="undefined")return a;if(!a.data||typeof a.data!="object")a.data={};var b={};b.normalized=!0,b.title=a.title||"",b.url=k.getFullUrl(k.unescapeString(a.url||d.location.href)),b.hash=k.getShortUrl(b.url),b.data=k.cloneObject(a.data),b.id=k.getIdByState(b),b.cleanUrl=b.url.replace(/\??\&_suid.*/,""),b.url=b.cleanUrl;var c=!k.isEmptyObject(b.data);if(b.title||c)b.hash=k.getShortUrl(b.url).replace(/\??\&_suid.*/,""),/\?/.test(b.hash)||(b.hash+="?"),b.hash+="&_suid="+b.id;b.hashedUrl=k.getFullUrl(b.hash),(k.emulated.pushState||k.bugs.safariPoll)&&k.hasUrlDuplicate(b)&&(b.url=b.hashedUrl);return b},k.createStateObject=function(a,b,c){var d={data:a,title:b,url:c};d=k.normalizeState(d);return d},k.getStateById=function(a){a=String(a);var c=k.idToState[a]||k.store.idToState[a]||b;return c},k.getStateString=function(a){var b=k.normalizeState(a),c={data:b.data,title:a.title,url:a.url},d=j.stringify(c);return d},k.getStateId=function(a){var b=k.normalizeState(a),c=b.id;return c},k.getHashByState=function(a){var b,c=k.normalizeState(a);b=c.hash;return b},k.extractId=function(a){var b,c,d;c=/(.*)\&_suid=([0-9]+)$/.exec(a),d=c?c[1]||a:a,b=c?String(c[2]||""):"";return b||!1},k.isTraditionalAnchor=function(a){var b=!/[\/\?\.]/.test(a);return b},k.extractState=function(a,b){var c=null;b=b||!1;var d=k.extractId(a);d&&(c=k.getStateById(d));if(!c){var e=k.getFullUrl(a);d=k.getIdByUrl(e)||!1,d&&(c=k.getStateById(d)),!c&&b&&!k.isTraditionalAnchor(a)&&(c=k.createStateObject(null,null,e))}return c},k.getIdByUrl=function(a){var c=k.urlToId[a]||k.store.urlToId[a]||b;return c},k.getLastSavedState=function(){return k.savedStates[k.savedStates.length-1]||b},k.getLastStoredState=function(){return k.storedStates[k.storedStates.length-1]||b},k.hasUrlDuplicate=function(a){var b=!1,c=k.extractState(a.url);b=c&&c.id!==a.id;return b},k.storeState=function(a){k.urlToId[a.url]=a.id,k.storedStates.push(k.cloneObject(a));return a},k.isLastSavedState=function(a){var b=!1;if(k.savedStates.length){var c=a.id,d=k.getLastSavedState(),e=d.id;b=c===e}return b},k.saveState=function(a){if(k.isLastSavedState(a))return!1;k.savedStates.push(k.cloneObject(a));return!0},k.getStateByIndex=function(a){var b=null;typeof a=="undefined"?b=k.savedStates[k.savedStates.length-1]:a<0?b=k.savedStates[k.savedStates.length+a]:b=k.savedStates[a];return b},k.getHash=function(){var a=k.unescapeHash(d.location.hash);return a},k.unescapeString=function(b){var c=b,d;for(;;){d=a.unescape(c);if(d===c)break;c=d}return c},k.unescapeHash=function(a){var b=k.normalizeHash(a);b=k.unescapeString(b);return b},k.normalizeHash=function(a){var b=a.replace(/[^#]*#/,"").replace(/#.*/,"");return b},k.setHash=function(a,b){if(b!==!1&&k.busy()){k.pushQueue({scope:k,callback:k.setHash,args:arguments,queue:b});return!1}var c=k.escapeHash(a);k.busy(!0);var e=k.extractState(a,!0);if(e&&!k.emulated.pushState)k.pushState(e.data,e.title,e.url,!1);else if(d.location.hash!==c)if(k.bugs.setHash){var f=k.getPageUrl();k.pushState(null,null,f+"#"+c,!1)}else d.location.hash=c;return k},k.escapeHash=function(b){var c=k.normalizeHash(b);c=a.escape(c),k.bugs.hashEscape||(c=c.replace(/\%21/g,"!").replace(/\%26/g,"&").replace(/\%3D/g,"=").replace(/\%3F/g,"?"));return c},k.getHashByUrl=function(a){var b=String(a).replace(/([^#]*)#?([^#]*)#?(.*)/,"$2");b=k.unescapeHash(b);return b},k.setTitle=function(a){var b=a.title;if(!b){var c=k.getStateByIndex(0);c&&c.url===a.url&&(b=c.title||k.options.initialTitle)}try{d.getElementsByTagName("title")[0].innerHTML=b.replace("<","&lt;").replace(">","&gt;").replace(" & "," &amp; ")}catch(e){}d.title=b;return k},k.queues=[],k.busy=function(a){typeof a!="undefined"?k.busy.flag=a:typeof k.busy.flag=="undefined"&&(k.busy.flag=!1);if(!k.busy.flag){h(k.busy.timeout);var b=function(){if(!k.busy.flag)for(var a=k.queues.length-1;a>=0;--a){var c=k.queues[a];if(c.length===0)continue;var d=c.shift();k.fireQueueItem(d),k.busy.timeout=g(b,k.options.busyDelay)}};k.busy.timeout=g(b,k.options.busyDelay)}return k.busy.flag},k.fireQueueItem=function(a){return a.callback.apply(a.scope||k,a.args||[])},k.pushQueue=function(a){k.queues[a.queue||0]=k.queues[a.queue||0]||[],k.queues[a.queue||0].push(a);return k},k.queue=function(a,b){typeof a=="function"&&(a={callback:a}),typeof b!="undefined"&&(a.queue=b),k.busy()?k.pushQueue(a):k.fireQueueItem(a);return k},k.clearQueue=function(){k.busy.flag=!1,k.queues=[];return k},k.stateChanged=!1,k.doubleChecker=!1,k.doubleCheckComplete=function(){k.stateChanged=!0,k.doubleCheckClear();return k},k.doubleCheckClear=function(){k.doubleChecker&&(h(k.doubleChecker),k.doubleChecker=!1);return k},k.doubleCheck=function(a){k.stateChanged=!1,k.doubleCheckClear(),k.bugs.ieDoubleCheck&&(k.doubleChecker=g(function(){k.doubleCheckClear(),k.stateChanged||a();return!0},k.options.doubleCheckInterval));return k},k.safariStatePoll=function(){var b=k.extractState(d.location.href),c;if(!k.isLastSavedState(b))c=b;else return;c||(c=k.createStateObject()),k.Adapter.trigger(a,"popstate");return k},k.back=function(a){if(a!==!1&&k.busy()){k.pushQueue({scope:k,callback:k.back,args:arguments,queue:a});return!1}k.busy(!0),k.doubleCheck(function(){k.back(!1)}),l.go(-1);return!0},k.forward=function(a){if(a!==!1&&k.busy()){k.pushQueue({scope:k,callback:k.forward,args:arguments,queue:a});return!1}k.busy(!0),k.doubleCheck(function(){k.forward(!1)}),l.go(1);return!0},k.go=function(a,b){var c;if(a>0)for(c=1;c<=a;++c)k.forward(b);else{if(!(a<0))throw new Error("History.go: History.go requires a positive or negative integer passed.");for(c=-1;c>=a;--c)k.back(b)}return k},k.saveState(k.storeState(k.extractState(d.location.href,!0))),f&&(k.onUnload=function(){var a=f.store("History.store")||{},b;a.idToState=a.idToState||{},a.urlToId=a.urlToId||{},a.stateToId=a.stateToId||{};for(b in k.idToState){if(!k.idToState.hasOwnProperty(b))continue;a.idToState[b]=k.idToState[b]}for(b in k.urlToId){if(!k.urlToId.hasOwnProperty(b))continue;a.urlToId[b]=k.urlToId[b]}for(b in k.stateToId){if(!k.stateToId.hasOwnProperty(b))continue;a.stateToId[b]=k.stateToId[b]}k.store=a,f.store("History.store",a)},i(k.onUnload,k.options.storeInterval),k.Adapter.bind(a,"beforeunload",k.onUnload),k.Adapter.bind(a,"unload",k.onUnload));if(k.emulated.pushState){var m=function(){};k.pushState=k.pushState||m,k.replaceState=k.replaceState||m}else{k.onPopState=function(b){k.doubleCheckComplete();var c=k.getHash();if(c){var e=k.extractState(c||d.location.href,!0);e?k.replaceState(e.data,e.title,e.url,!1):(k.Adapter.trigger(a,"anchorchange"),k.busy(!1)),k.expectedStateId=!1;return!1}var f=!1;b=b||{},typeof b.state=="undefined"&&(typeof b.originalEvent!="undefined"&&typeof b.originalEvent.state!="undefined"?b.state=b.originalEvent.state||!1:typeof b.event!="undefined"&&typeof b.event.state!="undefined"&&(b.state=b.event.state||!1)),b.state=b.state||!1,b.state?f=k.getStateById(b.state):k.expectedStateId?f=k.getStateById(k.expectedStateId):f=k.extractState(d.location.href),f||(f=k.createStateObject(null,null,d.location.href)),k.expectedStateId=!1;if(k.isLastSavedState(f)){k.busy(!1);return!1}k.storeState(f),k.saveState(f),k.setTitle(f),k.Adapter.trigger(a,"statechange"),k.busy(!1);return!0},k.Adapter.bind(a,"popstate",k.onPopState),k.pushState=function(b,c,d,e){if(k.getHashByUrl(d)&&k.emulated.pushState)throw new Error("History.js does not support states with fragement-identifiers (hashes/anchors).");if(e!==!1&&k.busy()){k.pushQueue({scope:k,callback:k.pushState,args:arguments,queue:e});return!1}k.busy(!0);var f=k.createStateObject(b,c,d);k.isLastSavedState(f)?k.busy(!1):(k.storeState(f),k.expectedStateId=f.id,l.pushState(f.id,f.title,f.url),k.Adapter.trigger(a,"popstate"));return!0},k.replaceState=function(b,c,d,e){if(k.getHashByUrl(d)&&k.emulated.pushState)throw new Error("History.js does not support states with fragement-identifiers (hashes/anchors).");if(e!==!1&&k.busy()){k.pushQueue({scope:k,callback:k.replaceState,args:arguments,queue:e});return!1}k.busy(!0);var f=k.createStateObject(b,c,d);k.isLastSavedState(f)?k.busy(!1):(k.storeState(f),k.expectedStateId=f.id,l.replaceState(f.id,f.title,f.url),k.Adapter.trigger(a,"popstate"));return!0},k.bugs.safariPoll&&i(k.safariStatePoll,k.options.safariPollInterval);if(e.vendor==="Apple Computer, Inc."||(e.appCodeName||"")==="Mozilla")k.Adapter.bind(a,"hashchange",function(){k.Adapter.trigger(a,"popstate")}),k.getHash()&&k.Adapter.onDomLoad(function(){k.Adapter.trigger(a,"hashchange")})}},k.init()})(window);
(function(a,b){"use strict";var c=a.document,d=a.setTimeout||d,e=a.clearTimeout||e,f=a.setInterval||f,g=a.History=a.History||{};if(typeof g.initHtml4!="undefined")throw new Error("History.js HTML4 Support has already been loaded...");g.initHtml4=function(){if(typeof g.initHtml4.initialized!="undefined")return!1;g.initHtml4.initialized=!0,g.enabled=!0,g.savedHashes=[],g.isLastHash=function(a){var b=g.getHashByIndex(),c=a===b;return c},g.saveHash=function(a){if(g.isLastHash(a))return!1;g.savedHashes.push(a);return!0},g.getHashByIndex=function(a){var b=null;typeof a=="undefined"?b=g.savedHashes[g.savedHashes.length-1]:a<0?b=g.savedHashes[g.savedHashes.length+a]:b=g.savedHashes[a];return b},g.discardedHashes={},g.discardedStates={},g.discardState=function(a,b,c){var d=g.getHashByState(a),e={discardedState:a,backState:c,forwardState:b};g.discardedStates[d]=e;return!0},g.discardHash=function(a,b,c){var d={discardedHash:a,backState:c,forwardState:b};g.discardedHashes[a]=d;return!0},g.discardedState=function(a){var b=g.getHashByState(a),c=g.discardedStates[b]||!1;return c},g.discardedHash=function(a){var b=g.discardedHashes[a]||!1;return b},g.recycleState=function(a){var b=g.getHashByState(a);g.discardedState(a)&&delete g.discardedStates[b];return!0},g.emulated.hashChange&&(g.hashChangeInit=function(){g.checkerFunction=null;var b="";if(g.isInternetExplorer()){var d="historyjs-iframe",e=c.createElement("iframe");e.setAttribute("id",d),e.style.display="none",c.body.appendChild(e),e.contentWindow.document.open(),e.contentWindow.document.close();var h="",i=!1;g.checkerFunction=function(){if(i)return!1;i=!0;var c=g.getHash()||"",d=g.unescapeHash(e.contentWindow.document.location.hash)||"";c!==b?(b=c,d!==c&&(h=d=c,e.contentWindow.document.open(),e.contentWindow.document.close(),e.contentWindow.document.location.hash=g.escapeHash(c)),g.Adapter.trigger(a,"hashchange")):d!==h&&(h=d,g.setHash(d,!1)),i=!1;return!0}}else g.checkerFunction=function(){var c=g.getHash();c!==b&&(b=c,g.Adapter.trigger(a,"hashchange"));return!0};f(g.checkerFunction,g.options.hashChangeInterval);return!0},g.Adapter.onDomLoad(g.hashChangeInit)),g.emulated.pushState&&(g.onHashChange=function(b){var d=b&&b.newURL||c.location.href,e=g.getHashByUrl(d),f=null,h=null,i=null;if(g.isLastHash(e)){g.busy(!1);return!1}g.doubleCheckComplete(),g.saveHash(e);if(e&&g.isTraditionalAnchor(e)){g.Adapter.trigger(a,"anchorchange"),g.busy(!1);return!1}f=g.extractState(g.getFullUrl(e||c.location.href,!1),!0);if(g.isLastSavedState(f)){g.busy(!1);return!1}h=g.getHashByState(f);var j=g.discardedState(f);if(j){g.getHashByIndex(-2)===g.getHashByState(j.forwardState)?g.back(!1):g.forward(!1);return!1}g.pushState(f.data,f.title,f.url,!1);return!0},g.Adapter.bind(a,"hashchange",g.onHashChange),g.pushState=function(b,d,e,f){if(g.getHashByUrl(e))throw new Error("History.js does not support states with fragement-identifiers (hashes/anchors).");if(f!==!1&&g.busy()){g.pushQueue({scope:g,callback:g.pushState,args:arguments,queue:f});return!1}g.busy(!0);var h=g.createStateObject(b,d,e),i=g.getHashByState(h),j=g.getState(!1),k=g.getHashByState(j),l=g.getHash();g.storeState(h),g.expectedStateId=h.id,g.recycleState(h),g.setTitle(h);if(i===k){g.busy(!1);return!1}if(i!==l&&i!==g.getShortUrl(c.location.href)){g.setHash(i,!1);return!1}g.saveState(h),g.Adapter.trigger(a,"statechange"),g.busy(!1);return!0},g.replaceState=function(a,b,c,d){if(g.getHashByUrl(c))throw new Error("History.js does not support states with fragement-identifiers (hashes/anchors).");if(d!==!1&&g.busy()){g.pushQueue({scope:g,callback:g.replaceState,args:arguments,queue:d});return!1}g.busy(!0);var e=g.createStateObject(a,b,c),f=g.getState(!1),h=g.getStateByIndex(-2);g.discardState(f,e,h),g.pushState(e.data,e.title,e.url,!1);return!0},g.getHash()&&!g.emulated.hashChange&&g.Adapter.onDomLoad(function(){g.Adapter.trigger(a,"hashchange")}))},g.init()})(window);
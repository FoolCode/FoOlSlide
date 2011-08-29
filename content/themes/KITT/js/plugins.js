// usage: log('inside coolFunc', this, arguments);
// paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
window.log = function(){
  log.history = log.history || [];   // store logs to an array for reference
  log.history.push(arguments);
  if(this.console) {
    arguments.callee = arguments.callee.caller;
    var newarr = [].slice.call(arguments);
    (typeof console.log === 'object' ? log.apply.call(console.log, console, newarr) : console.log.apply(console, newarr));
  }
};

// make it safe to use console.log always
(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,timeStamp,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();){b[a]=b[a]||c}})((function(){try
{console.log();return window.console;}catch(err){return window.console={};}})());


// place any jQuery/helper plugins in here, instead of separate, slower script files.

(function() {
	if (window.__twitterIntentHandler) return;
	var intentRegex = /twitter\.com(\:\d{2,4})?\/intent\/(\w+)/,
	windowOptions = 'scrollbars=yes,resizable=yes,toolbar=no,location=yes',
	width = 550,
	height = 420,
	winHeight = screen.height,
	winWidth = screen.width;

	function handleIntent(e) {
		e = e || window.event;
		var target = e.target || e.srcElement,
		m, left, top;

		while (target && target.nodeName.toLowerCase() !== 'a') {
			target = target.parentNode;
		}

		if (target && target.nodeName.toLowerCase() === 'a' && target.href) {
			m = target.href.match(intentRegex);
			if (m) {
				left = Math.round((winWidth / 2) - (width / 2));
				top = 0;

				if (winHeight > height) {
					top = Math.round((winHeight / 2) - (height / 2));
				}

				window.open(target.href, 'intent', windowOptions + ',width=' + width +
					',height=' + height + ',left=' + left + ',top=' + top);
				e.returnValue = false;
				e.preventDefault && e.preventDefault();
			}
		}
	}

	if (document.addEventListener) {
		document.addEventListener('click', handleIntent, false);
	} else if (document.attachEvent) {
		document.attachEvent('onclick', handleIntent);
	}
	window.__twitterIntentHandler = true;
}());


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
	var JSON;
	JSON||(JSON={}),function(){
		function str(a,b){
			var c,d,e,f,g=gap,h,i=b[a];
			i&&typeof i=="object"&&typeof i.toJSON=="function"&&(i=i.toJSON(a)),typeof rep=="function"&&(i=rep.call(b,a,i));
			switch(typeof i){
				case"string":
					return quote(i);
				case"number":
					return isFinite(i)?String(i):"null";
				case"boolean":case"null":
					return String(i);
				case"object":
					if(!i)return"null";
					gap+=indent,h=[];
					if(Object.prototype.toString.apply(i)==="[object Array]"){
					f=i.length;
					for(c=0;c<f;c+=1)h[c]=str(c,i)||"null";
					e=h.length===0?"[]":gap?"[\n"+gap+h.join(",\n"+gap)+"\n"+g+"]":"["+h.join(",")+"]",gap=g;
					return e
					}
					if(rep&&typeof rep=="object"){
					f=rep.length;
					for(c=0;c<f;c+=1)d=rep[c],typeof d=="string"&&(e=str(d,i),e&&h.push(quote(d)+(gap?": ":":")+e))
						}else for(d in i)Object.hasOwnProperty.call(i,d)&&(e=str(d,i),e&&h.push(quote(d)+(gap?": ":":")+e));e=h.length===0?"{}":gap?"{\n"+gap+h.join(",\n"+gap)+"\n"+g+"}":"{"+h.join(",")+"}",gap=g;
					return e
					}
				}
		function quote(a){
		escapable.lastIndex=0;
		return escapable.test(a)?'"'+a.replace(escapable,function(a){
			var b=meta[a];
			return typeof b=="string"?b:"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4)
			})+'"':'"'+a+'"'
		}
		function f(a){
		return a<10?"0"+a:a
		}
		"use strict",typeof Date.prototype.toJSON!="function"&&(Date.prototype.toJSON=function(a){
		return isFinite(this.valueOf())?this.getUTCFullYear()+"-"+f(this.getUTCMonth()+1)+"-"+f(this.getUTCDate())+"T"+f(this.getUTCHours())+":"+f(this.getUTCMinutes())+":"+f(this.getUTCSeconds())+"Z":null
		},String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(a){
		return this.valueOf()
		});
	var cx=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,escapable=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,gap,indent,meta={
		"\b":"\\b",
		"\t":"\\t",
		"\n":"\\n",
		"\f":"\\f",
		"\r":"\\r",
		'"':'\\"',
		"\\":"\\\\"
	},rep;
	typeof JSON.stringify!="function"&&(JSON.stringify=function(a,b,c){
		var d;
		gap="",indent="";
		if(typeof c=="number")for(d=0;d<c;d+=1)indent+=" ";else typeof c=="string"&&(indent=c);
		rep=b;
		if(b&&typeof b!="function"&&(typeof b!="object"||typeof b.length!="number"))throw new Error("JSON.stringify");
		return str("",{
			"":a
		})
		}),typeof JSON.parse!="function"&&(JSON.parse=function(text,reviver){
		function walk(a,b){
			var c,d,e=a[b];
			if(e&&typeof e=="object")for(c in e)Object.hasOwnProperty.call(e,c)&&(d=walk(e,c),d!==undefined?e[c]=d:delete e[c]);return reviver.call(a,b,e)
			}
			var j;
		text=String(text),cx.lastIndex=0,cx.test(text)&&(text=text.replace(cx,function(a){
			return"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4)
			}));
		if(/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,"@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,"]").replace(/(?:^|:|,)(?:\s*\[)+/g,""))){
			j=eval("("+text+")");
			return typeof reviver=="function"?walk({
				"":j
			},""):j
			}
			throw new SyntaxError("JSON.parse")
		})
	}();
}

(function(a,b){
	function d(a,d){
		var e=d.__amplify__?JSON.parse(d.__amplify__):{};
		
		c.addType(a,function(f,g,h){
			var i=g,j=(new Date).getTime(),k,l;
			if(!f){
				i={};
				
				for(f in e)k=d[f],l=k?JSON.parse(k):{
					expires:-1
				},l.expires&&l.expires<=j?(delete d[f],delete e[f]):i[f.replace(/^__amplify__/,"")]=l.data;d.__amplify__=JSON.stringify(e);
				return i
				}
				f="__amplify__"+f;
			if(g===b){
				if(e[f]){
					k=d[f],l=k?JSON.parse(k):{
						expires:-1
					};
					
					if(l.expires&&l.expires<=j)delete d[f],delete e[f];else return l.data
						}
					}else if(g===null)delete d[f],delete e[f];
			else{
			l=JSON.stringify({
				data:g,
				expires:h.expires?j+h.expires:null
				});
			try{
				d[f]=l,e[f]=!0
				}catch(m){
				c[a]();
				try{
					d[f]=l,e[f]=!0
					}catch(m){
					throw c.error()
					}
				}
		}
		d.__amplify__=JSON.stringify(e);
	return i
	})
}
JSON.stringify=JSON.stringify||JSON.encode,JSON.parse=JSON.parse||JSON.decode;
var c=a.store=function(a,b,d,e){
	var e=c.type;
	d&&d.type&&d.type in c.types&&(e=d.type);
	return c.types[e](a,b,d||{})
	};
	
c.types={},c.type=null,c.addType=function(a,b){
	c.type||(c.type=a),c.types[a]=b,c[a]=function(b,d,e){
		e=e||{},e.type=a;
		return c(b,d,e)
		}
	},c.error=function(){
	return"amplify.store quota exceeded"
	};
	
for(var e in{
	localStorage:1,
	sessionStorage:1
})try{
	window[e].getItem&&d(e,window[e])
	}catch(f){}
	window.globalStorage&&(d("globalStorage",window.globalStorage[window.location.hostname]),c.type==="sessionStorage"&&(c.type="globalStorage")),function(){
	var a=document.createElement("div"),d="amplify",e;
	a.style.display="none",document.getElementsByTagName("head")[0].appendChild(a),a.addBehavior&&(a.addBehavior("#default#userdata"),a.load(d),e=a.getAttribute(d)?JSON.parse(a.getAttribute(d)):{},c.addType("userData",function(f,g,h){
		var i=g,j=(new Date).getTime(),k,l,m;
		if(!f){
			i={};
			
			for(f in e)k=a.getAttribute(f),l=k?JSON.parse(k):{
				expires:-1
			},l.expires&&l.expires<=j?(a.removeAttribute(f),delete e[f]):i[f]=l.data;a.setAttribute(d,JSON.stringify(e)),a.save(d);
			return i
			}
			f=f.replace(/[^-._0-9A-Za-z\xb7\xc0-\xd6\xd8-\xf6\xf8-\u037d\u37f-\u1fff\u200c-\u200d\u203f\u2040\u2070-\u218f]/g,"-");
		if(g===b){
			if(f in e){
				k=a.getAttribute(f),l=k?JSON.parse(k):{
					expires:-1
				};
				
				if(l.expires&&l.expires<=j)a.removeAttribute(f),delete e[f];else return l.data
					}
				}else g===null?(a.removeAttribute(f),delete e[f]):(m=a.getAttribute(f),l=JSON.stringify({
		data:g,
		expires:h.expires?j+h.expires:null
		}),a.setAttribute(f,l),e[f]=!0);
		a.setAttribute(d,JSON.stringify(e));
		try{
		a.save(d)
		}catch(n){
		m===null?(a.removeAttribute(f),delete e[f]):a.setAttribute(f,m),c.userData();
		try{
			a.setAttribute(f,l),e[f]=!0,a.save(d)
			}catch(n){
			m===null?(a.removeAttribute(f),delete e[f]):a.setAttribute(f,m);
			throw c.error()
			}
		}
	return i
		}))
}(),d("memory",{})
})(this.amplify=this.amplify||{});
(function(a,b){
	var c=a.History=a.History||{},d=a.jQuery;
	if(typeof c.Adapter!="undefined")throw new Error("History.js Adapter has already been loaded...");
	c.Adapter={
		bind:function(a,b,c){
			d(a).bind(b,c)
			},
		trigger:function(a,b){
			d(a).trigger(b)
			},
		onDomLoad:function(a){
			d(a)
			}
		},typeof c.init!="undefined"&&c.init()
	})(window);

/**
								 * History.js Core
								 * @author Benjamin Arthur Lupton <contact@balupton.com>
								 * @copyright 2010-2011 Benjamin Arthur Lupton <contact@balupton.com>
								 * @license New BSD License <http://creativecommons.org/licenses/BSD/>
								 */

(function(window,undefined){
	"use strict";

	// --------------------------------------------------------------------------
	// Initialise

	// Localise Globals
	var
	console = window.console||undefined, // Prevent a JSLint complain
	document = window.document, // Make sure we are using the correct document
	navigator = window.navigator, // Make sure we are using the correct navigator
	amplify = window.amplify||false, // Amplify.js
	setTimeout = window.setTimeout,
	clearTimeout = window.clearTimeout,
	setInterval = window.setInterval,
	clearInterval = window.clearInterval,
	JSON = window.JSON,
	History = window.History = window.History||{}, // Public History Object
	history = window.history; // Old History Object

	// MooTools Compatibility
	JSON.stringify = JSON.stringify||JSON.encode;
	JSON.parse = JSON.parse||JSON.decode;

	// Check Existence
	if ( typeof History.init !== 'undefined' ) {
		throw new Error('History.js Core has already been loaded...');
	}

	// Initialise History
	History.init = function(){
		// Check Load Status of Adapter
		if ( typeof History.Adapter === 'undefined' ) {
			return false;
		}

		// Check Load Status of Core
		if ( typeof History.initCore !== 'undefined' ) {
			History.initCore();
		}

		// Check Load Status of HTML4 Support
		if ( typeof History.initHtml4 !== 'undefined' ) {
			History.initHtml4();
		}

		// Return true
		return true;
	};

	// --------------------------------------------------------------------------
	// Initialise Core

	// Initialise Core
	History.initCore = function(){
		// Initialise
		if ( typeof History.initCore.initialized !== 'undefined' ) {
			// Already Loaded
			return false;
		}
		else {
			History.initCore.initialized = true;
		}

		// ----------------------------------------------------------------------
		// Options

		/**
										 * History.options
										 * Configurable options
										 */
		History.options = History.options||{};

		/**
										 * History.options.hashChangeInterval
										 * How long should the interval be before hashchange checks
										 */
		History.options.hashChangeInterval = History.options.hashChangeInterval || 100;

		/**
										 * History.options.safariPollInterval
										 * How long should the interval be before safari poll checks
										 */
		History.options.safariPollInterval = History.options.safariPollInterval || 500;

		/**
										 * History.options.doubleCheckInterval
										 * How long should the interval be before we perform a double check
										 */
		History.options.doubleCheckInterval = History.options.doubleCheckInterval || 500;

		/**
										 * History.options.storeInterval
										 * How long should we wait between store calls
										 */
		History.options.storeInterval = History.options.storeInterval || 1000;

		/**
										 * History.options.busyDelay
										 * How long should we wait between busy events
										 */
		History.options.busyDelay = History.options.busyDelay || 250;

		/**
										 * History.options.debug
										 * If true will enable debug messages to be logged
										 */
		History.options.debug = History.options.debug || false;

		/**
										 * History.options.initialTitle
										 * What is the title of the initial state
										 */
		History.options.initialTitle = History.options.initialTitle || document.title;


		// ----------------------------------------------------------------------
		// Interval record

		/**
										 * History.intervalList
										 * List of intervals set, to be cleared when document is unloaded.
										 */
		History.intervalList = [];

		/**
										 * History.clearAllIntervals
										 * Clears all setInterval instances.
										 */
		History.clearAllIntervals = function(){
			var i, il = History.intervalList;
			if (typeof il !== "undefined" && il !== null) {
				for (i = 0; i < il.length; i++) {
					clearInterval(il[i]);
				}
				History.intervalList = null;
			}
		};
		History.Adapter.bind(window,"beforeunload",History.clearAllIntervals);
		History.Adapter.bind(window,"unload",History.clearAllIntervals);


		// ----------------------------------------------------------------------
		// Debug

		/**
										 * History.debug(message,...)
										 * Logs the passed arguments if debug enabled
										 */
		History.debug = function(){
			if ( (History.options.debug||false) ) {
				History.log.apply(History,arguments);
			}
		};

		/**
										 * History.log(message,...)
										 * Logs the passed arguments
										 */
		History.log = function(){
			// Prepare
			var
			consoleExists = !(typeof console === 'undefined' || typeof console.log === 'undefined' || typeof console.log.apply === 'undefined'),
			textarea = document.getElementById('log'),
			message,
			i,n
			;

			// Write to Console
			if ( consoleExists ) {
				var args = Array.prototype.slice.call(arguments);
				message = args.shift();
				if ( typeof console.debug !== 'undefined' ) {
					console.debug.apply(console,[message,args]);
				}
				else {
					console.log.apply(console,[message,args]);
				}
			}
			else {
				message = ("\n"+arguments[0]+"\n");
			}

			// Write to log
			for ( i=1,n=arguments.length; i<n; ++i ) {
				var arg = arguments[i];
				if ( typeof arg === 'object' && typeof JSON !== 'undefined' ) {
					try {
						arg = JSON.stringify(arg);
					}
					catch ( Exception ) {
					// Recursive Object
					}
				}
				message += "\n"+arg+"\n";
			}

			// Textarea
			if ( textarea ) {
				textarea.value += message+"\n-----\n";
				textarea.scrollTop = textarea.scrollHeight - textarea.clientHeight;
			}
			// No Textarea, No Console
			else if ( !consoleExists ) {
				alert(message);
			}

			// Return true
			return true;
		};

		// ----------------------------------------------------------------------
		// Emulated Status

		/**
										 * History.getInternetExplorerMajorVersion()
										 * Get's the major version of Internet Explorer
										 * @return {integer}
										 * @license Public Domain
										 * @author Benjamin Arthur Lupton <contact@balupton.com>
										 * @author James Padolsey <https://gist.github.com/527683>
										 */
		History.getInternetExplorerMajorVersion = function(){
			var result = History.getInternetExplorerMajorVersion.cached =
			(typeof History.getInternetExplorerMajorVersion.cached !== 'undefined')
			?	History.getInternetExplorerMajorVersion.cached
			:	(function(){
				var v = 3,
				div = document.createElement('div'),
				all = div.getElementsByTagName('i');
				while ( (div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->') && all[0] ) {}
				return (v > 4) ? v : false;
			})()
			;
			return result;
		};

		/**
										 * History.isInternetExplorer()
										 * Are we using Internet Explorer?
										 * @return {boolean}
										 * @license Public Domain
										 * @author Benjamin Arthur Lupton <contact@balupton.com>
										 */
		History.isInternetExplorer = function(){
			var result =
			History.isInternetExplorer.cached =
			(typeof History.isInternetExplorer.cached !== 'undefined')
			?	History.isInternetExplorer.cached
			:	Boolean(History.getInternetExplorerMajorVersion())
			;
			return result;
		};

		/**
										 * History.emulated
										 * Which features require emulating?
										 */
		History.emulated = {
			pushState: !Boolean(
				window.history && window.history.pushState && window.history.replaceState
				&& !(
					(/ Mobile\/([1-7][a-z]|(8([abcde]|f(1[0-8]))))/i).test(navigator.userAgent) /* disable for versions of iOS before version 4.3 (8F190) */
					|| (/AppleWebKit\/5([0-2]|3[0-2])/i).test(navigator.userAgent) /* disable for the mercury iOS browser, or at least older versions of the webkit engine */
					)
				),
			hashChange: Boolean(
				!(('onhashchange' in window) || ('onhashchange' in document))
				||
				(History.isInternetExplorer() && History.getInternetExplorerMajorVersion() < 8)
				)
		};

		/**
										 * History.enabled
										 * Is History enabled?
										 */
		History.enabled = !History.emulated.pushState;

		/**
										 * History.bugs
										 * Which bugs are present
										 */
		History.bugs = {
			/**
											 * Safari 5 and Safari iOS 4 fail to return to the correct state once a hash is replaced by a `replaceState` call
											 * https://bugs.webkit.org/show_bug.cgi?id=56249
											 */
			setHash: Boolean(!History.emulated.pushState && navigator.vendor === 'Apple Computer, Inc.' && /AppleWebKit\/5([0-2]|3[0-3])/.test(navigator.userAgent)),

			/**
											 * Safari 5 and Safari iOS 4 sometimes fail to apply the state change under busy conditions
											 * https://bugs.webkit.org/show_bug.cgi?id=42940
											 */
			safariPoll: Boolean(!History.emulated.pushState && navigator.vendor === 'Apple Computer, Inc.' && /AppleWebKit\/5([0-2]|3[0-3])/.test(navigator.userAgent)),

			/**
											 * MSIE 6 and 7 sometimes do not apply a hash even it was told to (requiring a second call to the apply function)
											 */
			ieDoubleCheck: Boolean(History.isInternetExplorer() && History.getInternetExplorerMajorVersion() < 8),

			/**
											 * MSIE 6 requires the entire hash to be encoded for the hashes to trigger the onHashChange event
											 */
			hashEscape: Boolean(History.isInternetExplorer() && History.getInternetExplorerMajorVersion() < 7)
		};

		/**
										 * History.isEmptyObject(obj)
										 * Checks to see if the Object is Empty
										 * @param {Object} obj
										 * @return {boolean}
										 */
		History.isEmptyObject = function(obj) {
			for ( var name in obj ) {
				return false;
			}
			return true;
		};

		/**
										 * History.cloneObject(obj)
										 * Clones a object
										 * @param {Object} obj
										 * @return {Object}
										 */
		History.cloneObject = function(obj) {
			var hash,newObj;
			if ( obj ) {
				hash = JSON.stringify(obj);
				newObj = JSON.parse(hash);
			}
			else {
				newObj = {};
			}
			return newObj;
		};

		// ----------------------------------------------------------------------
		// URL Helpers

		/**
										 * History.getRootUrl()
										 * Turns "http://mysite.com/dir/page.html?asd" into "http://mysite.com"
										 * @return {String} rootUrl
										 */
		History.getRootUrl = function(){
			// Create
			var rootUrl = document.location.protocol+'//'+(document.location.hostname||document.location.host);
			if ( document.location.port||false ) {
				rootUrl += ':'+document.location.port;
			}
			rootUrl += '/';

			// Return
			return rootUrl;
		};

		/**
										 * History.getBaseHref()
										 * Fetches the `href` attribute of the `<base href="...">` element if it exists
										 * @return {String} baseHref
										 */
		History.getBaseHref = function(){
			// Create
			var
			baseElements = document.getElementsByTagName('base'),
			baseElement = null,
			baseHref = '';

			// Test for Base Element
			if ( baseElements.length === 1 ) {
				// Prepare for Base Element
				baseElement = baseElements[0];
				baseHref = baseElement.href.replace(/[^\/]+$/,'');
			}

			// Adjust trailing slash
			baseHref = baseHref.replace(/\/+$/,'');
			if ( baseHref ) baseHref += '/';

			// Return
			return baseHref;
		};

		/**
										 * History.getBaseUrl()
										 * Fetches the baseHref or basePageUrl or rootUrl (whichever one exists first)
										 * @return {String} baseUrl
										 */
		History.getBaseUrl = function(){
			// Create
			var baseUrl = History.getBaseHref()||History.getBasePageUrl()||History.getRootUrl();

			// Return
			return baseUrl;
		};

		/**
										 * History.getPageUrl()
										 * Fetches the URL of the current page
										 * @return {String} pageUrl
										 */
		History.getPageUrl = function(){
			// Fetch
			var
			State = History.getState(false,false),
			stateUrl = (State||{}).url||document.location.href;

			// Create
			var pageUrl = stateUrl.replace(/\/+$/,'').replace(/[^\/]+$/,function(part,index,string){
				return (/\./).test(part) ? part : part+'/';
			});

			// Return
			return pageUrl;
		};

		/**
										 * History.getBasePageUrl()
										 * Fetches the Url of the directory of the current page
										 * @return {String} basePageUrl
										 */
		History.getBasePageUrl = function(){
			// Create
			var basePageUrl = document.location.href.replace(/[#\?].*/,'').replace(/[^\/]+$/,function(part,index,string){
				return (/[^\/]$/).test(part) ? '' : part;
			}).replace(/\/+$/,'')+'/';

			// Return
			return basePageUrl;
		};

		/**
										 * History.getFullUrl(url)
										 * Ensures that we have an absolute URL and not a relative URL
										 * @param {string} url
										 * @param {Boolean} allowBaseHref
										 * @return {string} fullUrl
										 */
		History.getFullUrl = function(url,allowBaseHref){
			// Prepare
			var fullUrl = url, firstChar = url.substring(0,1);
			allowBaseHref = (typeof allowBaseHref === 'undefined') ? true : allowBaseHref;

			// Check
			if ( /[a-z]+\:\/\//.test(url) ) {
			// Full URL
			}
			else if ( firstChar === '/' ) {
				// Root URL
				fullUrl = History.getRootUrl()+url.replace(/^\/+/,'');
			}
			else if ( firstChar === '#' ) {
				// Anchor URL
				fullUrl = History.getPageUrl().replace(/#.*/,'')+url;
			}
			else if ( firstChar === '?' ) {
				// Query URL
				fullUrl = History.getPageUrl().replace(/[\?#].*/,'')+url;
			}
			else {
				// Relative URL
				if ( allowBaseHref ) {
					fullUrl = History.getBaseUrl()+url.replace(/^(\.\/)+/,'');
				} else {
					fullUrl = History.getBasePageUrl()+url.replace(/^(\.\/)+/,'');
				}
			// We have an if condition above as we do not want hashes
			// which are relative to the baseHref in our URLs
			// as if the baseHref changes, then all our bookmarks
			// would now point to different locations
			// whereas the basePageUrl will always stay the same
			}

			// Return
			return fullUrl.replace(/\#$/,'');
		};

		/**
										 * History.getShortUrl(url)
										 * Ensures that we have a relative URL and not a absolute URL
										 * @param {string} url
										 * @return {string} url
										 */
		History.getShortUrl = function(url){
			// Prepare
			var shortUrl = url, baseUrl = History.getBaseUrl(), rootUrl = History.getRootUrl();

			// Trim baseUrl
			if ( History.emulated.pushState ) {
				// We are in a if statement as when pushState is not emulated
				// The actual url these short urls are relative to can change
				// So within the same session, we the url may end up somewhere different
				shortUrl = shortUrl.replace(baseUrl,'');
			}

			// Trim rootUrl
			shortUrl = shortUrl.replace(rootUrl,'/');

			// Ensure we can still detect it as a state
			if ( History.isTraditionalAnchor(shortUrl) ) {
				shortUrl = './'+shortUrl;
			}

			// Clean It
			shortUrl = shortUrl.replace(/^(\.\/)+/g,'./').replace(/\#$/,'');

			// Return
			return shortUrl;
		};

		// ----------------------------------------------------------------------
		// State Storage

		/**
										 * History.store
										 * The store for all session specific data
										 */
		History.store = amplify ? (amplify.store('History.store')||{}) : {};
		History.store.idToState = History.store.idToState||{};
		History.store.urlToId = History.store.urlToId||{};
		History.store.stateToId = History.store.stateToId||{};

		/**
										 * History.idToState
										 * 1-1: State ID to State Object
										 */
		History.idToState = History.idToState||{};

		/**
										 * History.stateToId
										 * 1-1: State String to State ID
										 */
		History.stateToId = History.stateToId||{};

		/**
										 * History.urlToId
										 * 1-1: State URL to State ID
										 */
		History.urlToId = History.urlToId||{};

		/**
										 * History.storedStates
										 * Store the states in an array
										 */
		History.storedStates = History.storedStates||[];

		/**
										 * History.savedStates
										 * Saved the states in an array
										 */
		History.savedStates = History.savedStates||[];

		/**
										 * History.getState()
										 * Get an object containing the data, title and url of the current state
										 * @param {Boolean} friendly
										 * @param {Boolean} create
										 * @return {Object} State
										 */
		History.getState = function(friendly,create){
			// Prepare
			if ( typeof friendly === 'undefined' ) {
				friendly = true;
			}
			if ( typeof create === 'undefined' ) {
				create = true;
			}

			// Fetch
			var State = History.getLastSavedState();

			// Create
			if ( !State && create ) {
				State = History.createStateObject();
			}

			// Adjust
			if ( friendly ) {
				State = History.cloneObject(State);
				State.url = State.cleanUrl||State.url;
			}

			// Return
			return State;
		};

		/**
										 * History.getIdByState(State)
										 * Gets a ID for a State
										 * @param {State} newState
										 * @return {String} id
										 */
		History.getIdByState = function(newState){

			// Fetch ID
			var id = History.extractId(newState.url);
			if ( !id ) {
				// Find ID via State String
				var str = History.getStateString(newState);
				if ( typeof History.stateToId[str] !== 'undefined' ) {
					id = History.stateToId[str];
				}
				else if ( typeof History.store.stateToId[str] !== 'undefined' ) {
					id = History.store.stateToId[str];
				}
				else {
					// Generate a new ID
					/**
													 *	Added a counter not to create infinite loops...
													 *	@author Woxxy
													 */
					var counter = 0;
					while ( true  && counter < 20) {
						id = String(Math.floor(Math.random()*1000));
						if ( typeof History.idToState[id] === 'undefined' && typeof History.store.idToState[id] === 'undefined' ) {
							break;
						}
						counter++;
					}

					// Apply the new State to the ID
					History.stateToId[str] = id;
					History.idToState[id] = newState;
				}
			}

			// Return ID
			return id;
		};

		/**
										 * History.normalizeState(State)
										 * Expands a State Object
										 * @param {object} State
										 * @return {object}
										 */
		History.normalizeState = function(oldState){
			// Prepare
			if ( !oldState || (typeof oldState !== 'object') ) {
				oldState = {};
			}

			// Check
			if ( typeof oldState.normalized !== 'undefined' ) {
				return oldState;
			}

			// Adjust
			if ( !oldState.data || (typeof oldState.data !== 'object') ) {
				oldState.data = {};
			}

			// ----------------------------------------------------------------------

			// Create
			var newState = {};
			newState.normalized = true;
			newState.title = oldState.title||'';
			newState.url = History.getFullUrl(History.unescapeString(oldState.url||document.location.href));
			newState.hash = History.getShortUrl(newState.url);
			newState.data = History.cloneObject(oldState.data);

			// Fetch ID
			newState.id = History.getIdByState(newState);

			// ----------------------------------------------------------------------

			// Clean the URL
			newState.cleanUrl = newState.url.replace(/\??\&_suid.*/,'');
			newState.url = newState.cleanUrl;

			// Check to see if we have more than just a url
			var dataNotEmpty = !History.isEmptyObject(newState.data);

			// Apply
			if ( newState.title || dataNotEmpty ) {
				// Add ID to Hash
				newState.hash = History.getShortUrl(newState.url).replace(/\??\&_suid.*/,'');
				if ( !/\?/.test(newState.hash) ) {
					newState.hash += '?';
				}
				newState.hash += '&_suid='+newState.id;
			}

			// Create the Hashed URL
			newState.hashedUrl = History.getFullUrl(newState.hash);

			// ----------------------------------------------------------------------

			// Update the URL if we have a duplicate
			if ( (History.emulated.pushState || History.bugs.safariPoll) && History.hasUrlDuplicate(newState) ) {
				newState.url = newState.hashedUrl;
			}

			// ----------------------------------------------------------------------

			// Return
			return newState;
		};

		/**
										 * History.createStateObject(data,title,url)
										 * Creates a object based on the data, title and url state params
										 * @param {object} data
										 * @param {string} title
										 * @param {string} url
										 * @return {object}
										 */
		History.createStateObject = function(data,title,url){
			// Hashify
			var State = {
				'data': data,
				'title': title,
				'url': url
			};

			// Expand the State
			State = History.normalizeState(State);

			// Return object
			return State;
		};

		/**
										 * History.getStateById(id)
										 * Get a state by it's UID
										 * @param {String} id
										 */
		History.getStateById = function(id){
			// Prepare
			id = String(id);

			// Retrieve
			var State = History.idToState[id] || History.store.idToState[id] || undefined;

			// Return State
			return State;
		};

		/**
										 * Get a State's String
										 * @param {State} passedState
										 */
		History.getStateString = function(passedState){
			// Prepare
			var State = History.normalizeState(passedState);

			// Clean
			var cleanedState = {
				data: State.data,
				title: passedState.title,
				url: passedState.url
			};

			// Fetch
			var str = JSON.stringify(cleanedState);

			// Return
			return str;
		};

		/**
										 * Get a State's ID
										 * @param {State} passedState
										 * @return {String} id
										 */
		History.getStateId = function(passedState){
			// Prepare
			var State = History.normalizeState(passedState);

			// Fetch
			var id = State.id;

			// Return
			return id;
		};

		/**
										 * History.getHashByState(State)
										 * Creates a Hash for the State Object
										 * @param {State} passedState
										 * @return {String} hash
										 */
		History.getHashByState = function(passedState){
			// Prepare
			var hash, State = History.normalizeState(passedState);

			// Fetch
			hash = State.hash;

			// Return
			return hash;
		};

		/**
										 * History.extractId(url_or_hash)
										 * Get a State ID by it's URL or Hash
										 * @param {string} url_or_hash
										 * @return {string} id
										 */
		History.extractId = function ( url_or_hash ) {
			// Prepare
			var id;

			// Extract
			var parts,url;
			parts = /(.*)\&_suid=([0-9]+)$/.exec(url_or_hash);
			url = parts ? (parts[1]||url_or_hash) : url_or_hash;
			id = parts ? String(parts[2]||'') : '';

			// Return
			return id||false;
		};

		/**
										 * History.isTraditionalAnchor
										 * Checks to see if the url is a traditional anchor or not
										 * @param {String} url_or_hash
										 * @return {Boolean}
										 */
		History.isTraditionalAnchor = function(url_or_hash){
			// Check
			var isTraditional = !(/[\/\?\.]/.test(url_or_hash));

			// Return
			return isTraditional;
		};

		/**
										 * History.extractState
										 * Get a State by it's URL or Hash
										 * @param {String} url_or_hash
										 * @return {State|null}
										 */
		History.extractState = function(url_or_hash,create){
			// Prepare
			var State = null;
			create = create||false;

			// Fetch SUID
			var id = History.extractId(url_or_hash);
			if ( id ) {
				State = History.getStateById(id);
			}

			// Fetch SUID returned no State
			if ( !State ) {
				// Fetch URL
				var url = History.getFullUrl(url_or_hash);

				// Check URL
				id = History.getIdByUrl(url)||false;
				if ( id ) {
					State = History.getStateById(id);
				}

				// Create State
				if ( !State && create && !History.isTraditionalAnchor(url_or_hash) ) {
					State = History.createStateObject(null,null,url);
				}
			}

			// Return
			return State;
		};

		/**
										 * History.getIdByUrl()
										 * Get a State ID by a State URL
										 */
		History.getIdByUrl = function(url){
			// Fetch
			var id = History.urlToId[url] || History.store.urlToId[url] || undefined;

			// Return
			return id;
		};

		/**
										 * History.getLastSavedState()
										 * Get an object containing the data, title and url of the current state
										 * @return {Object} State
										 */
		History.getLastSavedState = function(){
			return History.savedStates[History.savedStates.length-1]||undefined;
		};

		/**
										 * History.getLastStoredState()
										 * Get an object containing the data, title and url of the current state
										 * @return {Object} State
										 */
		History.getLastStoredState = function(){
			return History.storedStates[History.storedStates.length-1]||undefined;
		};

		/**
										 * History.hasUrlDuplicate
										 * Checks if a Url will have a url conflict
										 * @param {Object} newState
										 * @return {Boolean} hasDuplicate
										 */
		History.hasUrlDuplicate = function(newState) {
			// Prepare
			var hasDuplicate = false;

			// Fetch
			var oldState = History.extractState(newState.url);

			// Check
			hasDuplicate = oldState && oldState.id !== newState.id;

			// Return
			return hasDuplicate;
		};

		/**
										 * History.storeState
										 * Store a State
										 * @param {Object} newState
										 * @return {Object} newState
										 */
		History.storeState = function(newState){
			// Store the State
			History.urlToId[newState.url] = newState.id;

			// Push the State
			History.storedStates.push(History.cloneObject(newState));

			// Return newState
			return newState;
		};

		/**
										 * History.isLastSavedState(newState)
										 * Tests to see if the state is the last state
										 * @param {Object} newState
										 * @return {boolean} isLast
										 */
		History.isLastSavedState = function(newState){
			// Prepare
			var isLast = false;

			// Check
			if ( History.savedStates.length ) {
				var
				newId = newState.id,
				oldState = History.getLastSavedState(),
				oldId = oldState.id;

				// Check
				isLast = (newId === oldId);
			}

			// Return
			return isLast;
		};

		/**
										 * History.saveState
										 * Push a State
										 * @param {Object} newState
										 * @return {boolean} changed
										 */
		History.saveState = function(newState){
			// Check Hash
			if ( History.isLastSavedState(newState) ) {
				return false;
			}

			// Push the State
			History.savedStates.push(History.cloneObject(newState));

			// Return true
			return true;
		};

		/**
										 * History.getStateByIndex()
										 * Gets a state by the index
										 * @param {integer} index
										 * @return {Object}
										 */
		History.getStateByIndex = function(index){
			// Prepare
			var State = null;

			// Handle
			if ( typeof index === 'undefined' ) {
				// Get the last inserted
				State = History.savedStates[History.savedStates.length-1];
			}
			else if ( index < 0 ) {
				// Get from the end
				State = History.savedStates[History.savedStates.length+index];
			}
			else {
				// Get from the beginning
				State = History.savedStates[index];
			}

			// Return State
			return State;
		};

		// ----------------------------------------------------------------------
		// Hash Helpers

		/**
										 * History.getHash()
										 * Gets the current document hash
										 * @return {string}
										 */
		History.getHash = function(){
			var hash = History.unescapeHash(document.location.hash);
			return hash;
		};

		/**
										 * History.unescapeString()
										 * Unescape a string
										 * @param {String} str
										 * @return {string}
										 */
		History.unescapeString = function(str){
			// Prepare
			var result = str;

			// Unescape hash
			var tmp;
			while ( true ) {
				tmp = window.unescape(result);
				if ( tmp === result ) {
					break;
				}
				result = tmp;
			}

			// Return result
			return result;
		};

		/**
										 * History.unescapeHash()
										 * normalize and Unescape a Hash
										 * @param {String} hash
										 * @return {string}
										 */
		History.unescapeHash = function(hash){
			// Prepare
			var result = History.normalizeHash(hash);

			// Unescape hash
			result = History.unescapeString(result);

			// Return result
			return result;
		};

		/**
										 * History.normalizeHash()
										 * normalize a hash across browsers
										 * @return {string}
										 */
		History.normalizeHash = function(hash){
			var result = hash.replace(/[^#]*#/,'').replace(/#.*/, '');

			// Return result
			return result;
		};

		/**
										 * History.setHash(hash)
										 * Sets the document hash
										 * @param {string} hash
										 * @return {History}
										 */
		History.setHash = function(hash,queue){
			// Handle Queueing
			if ( queue !== false && History.busy() ) {
				// Wait + Push to Queue
				//History.debug('History.setHash: we must wait', arguments);
				History.pushQueue({
					scope: History,
					callback: History.setHash,
					args: arguments,
					queue: queue
				});
				return false;
			}

			// Log
			//History.debug('History.setHash: called',hash);

			// Prepare
			var adjustedHash = History.escapeHash(hash);

			// Make Busy + Continue
			History.busy(true);

			// Check if hash is a state
			var State = History.extractState(hash,true);
			if ( State && !History.emulated.pushState ) {
				// Hash is a state so skip the setHash
				//History.debug('History.setHash: Hash is a state so skipping the hash set with a direct pushState call',arguments);

				// PushState
				History.pushState(State.data,State.title,State.url,false);
			}
			else if ( document.location.hash !== adjustedHash ) {
				// Hash is a proper hash, so apply it

				// Handle browser bugs
				if ( History.bugs.setHash ) {
					// Fix Safari Bug https://bugs.webkit.org/show_bug.cgi?id=56249

					// Fetch the base page
					var pageUrl = History.getPageUrl();

					// Safari hash apply
					History.pushState(null,null,pageUrl+'#'+adjustedHash,false);
				}
				else {
					// Normal hash apply
					document.location.hash = adjustedHash;
				}
			}

			// Chain
			return History;
		};

		/**
										 * History.escape()
										 * normalize and Escape a Hash
										 * @return {string}
										 */
		History.escapeHash = function(hash){
			var result = History.normalizeHash(hash);

			// Escape hash
			result = window.escape(result);

			// IE6 Escape Bug
			if ( !History.bugs.hashEscape ) {
				// Restore common parts
				result = result
				.replace(/\%21/g,'!')
				.replace(/\%26/g,'&')
				.replace(/\%3D/g,'=')
				.replace(/\%3F/g,'?');
			}

			// Return result
			return result;
		};

		/**
										 * History.getHashByUrl(url)
										 * Extracts the Hash from a URL
										 * @param {string} url
										 * @return {string} url
										 */
		History.getHashByUrl = function(url){
			// Extract the hash
			var hash = String(url)
			.replace(/([^#]*)#?([^#]*)#?(.*)/, '$2')
			;

			// Unescape hash
			hash = History.unescapeHash(hash);

			// Return hash
			return hash;
		};

		/**
										 * History.setTitle(title)
										 * Applies the title to the document
										 * @param {State} newState
										 * @return {Boolean}
										 */
		History.setTitle = function(newState){
			// Prepare
			var title = newState.title;

			// Initial
			if ( !title ) {
				var firstState = History.getStateByIndex(0);
				if ( firstState && firstState.url === newState.url ) {
					title = firstState.title||History.options.initialTitle;
				}
			}

			// Apply
			try {
				document.getElementsByTagName('title')[0].innerHTML = title.replace('<','&lt;').replace('>','&gt;').replace(' & ',' &amp; ');
			}
			catch ( Exception ) { }
			document.title = title;

			// Chain
			return History;
		};

		// ----------------------------------------------------------------------
		// Queueing

		/**
										 * History.queues
										 * The list of queues to use
										 * First In, First Out
										 */
		History.queues = [];

		/**
										 * History.busy(value)
										 * @param {boolean} value [optional]
										 * @return {boolean} busy
										 */
		History.busy = function(value){
			// Apply
			if ( typeof value !== 'undefined' ) {
				//History.debug('History.busy: changing ['+(History.busy.flag||false)+'] to ['+(value||false)+']', History.queues.length);
				History.busy.flag = value;
			}
			// Default
			else if ( typeof History.busy.flag === 'undefined' ) {
				History.busy.flag = false;
			}

			// Queue
			if ( !History.busy.flag ) {
				// Execute the next item in the queue
				clearTimeout(History.busy.timeout);
				var fireNext = function(){
					if ( History.busy.flag ) return;
					for ( var i=History.queues.length-1; i >= 0; --i ) {
						var queue = History.queues[i];
						if ( queue.length === 0 ) continue;
						var item = queue.shift();
						History.fireQueueItem(item);
						History.busy.timeout = setTimeout(fireNext,History.options.busyDelay);
					}
				};
				History.busy.timeout = setTimeout(fireNext,History.options.busyDelay);
			}

			// Return
			return History.busy.flag;
		};

		/**
										 * History.fireQueueItem(item)
										 * Fire a Queue Item
										 * @param {Object} item
										 * @return {Mixed} result
										 */
		History.fireQueueItem = function(item){
			return item.callback.apply(item.scope||History,item.args||[]);
		};

		/**
										 * History.pushQueue(callback,args)
										 * Add an item to the queue
										 * @param {Object} item [scope,callback,args,queue]
										 */
		History.pushQueue = function(item){
			// Prepare the queue
			History.queues[item.queue||0] = History.queues[item.queue||0]||[];

			// Add to the queue
			History.queues[item.queue||0].push(item);

			// Chain
			return History;
		};

		/**
										 * History.queue (item,queue), (func,queue), (func), (item)
										 * Either firs the item now if not busy, or adds it to the queue
										 */
		History.queue = function(item,queue){
			// Prepare
			if ( typeof item === 'function' ) {
				item = {
					callback: item
				};
			}
			if ( typeof queue !== 'undefined' ) {
				item.queue = queue;
			}

			// Handle
			if ( History.busy() ) {
				History.pushQueue(item);
			} else {
				History.fireQueueItem(item);
			}

			// Chain
			return History;
		};

		/**
										 * History.clearQueue()
										 * Clears the Queue
										 */
		History.clearQueue = function(){
			History.busy.flag = false;
			History.queues = [];
			return History;
		};


		// ----------------------------------------------------------------------
		// IE Bug Fix

		/**
										 * History.stateChanged
										 * States whether or not the state has changed since the last double check was initialised
										 */
		History.stateChanged = false;

		/**
										 * History.doubleChecker
										 * Contains the timeout used for the double checks
										 */
		History.doubleChecker = false;

		/**
										 * History.doubleCheckComplete()
										 * Complete a double check
										 * @return {History}
										 */
		History.doubleCheckComplete = function(){
			// Update
			History.stateChanged = true;

			// Clear
			History.doubleCheckClear();

			// Chain
			return History;
		};

		/**
										 * History.doubleCheckClear()
										 * Clear a double check
										 * @return {History}
										 */
		History.doubleCheckClear = function(){
			// Clear
			if ( History.doubleChecker ) {
				clearTimeout(History.doubleChecker);
				History.doubleChecker = false;
			}

			// Chain
			return History;
		};

		/**
										 * History.doubleCheck()
										 * Create a double check
										 * @return {History}
										 */
		History.doubleCheck = function(tryAgain){
			// Reset
			History.stateChanged = false;
			History.doubleCheckClear();

			// Fix IE6,IE7 bug where calling history.back or history.forward does not actually change the hash (whereas doing it manually does)
			// Fix Safari 5 bug where sometimes the state does not change: https://bugs.webkit.org/show_bug.cgi?id=42940
			if ( History.bugs.ieDoubleCheck ) {
				// Apply Check
				History.doubleChecker = setTimeout(
					function(){
						History.doubleCheckClear();
						if ( !History.stateChanged ) {
							//History.debug('History.doubleCheck: State has not yet changed, trying again', arguments);
							// Re-Attempt
							tryAgain();
						}
						return true;
					},
					History.options.doubleCheckInterval
					);
			}

			// Chain
			return History;
		};

		// ----------------------------------------------------------------------
		// Safari Bug Fix

		/**
										 * History.safariStatePoll()
										 * Poll the current state
										 * @return {History}
										 */
		History.safariStatePoll = function(){
			// Poll the URL

			// Get the Last State which has the new URL
			var
			urlState = History.extractState(document.location.href),
			newState;

			// Check for a difference
			if ( !History.isLastSavedState(urlState) ) {
				newState = urlState;
			}
			else {
				return;
			}

			// Check if we have a state with that url
			// If not create it
			if ( !newState ) {
				//History.debug('History.safariStatePoll: new');
				newState = History.createStateObject();
			}

			// Apply the New State
			//History.debug('History.safariStatePoll: trigger');
			History.Adapter.trigger(window,'popstate');

			// Chain
			return History;
		};

		// ----------------------------------------------------------------------
		// State Aliases

		/**
										 * History.back(queue)
										 * Send the browser history back one item
										 * @param {Integer} queue [optional]
										 */
		History.back = function(queue){
			//History.debug('History.back: called', arguments);

			// Handle Queueing
			if ( queue !== false && History.busy() ) {
				// Wait + Push to Queue
				//History.debug('History.back: we must wait', arguments);
				History.pushQueue({
					scope: History,
					callback: History.back,
					args: arguments,
					queue: queue
				});
				return false;
			}

			// Make Busy + Continue
			History.busy(true);

			// Fix certain browser bugs that prevent the state from changing
			History.doubleCheck(function(){
				History.back(false);
			});

			// Go back
			history.go(-1);

			// End back closure
			return true;
		};

		/**
										 * History.forward(queue)
										 * Send the browser history forward one item
										 * @param {Integer} queue [optional]
										 */
		History.forward = function(queue){
			//History.debug('History.forward: called', arguments);

			// Handle Queueing
			if ( queue !== false && History.busy() ) {
				// Wait + Push to Queue
				//History.debug('History.forward: we must wait', arguments);
				History.pushQueue({
					scope: History,
					callback: History.forward,
					args: arguments,
					queue: queue
				});
				return false;
			}

			// Make Busy + Continue
			History.busy(true);

			// Fix certain browser bugs that prevent the state from changing
			History.doubleCheck(function(){
				History.forward(false);
			});

			// Go forward
			history.go(1);

			// End forward closure
			return true;
		};

		/**
										 * History.go(index,queue)
										 * Send the browser history back or forward index times
										 * @param {Integer} queue [optional]
										 */
		History.go = function(index,queue){
			//History.debug('History.go: called', arguments);

			// Prepare
			var i;

			// Handle
			if ( index > 0 ) {
				// Forward
				for ( i=1; i<=index; ++i ) {
					History.forward(queue);
				}
			}
			else if ( index < 0 ) {
				// Backward
				for ( i=-1; i>=index; --i ) {
					History.back(queue);
				}
			}
			else {
				throw new Error('History.go: History.go requires a positive or negative integer passed.');
			}

			// Chain
			return History;
		};


		// ----------------------------------------------------------------------
		// Initialise

		/**
										 * Create the initial State
										 */
		History.saveState(History.storeState(History.extractState(document.location.href,true)));

		/**
										 * Bind for Saving Store
										 */
		if ( amplify ) {
			History.onUnload = function(){
				// Prepare
				var
				currentStore = amplify.store('History.store')||{},
				item;

				// Ensure
				currentStore.idToState = currentStore.idToState || {};
				currentStore.urlToId = currentStore.urlToId || {};
				currentStore.stateToId = currentStore.stateToId || {};

				// Sync
				for ( item in History.idToState ) {
					if ( !History.idToState.hasOwnProperty(item) ) {
						continue;
					}
					currentStore.idToState[item] = History.idToState[item];
				}
				for ( item in History.urlToId ) {
					if ( !History.urlToId.hasOwnProperty(item) ) {
						continue;
					}
					currentStore.urlToId[item] = History.urlToId[item];
				}
				for ( item in History.stateToId ) {
					if ( !History.stateToId.hasOwnProperty(item) ) {
						continue;
					}
					currentStore.stateToId[item] = History.stateToId[item];
				}

				// Update
				History.store = currentStore;

				// Store
				amplify.store('History.store',currentStore);
			};
			// For Internet Explorer
			History.intervalList.push(setInterval(History.onUnload,History.options.storeInterval));
			// For Other Browsers
			History.Adapter.bind(window,'beforeunload',History.onUnload);
			History.Adapter.bind(window,'unload',History.onUnload);
		// Both are enabled for consistency
		}


		// ----------------------------------------------------------------------
		// HTML5 State Support

		if ( History.emulated.pushState ) {
			/*
											 * Provide Skeleton for HTML4 Browsers
											 */

			// Prepare
			var emptyFunction = function(){};
			History.pushState = History.pushState||emptyFunction;
			History.replaceState = History.replaceState||emptyFunction;
		}
		else {
			/*
											 * Use native HTML5 History API Implementation
											 */

			/**
											 * History.onPopState(event,extra)
											 * Refresh the Current State
											 */
			History.onPopState = function(event){
				// Reset the double check
				History.doubleCheckComplete();

				// Check for a Hash, and handle apporiatly
				var currentHash	= History.getHash();
				if ( currentHash ) {
					// Expand Hash
					var currentState = History.extractState(currentHash||document.location.href,true);
					if ( currentState ) {
						// We were able to parse it, it must be a State!
						// Let's forward to replaceState
						//History.debug('History.onPopState: state anchor', currentHash, currentState);
						History.replaceState(currentState.data, currentState.title, currentState.url, false);
					}
					else {
						// Traditional Anchor
						//History.debug('History.onPopState: traditional anchor', currentHash);
						History.Adapter.trigger(window,'anchorchange');
						History.busy(false);
					}

					// We don't care for hashes
					History.expectedStateId = false;
					return false;
				}

				// Prepare
				var newState = false;

				// Prepare
				event = event||{};
				if ( typeof event.state === 'undefined' ) {
					// jQuery
					if ( typeof event.originalEvent !== 'undefined' && typeof event.originalEvent.state !== 'undefined' ) {
						event.state = event.originalEvent.state||false;
					}
					// MooTools
					else if ( typeof event.event !== 'undefined' && typeof event.event.state !== 'undefined' ) {
						event.state = event.event.state||false;
					}
				}

				// Ensure
				event.state = (event.state||false);

				// Fetch State
				if ( event.state ) {
					// Vanilla: Back/forward button was used
					newState = History.getStateById(event.state);
				}
				else if ( History.expectedStateId ) {
					// Vanilla: A new state was pushed, and popstate was called manually
					newState = History.getStateById(History.expectedStateId);
				}
				else {
					// Initial State
					newState = History.extractState(document.location.href);
				}

				// The State did not exist in our store
				if ( !newState ) {
					// Regenerate the State
					newState = History.createStateObject(null,null,document.location.href);
				}

				// Clean
				History.expectedStateId = false;

				// Check if we are the same state
				if ( History.isLastSavedState(newState) ) {
					// There has been no change (just the page's hash has finally propagated)
					//History.debug('History.onPopState: no change', newState, History.savedStates);
					History.busy(false);
					return false;
				}

				// Store the State
				History.storeState(newState);
				History.saveState(newState);

				// Force update of the title
				History.setTitle(newState);

				// Fire Our Event
				History.Adapter.trigger(window,'statechange');
				History.busy(false);

				// Return true
				return true;
			};
			History.Adapter.bind(window,'popstate',History.onPopState);

			/**
											 * History.pushState(data,title,url)
											 * Add a new State to the history object, become it, and trigger onpopstate
											 * We have to trigger for HTML4 compatibility
											 * @param {object} data
											 * @param {string} title
											 * @param {string} url
											 * @return {true}
											 */
			History.pushState = function(data,title,url,queue){
				//History.debug('History.pushState: called', arguments);

				// Check the State
				if ( History.getHashByUrl(url) && History.emulated.pushState ) {
					throw new Error('History.js does not support states with fragement-identifiers (hashes/anchors).');
				}

				// Handle Queueing
				if ( queue !== false && History.busy() ) {
					// Wait + Push to Queue
					//History.debug('History.pushState: we must wait', arguments);
					History.pushQueue({
						scope: History,
						callback: History.pushState,
						args: arguments,
						queue: queue
					});
					return false;
				}

				// Make Busy + Continue
				History.busy(true);

				// Create the newState
				var newState = History.createStateObject(data,title,url);

				// Check it
				if ( History.isLastSavedState(newState) ) {
					// Won't be a change
					History.busy(false);
				}
				else {
					// Store the newState
					History.storeState(newState);
					History.expectedStateId = newState.id;

					// Push the newState
					history.pushState(newState.id,newState.title,newState.url);

					// Fire HTML5 Event
					History.Adapter.trigger(window,'popstate');
				}

				// End pushState closure
				return true;
			};

			/**
											 * History.replaceState(data,title,url)
											 * Replace the State and trigger onpopstate
											 * We have to trigger for HTML4 compatibility
											 * @param {object} data
											 * @param {string} title
											 * @param {string} url
											 * @return {true}
											 */
			History.replaceState = function(data,title,url,queue){
				//History.debug('History.replaceState: called', arguments);

				// Check the State
				if ( History.getHashByUrl(url) && History.emulated.pushState ) {
					throw new Error('History.js does not support states with fragement-identifiers (hashes/anchors).');
				}

				// Handle Queueing
				if ( queue !== false && History.busy() ) {
					// Wait + Push to Queue
					//History.debug('History.replaceState: we must wait', arguments);
					History.pushQueue({
						scope: History,
						callback: History.replaceState,
						args: arguments,
						queue: queue
					});
					return false;
				}

				// Make Busy + Continue
				History.busy(true);

				// Create the newState
				var newState = History.createStateObject(data,title,url);

				// Check it
				if ( History.isLastSavedState(newState) ) {
					// Won't be a change
					History.busy(false);
				}
				else {
					// Store the newState
					History.storeState(newState);
					History.expectedStateId = newState.id;

					// Push the newState
					history.replaceState(newState.id,newState.title,newState.url);

					// Fire HTML5 Event
					History.Adapter.trigger(window,'popstate');
				}

				// End replaceState closure
				return true;
			};

			// Be aware, the following is only for native pushState implementations
			// If you are wanting to include something for all browsers
			// Then include it above this if block

			/**
											 * Setup Safari Fix
											 */
			if ( History.bugs.safariPoll ) {
				History.intervalList.push(setInterval(History.safariStatePoll, History.options.safariPollInterval));
			}

			/**
											 * Ensure Cross Browser Compatibility
											 */
			if ( navigator.vendor === 'Apple Computer, Inc.' || (navigator.appCodeName||'') === 'Mozilla' ) {
				/**
												 * Fix Safari HashChange Issue
												 */

				// Setup Alias
				History.Adapter.bind(window,'hashchange',function(){
					History.Adapter.trigger(window,'popstate');
				});

				// Initialise Alias
				if ( History.getHash() ) {
					History.Adapter.onDomLoad(function(){
						History.Adapter.trigger(window,'hashchange');
					});
				}
			}

		} // !History.emulated.pushState

	}; // History.initCore

	// Try and Initialise History
	History.init();

})(window);






        /* Simple JavaScript Inheritance
         * By John Resig http://ejohn.org/
         * MIT Licensed.
         */
        // Inspired by base2 and Prototype
        (function(){
          var initializing = false, fnTest = /xyz/.test(function(){xyz;}) ? /\b_super\b/ : /.*/;

          // The base Class implementation (does nothing)
          this.Class = function(){};

          // Create a new Class that inherits from this class
          Class.extend = function(prop) {
            var _super = this.prototype;

            // Instantiate a base class (but only create the instance,
            // don't run the init constructor)
            initializing = true;
            var prototype = new this();
            initializing = false;

            // Copy the properties over onto the new prototype
            for (var name in prop) {
              // Check if we're overwriting an existing function
              prototype[name] = typeof prop[name] == "function" &&
                typeof _super[name] == "function" && fnTest.test(prop[name]) ?
                (function(name, fn){
                  return function() {
                    var tmp = this._super;

                    // Add a new ._super() method that is the same method
                    // but on the super-class
                    this._super = _super[name];

                    // The method only need to be bound temporarily, so we
                    // remove it when we're done executing
                    var ret = fn.apply(this, arguments);
                    this._super = tmp;

                    return ret;
                  };
                })(name, prop[name]) :
                prop[name];
            }

            // The dummy class constructor
            function Class() {
              // All construction is actually done in the init method
              if ( !initializing && this.init )
                this.init.apply(this, arguments);
            }

            // Populate our constructed prototype object
            Class.prototype = prototype;

            // Enforce the constructor to be what we expect
            Class.constructor = Class;

            // And make this class extendable
            Class.extend = arguments.callee;

            return Class;
          };
        })();
		
		
/*
 * Copyright (c) 2010 - The OWASP Foundation
 *
 * The jquery-encoder is published by OWASP under the MIT license. You should read and accept the
 * LICENSE before you use, modify, and/or redistribute this software.
 */

(function($){var default_immune={'js':[',','.','_',' ']};var attr_whitelist_classes={'default':[',','.','-','_',' ']};var attr_whitelist={'width':['%'],'height':['%']};var css_whitelist_classes={'default':['-',' ','%'],'color':['#',' ','(',')'],'image':['(',')',':','/','?','&','-','.','"','=',' ']};var css_whitelist={'background':['(',')',':','%','/','?','&','-',' ','.','"','=','#'],'background-image':css_whitelist_classes['image'],'background-color':css_whitelist_classes['color'],'border-color':css_whitelist_classes['color'],'border-image':css_whitelist_classes['image'],'color':css_whitelist_classes['color'],'icon':css_whitelist_classes['image'],'list-style-image':css_whitelist_classes['image'],'outline-color':css_whitelist_classes['color']};var unsafeKeys={'attr_name':['on[a-z]{1,}','style','href','src'],'attr_val':['javascript:'],'css_key':['behavior','-moz-behavior','-ms-behavior'],'css_val':['expression']};var options={blacklist:true};var hasBeenInitialized=false;$.encoder={author:'Chris Schmidt (chris.schmidt@owasp.org)',version:'${project.version}',init:function(opts){if(hasBeenInitialized)
throw"jQuery Encoder has already been initialized - cannot set options after initialization";hasBeenInitialized=true;$.extend(options,opts);},encodeForHTML:function(input){hasBeenInitialized=true;var div=document.createElement('div');$(div).text(input);return $(div).html();},encodeForHTMLAttribute:function(attr,input,omitAttributeName){hasBeenInitialized=true;attr=$.encoder.canonicalize(attr).toLowerCase();input=$.encoder.canonicalize(input);if($.inArray(attr,unsafeKeys['attr_name'])>=0){throw"Unsafe attribute name used: "+attr;}
for(var a=0;a<unsafeKeys['attr_val'];a++){if(input.toLowerCase().match(unsafeKeys['attr_val'][a])){throw"Unsafe attribute value used: "+input;}}
immune=attr_whitelist[attr];if(!immune)immune=attr_whitelist_classes['default'];var encoded='';if(!omitAttributeName){for(var p=0;p<attr.length;p++){var pc=attr.charAt(p);if(!pc.match(/[a-zA-Z\-0-9]/)){throw"Invalid attribute name specified";}
encoded+=pc;}
encoded+='="';}
for(var i=0;i<input.length;i++){var ch=input.charAt(i),cc=input.charCodeAt(i);if(!ch.match(/[a-zA-Z0-9]/)&&$.inArray(ch,immune)<0){var hex=cc.toString(16);encoded+='&#x'+hex+';';}else{encoded+=ch;}}
if(!omitAttributeName){encoded+='"';}
return encoded;},encodeForCSS:function(propName,input,omitPropertyName){hasBeenInitialized=true;propName=$.encoder.canonicalize(propName).toLowerCase();input=$.encoder.canonicalize(input);if($.inArray(propName,unsafeKeys['css_key'])>=0){throw"Unsafe property name used: "+propName;}
for(var a=0;a<unsafeKeys['css_val'].length;a++){if(input.toLowerCase().indexOf(unsafeKeys['css_val'][a])>=0){throw"Unsafe property value used: "+input;}}
immune=css_whitelist[propName];if(!immune)immune=css_whitelist_classes['default'];var encoded='';if(!omitPropertyName){for(var p=0;p<propName.length;p++){var pc=propName.charAt(p);if(!pc.match(/[a-zA-Z\-]/)){throw"Invalid Property Name specified";}
encoded+=pc;}
encoded+=': ';}
for(var i=0;i<input.length;i++){var ch=input.charAt(i),cc=input.charCodeAt(i);if(!ch.match(/[a-zA-Z0-9]/)&&$.inArray(ch,immune)<0){var hex=cc.toString(16);var pad='000000'.substr((hex.length));encoded+='\\'+pad+hex;}else{encoded+=ch;}}
return encoded;},encodeForURL:function(input,attr){hasBeenInitialized=true;var encoded='';if(attr){if(attr.match(/^[A-Za-z\-0-9]{1,}$/)){encoded+=$.encoder.canonicalize(attr).toLowerCase();}else{throw"Illegal Attribute Name Specified";}
encoded+='="';}
encoded+=encodeURIComponent(input);encoded+=attr?'"':'';return encoded;},encodeForJavascript:function(input){hasBeenInitialized=true;if(!immune)immune=default_immune['js'];var encoded='';for(var i=0;i<input.length;i++){var ch=input.charAt(i),cc=input.charCodeAt(i);if($.inArray(ch,immune)>=0||hex[cc]==null){encoded+=ch;continue;}
var temp=cc.toString(16),pad;if(cc<256){pad='00'.substr(temp.length);encoded+='\\x'+pad+temp.toUpperCase();}else{pad='0000'.substr(temp.length);encoded+='\\u'+pad+temp.toUpperCase();}}
return encoded;},canonicalize:function(input,strict){hasBeenInitialized=true;if(input===null)return null;var out=input,cycle_out=input;var decodeCount=0,cycles=0;var codecs=[new HTMLEntityCodec(),new PercentCodec(),new CSSCodec()];while(true){cycle_out=out;for(var i=0;i<codecs.length;i++){var new_out=codecs[i].decode(out);if(new_out!=out){decodeCount++;out=new_out;}}
if(cycle_out==out){break;}
cycles++;}
if(strict&&decodeCount>1){throw"Attack Detected - Multiple/Double Encodings used in input";}
return out;}};var hex=[];for(var c=0;c<0xFF;c++){if(c>=0x30&&c<=0x39||c>=0x41&&c<=0x5a||c>=0x61&&c<=0x7a){hex[c]=null;}else{hex[c]=c.toString(16);}}
var methods={html:function(opts){return $.encoder.encodeForHTML(opts.unsafe);},css:function(opts){var work=[];var out=[];if(opts.map){work=opts.map;}else{work[opts.name]=opts.unsafe;}
for(var k in work){if(!(typeof work[k]=='function')&&work.hasOwnProperty(k)){out[k]=$.encoder.encodeForCSS(k,work[k],true);}}
return out;},attr:function(opts){var work=[];var out=[];if(opts.map){work=opts.map;}else{work[opts.name]=opts.unsafe;}
for(var k in work){if(!(typeof work[k]=='function')&&work.hasOwnProperty(k)){out[k]=$.encoder.encodeForHTMLAttribute(k,work[k],true);}}
return out;}};$.fn.encode=function(){hasBeenInitialized=true;var argCount=arguments.length;var opts={'context':'html','unsafe':null,'name':null,'map':null,'setter':null,'strict':true};if(argCount==1&&typeof arguments[0]=='object'){$.extend(opts,arguments[0]);}else{opts.context=arguments[0];if(arguments.length==2){if(opts.context=='html'){opts.unsafe=arguments[1];}
else if(opts.content=='attr'||opts.content=='css'){opts.map=arguments[1];}}else{opts.name=arguments[1];opts.unsafe=arguments[2];}}
if(opts.context=='html'){opts.setter=this.html;}
else if(opts.context=='css'){opts.setter=this.css;}
else if(opts.context=='attr'){opts.setter=this.attr;}
return opts.setter.call(this,methods[opts.context].call(this,opts));};var PushbackString=Class.extend({_input:null,_pushback:null,_temp:null,_index:0,_mark:0,_hasNext:function(){if(this._input==null)return false;if(this._input.length==0)return false;return this._index<this._input.length;},init:function(input){this._input=input;},pushback:function(c){this._pushback=c;},index:function(){return this._index;},hasNext:function(){if(this._pushback!=null)return true;return this._hasNext();},next:function(){if(this._pushback!=null){var save=this._pushback;this._pushback=null;return save;}
return(this._hasNext())?this._input.charAt(this._index++):null;},nextHex:function(){var c=this.next();if(c==null)return null;if(c.match(/[0-9A-Fa-f]/))return c;return null;},peek:function(c){if(c){if(this._pushback&&this._pushback==c)return true;return this._hasNext()?this._input.charAt(this._index)==c:false;}
if(this._pushback)return this._pushback;return this._hasNext()?this._input.charAt(this._index):null;},mark:function(){this._temp=this._pushback;this._mark=this._index;},reset:function(){this._pushback=this._temp;this._index=this._mark;},remainder:function(){var out=this._input.substr(this._index);if(this._pushback!=null){out=this._pushback+out;}
return out;}});var Codec=Class.extend({decode:function(input){var out='',pbs=new PushbackString(input);while(pbs.hasNext()){var c=this.decodeCharacter(pbs);if(c!=null){out+=c;}else{out+=pbs.next();}}
return out;},decodeCharacter:function(pbs){return pbs.next();}});var HTMLEntityCodec=Codec.extend({decodeCharacter:function(input){input.mark();var first=input.next();if(first==null||first!='&'){input.reset();return null;}
var second=input.next();if(second==null){input.reset();return null;}
var c;if(second=='#'){c=this._getNumericEntity(input);if(c!=null)return c;}else if(second.match(/[A-Za-z]/)){input.pushback(second);c=this._getNamedEntity(input);if(c!=null)return c;}
input.reset();return null;},_getNamedEntity:function(input){var possible='',entry,len;len=Math.min(input.remainder().length,ENTITY_TO_CHAR_TRIE.getMaxKeyLength());for(var i=0;i<len;i++){possible+=input.next().toLowerCase();}
entry=ENTITY_TO_CHAR_TRIE.getLongestMatch(possible);if(entry==null)
return null;input.reset();input.next();len=entry.getKey().length;for(var j=0;j<len;j++){input.next();}
if(input.peek(';'))
input.next();return entry.getValue();},_getNumericEntity:function(input){var first=input.peek();if(first==null)return null;if(first=='x'||first=='X'){input.next();return this._parseHex(input);}
return this._parseNumber(input);},_parseHex:function(input){var out='';while(input.hasNext()){var c=input.peek();if(!isNaN(parseInt(c,16))){out+=c;input.next();}else if(c==';'){input.next();break;}else{break;}}
var i=parseInt(out,16);if(!isNaN(i)&&isValidCodePoint(i))return String.fromCharCode(i);return null;},_parseNumber:function(input){var out='';while(input.hasNext()){var ch=input.peek();if(!isNaN(parseInt(ch,10))){out+=ch;input.next();}else if(ch==';'){input.next();break;}else{break;}}
var i=parseInt(out,10);if(!isNaN(i)&&isValidCodePoint(i))return String.fromCharCode(i);return null;}});var PercentCodec=Codec.extend({decodeCharacter:function(input){input.mark();var first=input.next();if(first==null){input.reset();return null;}
if(first!='%'){input.reset();return null;}
var out='';for(var i=0;i<2;i++){var c=input.nextHex();if(c!=null)out+=c;}
if(out.length==2){var p=parseInt(out,16);if(isValidCodePoint(p))
return String.fromCharCode(p);}
input.reset();return null;}});var CSSCodec=Codec.extend({decodeCharacter:function(input){input.mark();var first=input.next();if(first==null||first!='\\'){input.reset();return null;}
var second=input.next();if(second==null){input.reset();return null;}
switch(second){case'\r':if(input.peek('\n')){input.next();}
case'\n':case'\f':case'\u0000':return this.decodeCharacter(input);}
if(parseInt(second,16)=='NaN'){return second;}
var out=second;for(var j=0;j<5;j++){var c=input.next();if(c==null||isWhiteSpace(c)){break;}
if(parseInt(c,16)!='NaN'){out+=c;}else{input.pushback(c);break;}}
var p=parseInt(out,16);if(isValidCodePoint(p))
return String.fromCharCode(p);return'\ufffd';}});var Trie=Class.extend({root:null,maxKeyLen:0,size:0,init:function(){this.clear();},getLongestMatch:function(key){return(this.root==null&&key==null)?null:this.root.getLongestMatch(key,0);},getMaxKeyLength:function(){return this.maxKeyLen;},clear:function(){this.root=null,this.maxKeyLen=0,this.size=0;},put:function(key,val){var len,old;if(this.root==null)
this.root=new Trie.Node();if((old=this.root.put(key,0,val))!=null)
return old;if((len=key.length)>this.maxKeyLen)
this.maxKeyLen=key.length;this.size++;return null;}});Trie.Entry=Class.extend({_key:null,_value:null,init:function(key,value){this._key=key,this._value=value;},getKey:function(){return this._key;},getValue:function(){return this._value;},equals:function(other){if(!(other instanceof Trie.Entry)){return false;}
return this._key==other._key&&this._value==other._value;}});Trie.Node=Class.extend({_value:null,_nextMap:null,setValue:function(value){this._value=value;},getNextNode:function(ch){if(!this._nextMap)return null;return this._nextMap[ch];},put:function(key,pos,value){var nextNode,ch,old;if(key.length==pos){old=this._value;this.setValue(value);return old;}
ch=key.charAt(pos);if(this._nextMap==null){this._nextMap=Trie.Node.newNodeMap();nextNode=new Trie.Node();this._nextMap[ch]=nextNode;}else if((nextNode=this._nextMap[ch])==null){nextNode=new Trie.Node();this._nextMap[ch]=nextNode;}
return nextNode.put(key,pos+1,value);},get:function(key,pos){var nextNode;if(key.length<=pos)
return this._value;if((nextNode=this.getNextNode(key.charAt(pos)))==null)
return null;return nextNode.get(key,pos+1);},getLongestMatch:function(key,pos){var nextNode,ret;if(key.length<=pos){return Trie.Entry.newInstanceIfNeeded(key,this._value);}
if((nextNode=this.getNextNode(key.charAt(pos)))==null){return Trie.Entry.newInstanceIfNeeded(key,pos,this._value);}
if((ret=nextNode.getLongestMatch(key,pos+1))!=null){return ret;}
return Trie.Entry.newInstanceIfNeeded(key,pos,this._value);}});Trie.Entry.newInstanceIfNeeded=function(){var key=arguments[0],value,keyLength;if(typeof arguments[1]=='string'){value=arguments[1];keyLength=key.length;}else{keyLength=arguments[1];value=arguments[2];}
if(value==null||key==null){return null;}
if(key.length>keyLength){key=key.substr(0,keyLength);}
return new Trie.Entry(key,value);};Trie.Node.newNodeMap=function(){return{};};var isValidCodePoint=function(codepoint){return codepoint>=0x0000&&codepoint<=0x10FFFF;};var isWhiteSpace=function(input){return input.match(/[\s]/);};var MAP_ENTITY_TO_CHAR=[];var MAP_CHAR_TO_ENTITY=[];var ENTITY_TO_CHAR_TRIE=new Trie();(function(){MAP_ENTITY_TO_CHAR["&quot"]="34";MAP_ENTITY_TO_CHAR["&amp"]="38";MAP_ENTITY_TO_CHAR["&lt"]="60";MAP_ENTITY_TO_CHAR["&gt"]="62";MAP_ENTITY_TO_CHAR["&nbsp"]="160";MAP_ENTITY_TO_CHAR["&iexcl"]="161";MAP_ENTITY_TO_CHAR["&cent"]="162";MAP_ENTITY_TO_CHAR["&pound"]="163";MAP_ENTITY_TO_CHAR["&curren"]="164";MAP_ENTITY_TO_CHAR["&yen"]="165";MAP_ENTITY_TO_CHAR["&brvbar"]="166";MAP_ENTITY_TO_CHAR["&sect"]="167";MAP_ENTITY_TO_CHAR["&uml"]="168";MAP_ENTITY_TO_CHAR["&copy"]="169";MAP_ENTITY_TO_CHAR["&ordf"]="170";MAP_ENTITY_TO_CHAR["&laquo"]="171";MAP_ENTITY_TO_CHAR["&not"]="172";MAP_ENTITY_TO_CHAR["&shy"]="173";MAP_ENTITY_TO_CHAR["&reg"]="174";MAP_ENTITY_TO_CHAR["&macr"]="175";MAP_ENTITY_TO_CHAR["&deg"]="176";MAP_ENTITY_TO_CHAR["&plusmn"]="177";MAP_ENTITY_TO_CHAR["&sup2"]="178";MAP_ENTITY_TO_CHAR["&sup3"]="179";MAP_ENTITY_TO_CHAR["&acute"]="180";MAP_ENTITY_TO_CHAR["&micro"]="181";MAP_ENTITY_TO_CHAR["&para"]="182";MAP_ENTITY_TO_CHAR["&middot"]="183";MAP_ENTITY_TO_CHAR["&cedil"]="184";MAP_ENTITY_TO_CHAR["&sup1"]="185";MAP_ENTITY_TO_CHAR["&ordm"]="186";MAP_ENTITY_TO_CHAR["&raquo"]="187";MAP_ENTITY_TO_CHAR["&frac14"]="188";MAP_ENTITY_TO_CHAR["&frac12"]="189";MAP_ENTITY_TO_CHAR["&frac34"]="190";MAP_ENTITY_TO_CHAR["&iquest"]="191";MAP_ENTITY_TO_CHAR["&Agrave"]="192";MAP_ENTITY_TO_CHAR["&Aacute"]="193";MAP_ENTITY_TO_CHAR["&Acirc"]="194";MAP_ENTITY_TO_CHAR["&Atilde"]="195";MAP_ENTITY_TO_CHAR["&Auml"]="196";MAP_ENTITY_TO_CHAR["&Aring"]="197";MAP_ENTITY_TO_CHAR["&AElig"]="198";MAP_ENTITY_TO_CHAR["&Ccedil"]="199";MAP_ENTITY_TO_CHAR["&Egrave"]="200";MAP_ENTITY_TO_CHAR["&Eacute"]="201";MAP_ENTITY_TO_CHAR["&Ecirc"]="202";MAP_ENTITY_TO_CHAR["&Euml"]="203";MAP_ENTITY_TO_CHAR["&Igrave"]="204";MAP_ENTITY_TO_CHAR["&Iacute"]="205";MAP_ENTITY_TO_CHAR["&Icirc"]="206";MAP_ENTITY_TO_CHAR["&Iuml"]="207";MAP_ENTITY_TO_CHAR["&ETH"]="208";MAP_ENTITY_TO_CHAR["&Ntilde"]="209";MAP_ENTITY_TO_CHAR["&Ograve"]="210";MAP_ENTITY_TO_CHAR["&Oacute"]="211";MAP_ENTITY_TO_CHAR["&Ocirc"]="212";MAP_ENTITY_TO_CHAR["&Otilde"]="213";MAP_ENTITY_TO_CHAR["&Ouml"]="214";MAP_ENTITY_TO_CHAR["&times"]="215";MAP_ENTITY_TO_CHAR["&Oslash"]="216";MAP_ENTITY_TO_CHAR["&Ugrave"]="217";MAP_ENTITY_TO_CHAR["&Uacute"]="218";MAP_ENTITY_TO_CHAR["&Ucirc"]="219";MAP_ENTITY_TO_CHAR["&Uuml"]="220";MAP_ENTITY_TO_CHAR["&Yacute"]="221";MAP_ENTITY_TO_CHAR["&THORN"]="222";MAP_ENTITY_TO_CHAR["&szlig"]="223";MAP_ENTITY_TO_CHAR["&agrave"]="224";MAP_ENTITY_TO_CHAR["&aacute"]="225";MAP_ENTITY_TO_CHAR["&acirc"]="226";MAP_ENTITY_TO_CHAR["&atilde"]="227";MAP_ENTITY_TO_CHAR["&auml"]="228";MAP_ENTITY_TO_CHAR["&aring"]="229";MAP_ENTITY_TO_CHAR["&aelig"]="230";MAP_ENTITY_TO_CHAR["&ccedil"]="231";MAP_ENTITY_TO_CHAR["&egrave"]="232";MAP_ENTITY_TO_CHAR["&eacute"]="233";MAP_ENTITY_TO_CHAR["&ecirc"]="234";MAP_ENTITY_TO_CHAR["&euml"]="235";MAP_ENTITY_TO_CHAR["&igrave"]="236";MAP_ENTITY_TO_CHAR["&iacute"]="237";MAP_ENTITY_TO_CHAR["&icirc"]="238";MAP_ENTITY_TO_CHAR["&iuml"]="239";MAP_ENTITY_TO_CHAR["&eth"]="240";MAP_ENTITY_TO_CHAR["&ntilde"]="241";MAP_ENTITY_TO_CHAR["&ograve"]="242";MAP_ENTITY_TO_CHAR["&oacute"]="243";MAP_ENTITY_TO_CHAR["&ocirc"]="244";MAP_ENTITY_TO_CHAR["&otilde"]="245";MAP_ENTITY_TO_CHAR["&ouml"]="246";MAP_ENTITY_TO_CHAR["&divide"]="247";MAP_ENTITY_TO_CHAR["&oslash"]="248";MAP_ENTITY_TO_CHAR["&ugrave"]="249";MAP_ENTITY_TO_CHAR["&uacute"]="250";MAP_ENTITY_TO_CHAR["&ucirc"]="251";MAP_ENTITY_TO_CHAR["&uuml"]="252";MAP_ENTITY_TO_CHAR["&yacute"]="253";MAP_ENTITY_TO_CHAR["&thorn"]="254";MAP_ENTITY_TO_CHAR["&yuml"]="255";MAP_ENTITY_TO_CHAR["&OElig"]="338";MAP_ENTITY_TO_CHAR["&oelig"]="339";MAP_ENTITY_TO_CHAR["&Scaron"]="352";MAP_ENTITY_TO_CHAR["&scaron"]="353";MAP_ENTITY_TO_CHAR["&Yuml"]="376";MAP_ENTITY_TO_CHAR["&fnof"]="402";MAP_ENTITY_TO_CHAR["&circ"]="710";MAP_ENTITY_TO_CHAR["&tilde"]="732";MAP_ENTITY_TO_CHAR["&Alpha"]="913";MAP_ENTITY_TO_CHAR["&Beta"]="914";MAP_ENTITY_TO_CHAR["&Gamma"]="915";MAP_ENTITY_TO_CHAR["&Delta"]="916";MAP_ENTITY_TO_CHAR["&Epsilon"]="917";MAP_ENTITY_TO_CHAR["&Zeta"]="918";MAP_ENTITY_TO_CHAR["&Eta"]="919";MAP_ENTITY_TO_CHAR["&Theta"]="920";MAP_ENTITY_TO_CHAR["&Iota"]="921";MAP_ENTITY_TO_CHAR["&Kappa"]="922";MAP_ENTITY_TO_CHAR["&Lambda"]="923";MAP_ENTITY_TO_CHAR["&Mu"]="924";MAP_ENTITY_TO_CHAR["&Nu"]="925";MAP_ENTITY_TO_CHAR["&Xi"]="926";MAP_ENTITY_TO_CHAR["&Omicron"]="927";MAP_ENTITY_TO_CHAR["&Pi"]="928";MAP_ENTITY_TO_CHAR["&Rho"]="929";MAP_ENTITY_TO_CHAR["&Sigma"]="931";MAP_ENTITY_TO_CHAR["&Tau"]="932";MAP_ENTITY_TO_CHAR["&Upsilon"]="933";MAP_ENTITY_TO_CHAR["&Phi"]="934";MAP_ENTITY_TO_CHAR["&Chi"]="935";MAP_ENTITY_TO_CHAR["&Psi"]="936";MAP_ENTITY_TO_CHAR["&Omega"]="937";MAP_ENTITY_TO_CHAR["&alpha"]="945";MAP_ENTITY_TO_CHAR["&beta"]="946";MAP_ENTITY_TO_CHAR["&gamma"]="947";MAP_ENTITY_TO_CHAR["&delta"]="948";MAP_ENTITY_TO_CHAR["&epsilon"]="949";MAP_ENTITY_TO_CHAR["&zeta"]="950";MAP_ENTITY_TO_CHAR["&eta"]="951";MAP_ENTITY_TO_CHAR["&theta"]="952";MAP_ENTITY_TO_CHAR["&iota"]="953";MAP_ENTITY_TO_CHAR["&kappa"]="954";MAP_ENTITY_TO_CHAR["&lambda"]="955";MAP_ENTITY_TO_CHAR["&mu"]="956";MAP_ENTITY_TO_CHAR["&nu"]="957";MAP_ENTITY_TO_CHAR["&xi"]="958";MAP_ENTITY_TO_CHAR["&omicron"]="959";MAP_ENTITY_TO_CHAR["&pi"]="960";MAP_ENTITY_TO_CHAR["&rho"]="961";MAP_ENTITY_TO_CHAR["&sigmaf"]="962";MAP_ENTITY_TO_CHAR["&sigma"]="963";MAP_ENTITY_TO_CHAR["&tau"]="964";MAP_ENTITY_TO_CHAR["&upsilon"]="965";MAP_ENTITY_TO_CHAR["&phi"]="966";MAP_ENTITY_TO_CHAR["&chi"]="967";MAP_ENTITY_TO_CHAR["&psi"]="968";MAP_ENTITY_TO_CHAR["&omega"]="969";MAP_ENTITY_TO_CHAR["&thetasym"]="977";MAP_ENTITY_TO_CHAR["&upsih"]="978";MAP_ENTITY_TO_CHAR["&piv"]="982";MAP_ENTITY_TO_CHAR["&ensp"]="8194";MAP_ENTITY_TO_CHAR["&emsp"]="8195";MAP_ENTITY_TO_CHAR["&thinsp"]="8201";MAP_ENTITY_TO_CHAR["&zwnj"]="8204";MAP_ENTITY_TO_CHAR["&zwj"]="8205";MAP_ENTITY_TO_CHAR["&lrm"]="8206";MAP_ENTITY_TO_CHAR["&rlm"]="8207";MAP_ENTITY_TO_CHAR["&ndash"]="8211";MAP_ENTITY_TO_CHAR["&mdash"]="8212";MAP_ENTITY_TO_CHAR["&lsquo"]="8216";MAP_ENTITY_TO_CHAR["&rsquo"]="8217";MAP_ENTITY_TO_CHAR["&sbquo"]="8218";MAP_ENTITY_TO_CHAR["&ldquo"]="8220";MAP_ENTITY_TO_CHAR["&rdquo"]="8221";MAP_ENTITY_TO_CHAR["&bdquo"]="8222";MAP_ENTITY_TO_CHAR["&dagger"]="8224";MAP_ENTITY_TO_CHAR["&Dagger"]="8225";MAP_ENTITY_TO_CHAR["&bull"]="8226";MAP_ENTITY_TO_CHAR["&hellip"]="8230";MAP_ENTITY_TO_CHAR["&permil"]="8240";MAP_ENTITY_TO_CHAR["&prime"]="8242";MAP_ENTITY_TO_CHAR["&Prime"]="8243";MAP_ENTITY_TO_CHAR["&lsaquo"]="8249";MAP_ENTITY_TO_CHAR["&rsaquo"]="8250";MAP_ENTITY_TO_CHAR["&oline"]="8254";MAP_ENTITY_TO_CHAR["&frasl"]="8260";MAP_ENTITY_TO_CHAR["&euro"]="8364";MAP_ENTITY_TO_CHAR["&image"]="8365";MAP_ENTITY_TO_CHAR["&weierp"]="8472";MAP_ENTITY_TO_CHAR["&real"]="8476";MAP_ENTITY_TO_CHAR["&trade"]="8482";MAP_ENTITY_TO_CHAR["&alefsym"]="8501";MAP_ENTITY_TO_CHAR["&larr"]="8592";MAP_ENTITY_TO_CHAR["&uarr"]="8593";MAP_ENTITY_TO_CHAR["&rarr"]="8594";MAP_ENTITY_TO_CHAR["&darr"]="8595";MAP_ENTITY_TO_CHAR["&harr"]="8596";MAP_ENTITY_TO_CHAR["&crarr"]="8629";MAP_ENTITY_TO_CHAR["&lArr"]="8656";MAP_ENTITY_TO_CHAR["&uArr"]="8657";MAP_ENTITY_TO_CHAR["&rArr"]="8658";MAP_ENTITY_TO_CHAR["&dArr"]="8659";MAP_ENTITY_TO_CHAR["&hArr"]="8660";MAP_ENTITY_TO_CHAR["&forall"]="8704";MAP_ENTITY_TO_CHAR["&part"]="8706";MAP_ENTITY_TO_CHAR["&exist"]="8707";MAP_ENTITY_TO_CHAR["&empty"]="8709";MAP_ENTITY_TO_CHAR["&nabla"]="8711";MAP_ENTITY_TO_CHAR["&isin"]="8712";MAP_ENTITY_TO_CHAR["&notin"]="8713";MAP_ENTITY_TO_CHAR["&ni"]="8715";MAP_ENTITY_TO_CHAR["&prod"]="8719";MAP_ENTITY_TO_CHAR["&sum"]="8721";MAP_ENTITY_TO_CHAR["&minus"]="8722";MAP_ENTITY_TO_CHAR["&lowast"]="8727";MAP_ENTITY_TO_CHAR["&radic"]="8730";MAP_ENTITY_TO_CHAR["&prop"]="8733";MAP_ENTITY_TO_CHAR["&infin"]="8734";MAP_ENTITY_TO_CHAR["&ang"]="8736";MAP_ENTITY_TO_CHAR["&and"]="8743";MAP_ENTITY_TO_CHAR["&or"]="8744";MAP_ENTITY_TO_CHAR["&cap"]="8745";MAP_ENTITY_TO_CHAR["&cup"]="8746";MAP_ENTITY_TO_CHAR["&int"]="8747";MAP_ENTITY_TO_CHAR["&there4"]="8756";MAP_ENTITY_TO_CHAR["&sim"]="8764";MAP_ENTITY_TO_CHAR["&cong"]="8773";MAP_ENTITY_TO_CHAR["&asymp"]="8776";MAP_ENTITY_TO_CHAR["&ne"]="8800";MAP_ENTITY_TO_CHAR["&equiv"]="8801";MAP_ENTITY_TO_CHAR["&le"]="8804";MAP_ENTITY_TO_CHAR["&ge"]="8805";MAP_ENTITY_TO_CHAR["&sub"]="8834";MAP_ENTITY_TO_CHAR["&sup"]="8835";MAP_ENTITY_TO_CHAR["&nsub"]="8836";MAP_ENTITY_TO_CHAR["&sube"]="8838";MAP_ENTITY_TO_CHAR["&supe"]="8839";MAP_ENTITY_TO_CHAR["&oplus"]="8853";MAP_ENTITY_TO_CHAR["&otimes"]="8855";MAP_ENTITY_TO_CHAR["&perp"]="8869";MAP_ENTITY_TO_CHAR["&sdot"]="8901";MAP_ENTITY_TO_CHAR["&lceil"]="8968";MAP_ENTITY_TO_CHAR["&rceil"]="8969";MAP_ENTITY_TO_CHAR["&lfloor"]="8970";MAP_ENTITY_TO_CHAR["&rfloor"]="8971";MAP_ENTITY_TO_CHAR["&lang"]="9001";MAP_ENTITY_TO_CHAR["&rang"]="9002";MAP_ENTITY_TO_CHAR["&loz"]="9674";MAP_ENTITY_TO_CHAR["&spades"]="9824";MAP_ENTITY_TO_CHAR["&clubs"]="9827";MAP_ENTITY_TO_CHAR["&hearts"]="9829";MAP_ENTITY_TO_CHAR["&diams"]="9830";for(var entity in MAP_ENTITY_TO_CHAR){if(!(typeof MAP_ENTITY_TO_CHAR[entity]=='function')&&MAP_ENTITY_TO_CHAR.hasOwnProperty(entity)){MAP_CHAR_TO_ENTITY[MAP_ENTITY_TO_CHAR[entity]]=entity;}}
for(var c in MAP_CHAR_TO_ENTITY){if(!(typeof MAP_CHAR_TO_ENTITY[c]=='function')&&MAP_CHAR_TO_ENTITY.hasOwnProperty(c)){var ent=MAP_CHAR_TO_ENTITY[c].toLowerCase().substr(1);ENTITY_TO_CHAR_TRIE.put(ent,String.fromCharCode(c));}}})();if(Object.freeze){$.encoder=Object.freeze($.encoder);$.fn.encode=Object.freeze($.fn.encode);}else if(Object.seal){$.encoder=Object.seal($.encoder);$.fn.encode=Object.seal($.fn.encode);}else if(Object.preventExtensions){$.encoder=Object.preventExtensions($.encoder);$.fn.encode=Object.preventExtensions($.fn.encode);}})(jQuery);
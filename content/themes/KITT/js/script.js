(function($) {
	$.foolslideui = function(element, options) {

		var defaults = {
			slideUrls: [],
			sidebarElement: "",
			contentElement: "",
			history: false, // needs History.js loaded!
			googleAnalyticsCode: "",
			afterSidebarUpdate: function() {},
			afterContentUpdate: function() {},
			afterDisplayHome: function() {},
			afterDisplayComic: function() {},
			afterDisplayReader: function() {}
		}

		var plugin = this;
		var foolslide;

		plugin.settings = {}

		var $element = $(element),
		element = element;

		// the "constructor" method that gets called when the object is created
		plugin.init = function() {
			plugin.settings = $.extend({}, defaults, options);
			foolslide = new $.foolslide(null, {
				slideUrls: plugin.settings.slideUrls
			});
			
			// remove the trailing slashes
			$.each(plugin.settings.slideUrls, function(index, value){
				if(value.substr(-1) == "/")
					plugin.settings.slideUrls[index] = value.substr(0, value.length-1);
			});
			
			if(plugin.settings.sidebarElement != "")
			{
				plugin.buildSidebar(plugin.settings.sidebarElement);
			}
			if(plugin.settings.contentElement != "")
			{
				plugin.buildContent(plugin.settings.contentElement);
			}
			plugin.settings.url = plugin.settings.slideUrls[0];
			
			if(plugin.settings.history) {
				/*
				jQuery(window).bind('statechange',function(){
					urlDecoder();
				});
				 */
				urlDecoder();
			}
		}
		
		var urlDecoder = function() {
			var state = History.getState();
			var url = state.url;
			url = url.substr(plugin.settings.url.length);
			if(url == "")
			{
				// no segments, add one
				History.pushState({}, "Home", plugin.settings.url + "reader");
				return false;
			}
			
			var segments = url.split('/');			
			if(segments.length > 3) {

				switch(segments[2]) {
					case "comic":
						plugin.displayComic({
							stub: segments[3]
						});
						break;
					case "team":
						plugin.displayTeam({
							stub: segments[1]
						});
						break;
					case "read":
						var readerSettings = {
							comic_stub: segments[3],
							language: segments[4],
							volume: segments[5],
							chapter: segments[6],
							subchapter: segments[7],
							team: segments[8],
							joint: segments[9]
						};
						plugin.displayReader(readerSettings);
						break;
				}
			}
			else
			{
				plugin.displayHome();
			//plugin.display404();
			}
			
			if(segments.length > 0 && plugin.settings.googleAnalyticsCode)
			{
				window._gaq.push(['_setAccount', plugin.settings.googleAnalyticsCode]);
				window._gaq.push(['_trackPageview', state.url]);
			}
		}
		
		
		var mode = {
			mode:"none"
		};
		plugin.modeGet = function() {
			return mode;
		}
		
		var modeSet = function(obj){
			mode = obj;
		}
		
		plugin.displayHome = function(opt) {
			var def = {
				returnString: false,
				element: "#dynamic_content"
			}
			var opt = $.extend({}, def, opt);
			
			var echo = '' +
			'<div id="splash">' +
			'	<h1>Welcome to our FoOlSlide.</h1>' +
			'	<div class="latest">' +
			'		<div class="title">Latest releases:</div>' +
			'			<ol>';
		
			var latest = foolslide.readerChapters({
				direction: "desc"
			});
			setTimeout(plugin.sidebar.displayLatest, 0, opt);
			var count = 0;
			$.each(latest.chapters, function(index, value){
				if(count++ == 3) 
				{
					return false;
				}
				var current_comic = foolslide.readerComic({
					id:value.comic_id
				}).comics[0];
				var current_teams = foolslide.readerChapter({
					id: value.id
				}).teams;
				echo += '<li><a href="' + $.encoder.encodeForHTMLAttribute('', current_comic.href, true) + '" ' + $.encoder.encodeForHTMLAttribute('title', current_comic.name) + ' >' + $.encoder.encodeForHTML(current_comic.name) + '</a> - <a href="' + $.encoder.encodeForHTMLAttribute('', value.href, true) + '" ' + $.encoder.encodeForHTML('title', value.title) + '>' + $.encoder.encodeForHTML(value.title) + '</a>';
				echo += '<span class="meta">';
				$.each(current_teams, function(i,v){
					echo += '<a href="' + $.encoder.encodeForHTMLAttribute('', v.href, true) + '" ' + $.encoder.encodeForHTML('title', v.name) + '>' + $.encoder.encodeForHTML(v.name) + '</a>';
					if (i < current_teams.length-1)
					{
						echo += ", ";
					}
				});
				echo += '</span>';
			});
							
			echo += '		</ol>' +
			'	</div>' +
			'<div class="suggestion">' +
			'	<span class="bracket">{</span> we\'d suggest to activate your browser\'s fullscreen mode <span class="bracket">}</span>' +
			'</div>' +
			'</div>';
			
			if(opt.element != "") {
				$(opt.element).fadeOut(800, function(){
					mode = {
						mode: "home"
					};
					$(this).html(echo).fadeIn(800, function(){
						plugin.sidebar.autoHide(false);
					});
				});
			}
			plugin.settings.afterDisplayHome();
			plugin.settings.afterContentUpdate();
			if(opt.returnString) {
				return echo;
			}
		}
		
		
		plugin.displayComic = function(opt) {
			var def = {
				returnString: false,
				element: "#dynamic_content",
				id: 0,
				stub: "",
				slideUrl: plugin.settings.url
			}
			var opt = $.extend({}, def, opt);

			var comicArr = foolslide.readerComic(opt);
			setTimeout(plugin.sidebar.displayComic, 0, opt);
			var comic = comicArr.comics[0];
			var chapters = comic.chapters;
			
			var echo =	'' +
			'<div id="comic">' +
			'	<h1 class="title">' +  $.encoder.encodeForHTML(comic.name) + '</h1>';
			if(comic.thumb_url != "") {
				echo += '' +
				'	<div class="image"><img src="' +  $.encoder.encodeForHTMLAttribute('', comic.thumb_url, true) + '" ' +  $.encoder.encodeForHTMLAttribute('title', comic.name) + '/></div>';
			}
			echo += '' +
			'	<div class="description">' +  $.encoder.encodeForHTML(comic.description) + '</div>' +
			'</div>';
			
			
			if(opt.element != "") {
				plugin.sidebar.autoHide(false);
				$(opt.element).fadeOut(800, function(){
					$(this).html(echo).fadeIn(800, function(){
						});
				});
			}
			History.pushState(null, null, comic.href);
			plugin.settings.afterDisplayComic();
			plugin.settings.afterContentUpdate();
			return false;
		}
		
		plugin.displayReader = function(opt) {
			var def = {
				element: "#dynamic_content",
				comic_id: 0,
				id: 0,
				comic_stub: "",
				language: "",
				volume: 0,
				chapter: 0,
				subchapter: 0,
				team: "",
				joint: 0,
				page: 1,
				slideUrl: plugin.settings.url,
				forcePages: true
			}
			var opt = $.extend({}, def, opt);
			if(opt.id > 0) {
				var chapterArr = foolslide.readerChapter({
					id: opt.id,
					slideUrl: opt.slideUrl,
					forcePages: true
				});

				opt.id = chapterArr.comics[0].id;
				if(opt.page < 0 || opt.page > chapterArr.pages.length)
				{
					opt.page = 1;
				}
			}
			else
			{	
				var chapterArr = foolslide.readerChapter(opt);
				opt.id = chapterArr.comics[0].id;

				if(opt.subchapter == "page" && !isNaN(opt.team))
				{
					opt.page = opt.team;
				}
			
				if(opt.team == "page" && !isNaN(opt.joint))
				{
					opt.page = opt.team;
				}
			
				if(opt.joint == "page" && !isNaN(opt.page))
				{
					opt.page = opt.team;
				}
			}
			
			if(mode.mode != "reader" || chapterArr.chapters[0].comic_id != mode.comic_id) {
				setTimeout(plugin.sidebar.displayComic, 0, opt);
			}
		
			var echo =	'' +
			'<div id="reader">' +
			'	<div class="previews"></div>' +
			'	<div class="topbar"></div>' +
			'	<div class="page">' + 
			'		<img class="displayed" src="" onClick="$.foolslideui.reader.nextPage()">' +
			'	</div>' +
			'	<div class="bottombar"></div>' +
			'</div>';
			
			
			if(opt.element != "") {
				$(opt.element).fadeOut(800, function(){
					modeSet({
						mode: "reader",
						comic_id: chapterArr.chapters[0].comic_id,
						chapter_id: chapterArr.chapters[0].id,
						slideUrl: chapterArr.chapters[0].slideUrl
					});
					plugin.reader.currentChapter = chapterArr.chapters;
					plugin.reader.currentComic = chapterArr.comics[0];
					plugin.reader.currentPages = chapterArr.pages;
					plugin.reader.currentPage = opt.page;
					History.pushState(null, null, chapterArr.chapters[0].href)
					$(this).html(echo);
					
					plugin.reader.changePage(opt.page);
					
					$(this).fadeIn(800, function(){
						plugin.sidebar.autoHide(true);
					});
				});
			}
			plugin.settings.afterDisplayComic();
			plugin.settings.afterContentUpdate();
			
			return false;
		}
		
		
		plugin.reader = {};
		
		plugin.reader.currentPage = 0;
		plugin.reader.isSpreadPage = 0;
		plugin.reader.currentPages = {};
		plugin.reader.currentChapter = {};
		plugin.reader.currentComic = {};
		plugin.reader.nextChapter = {};
		
		plugin.reader.changePage = function(pgNum) {
			if (mode.mode != "reader")
				return false;
			var pageImgElem = $("#reader .page .displayed");
			pageImgElem.attr('src', plugin.reader.currentPages[pgNum-1].url);
			plugin.reader.currentPage = pgNum;
			plugin.reader.resizePage(pgNum);
		}
		
		plugin.reader.resizePage = function (pgNum) {
			var contentElem = $("#dynamic_content")
			var readerElem = $("#reader")
			var pageElem = readerElem.find(".page");
			var imgElem = pageElem.find(".displayed");
			var id = pgNum - 1;
			var pages = plugin.reader.currentPages;
			var doc_width = readerElem.width();
			var page_width = parseInt(pages[id].width);
			var page_height = parseInt(pages[id].height);
			var nice_width = 980;
			var perfect_width = 980;
		
			if(doc_width > 1200) {
				nice_width = 1120;
				perfect_width = 1000;
			}
			if(doc_width > 1600) {
				nice_width = 1400;
				perfect_width = 1300;
			}
			if(doc_width > 1800) {
				nice_width = 1600;
				perfect_width = 1500;
			}
		
			var width, height;
		
			if (page_width > nice_width && (page_width/page_height) > 1.2) {
				if(page_height < 1610) {
					width = page_width;
					height = page_height;
				}
				else { 
					height = 1600;
					width = page_width;
					width = (height*width)/(page_height);
				}
				
				
				
				
				
				
				if(pageElem.width() < imgElem.width()) {
					plugin.reader.isSpreadPage = true;
				}
				else {
					pageElem.css({
						'max-width': width+10, 
						'overflow':'hidden'
					});
					plugin.reader.isSpreadPage = false;
				}
			}
			else{
				if(page_width < nice_width && doc_width > page_width + 10) {
					width = page_width;
					height = page_height;
				}
				else { 
					width = (doc_width > perfect_width) ? perfect_width : doc_width - 10;
					height = page_height; 
					height = (height*width)/page_width;
				}
				
				
				plugin.reader.isSpreadPage =  false;
			}
		}
		
		plugin.reader.nextPage = function() {
			plugin.reader.changePage(plugin.reader.currentPage + 1);
			return false;
		}
		 
		plugin.sidebar = {};
		
		plugin.sidebar.autoHide = function(bool) {
			if(bool)
			{
				plugin.sidebar.toggleSidebar(false);
				$(".foolslideui_content").css({
					marginLeft:"25px"
				}, 1000);
				$(".foolslideui_sidebar").hover(function(){
					plugin.sidebar.toggleSidebar(true)
				}, function(){
					plugin.sidebar.toggleSidebar(false)
				});
			}
			else
			{
				$(".foolslideui_sidebar").unbind('mouseenter mouseleave');
				plugin.sidebar.toggleSidebar(true);
				$(".foolslideui_content").animate({
					marginLeft: $(".foolslideui_sidebar").data('width')
				}, 1000);
			}
		}
		
		plugin.sidebar.toggleSidebar = function(open) {

			var sidebarElem = $(".foolslideui_sidebar");
			if(typeof sidebarElem.data('width') == "undefined")
			{
				sidebarElem.data('width', sidebarElem.css('width'));
			}

			// stop all animations
			sidebarElem.stop(true);
			if(open)
			{
				sidebarElem.animate({
					left:"0px"
				}, 1000);
				sidebarElem.find(".handle").fadeOut(800);
			}
			
			if(!open)
			{					
				sidebarElem.animate({
					left: -parseInt(sidebarElem.data('width').replace('px', '')) + 25 + "px"
				}, 1000);
				sidebarElem.find(".handle").fadeIn(800);
			}
		}
		
		plugin.infoComic = function(elem, id){
			var el = jQuery(elem).parent().parent().parent().find("li.info");
			if(el.height() > 0) {
				el.animate({
					height: 0 + "px"
				},100);
				return false;
			}
			var height = el.find(".inside").height();
			el.animate({
				height: height + 10 + "px"
			},100);
			return false;
		};
		
		var sidebarChapters = function(arr) {
			var current_comic_id = 0;
			var current_comic = {};
			var current_team_id = 0;
			var current_joint_id = 0;
			var current_teams = [];
			var result = [];
			var preresult = {};
			$.each(arr.chapters, function(index, value){
				
				if(value.comic_id != current_comic_id)
				{
					current_comic_id = value.comic_id;
					result.push(preresult);
					preresult = {};
					preresult.elements = [];
					current_comic = foolslide.readerComic({
						id: value.comic_id,
						slideUrl: value.slideUrl
					}).comics[0];
					preresult.group = {
						href: current_comic.href,
						text: current_comic.name,
						title: current_comic.name,
						comic: current_comic,
						onClick: "displayComic({id: " + parseInt(current_comic.id) + ", slideUrl: '" + $.encoder.encodeForHTMLAttribute('', current_comic.slideUrl, true) + "'})"
					};
					
					preresult.info = {
						text: current_comic.description,
						comic: current_comic
					};
					
					if(current_comic.thumb_url != "") {
						preresult.info.image = {
							comic: current_comic,
							href: current_comic.href,
							alt: current_comic.name,
							onClick: "displayComic({id: " + parseInt(current_comic.id) + ", slideUrl: '" + $.encoder.encodeForHTMLAttribute('', current_comic.slideUrl, true) + "'})"
							
						};
						preresult.info.image.src = current_comic.thumb_url;
					}
					
					preresult.group.plus = {
						href: current_comic.href,
						title: current_comic.name,
						onClick: "infoComic(this)"
					};
				}
				
				if(value.team_id != current_team_id || value.joint_id != current_joint_id)
				{
					current_team_id = value.team_id;
					current_joint_id = value.joint_id;
					current_teams = foolslide.readerChapter({
						id: value.id,
						slideUrl: value.slideUrl
					}).teams;
					
					if(current_teams.length == 1)
					{
						preresult.meta = {
							teams: current_teams,
							text: current_teams[0].name,
							href: current_teams[0].href,
							title: current_teams[0].name
						};
					}
					else
					{
						preresult.meta = {
							text: "",
							teams: current_teams
						};
						$.each(current_teams, function(i,v){
							preresult.meta.text += '<a href="' + $.encoder.encodeForHTMLAttribute('', v.href, true) + '" onClick="return displayTeam(this)" ' + $.encoder.encodeForHTMLAttribute('title', v.title) + '">' + $.encoder.encodeForHTML(v.name) + '</a>';
							
							if (i < current_teams.length-1)
							{
								preresult.meta.text += ", ";
							}
						});
					}
				}
				
				preresult.elements.push({
					chapter: value,
					text: value.title,
					href: value.href,
					title: value.title,
					onClick: "displayReader({id:" + parseInt(value.id) + ", slideUrl: '" + $.encoder.encodeForHTMLAttribute('', value.slideUrl, true) + "'})"
				});				
			});
			
			result.push(preresult);
			return result;
		}
		
		plugin.sidebar.displayLatest = function(opt){
			var def = {
				direction: "desc",
				orderby: "created",
				per_page: 40,
				page: 1,
				cache:true
			}
			var opt = $.extend({}, def, opt);
			var latest = foolslide.readerChapters(opt);
			updateSidebar(sidebarChapters(latest));
			return false;
		}
		
		plugin.sidebar.displayComic = function(opt){
			var def = {
				direction: "desc",
				orderby: "created",
				per_page: 40,
				page: 1,
				cache:true,
				forceChapters: true
			}
			var opt = $.extend({}, def, opt);
			var latest = foolslide.readerComic(opt);
			updateSidebar(sidebarChapters(latest));
			return false;
		}
		
		var updateSidebar = function(arr) {
			$(".foolslideui_sidebar .items").animate({
				position: "relative",
				right: "-130%"
			}, 1000, 
			function(){
				var echo = '';
				$.each(arr, function(index, value){
					if (typeof value.group != "undefined")
					{
						echo += '<ul>';
						echo += '	<li class="group">';
						if (typeof value.group.plus != "undefined") {
							echo += '		<div class="plus">';
							echo += '			<a href="' + $.encoder.encodeForHTMLAttribute('', value.group.plus.href, true) + '" onClick="return $.foolslideui.' + value.group.plus.onClick + '" ' + $.encoder.encodeForHTMLAttribute('title', value.group.plus.title) +'>+</a>';
							echo += '		</div>';
						}
						echo += '		<div class="text"><a href="' + $.encoder.encodeForHTMLAttribute('', value.group.href, true) + '" onClick="return $.foolslideui.' + value.group.onClick + '" ' + $.encoder.encodeForHTMLAttribute('title', value.group.title) + '>' + $.encoder.encodeForHTML(value.group.text) + '</a></div>';
						echo +=	'	</li>';
					}
										
					if(typeof value.info != "undefined")
					{
						echo += '	<li class="info clearfix"><div class="inside">';
						if(typeof value.info.image != "undefined")
						{
							echo += '		<div class="image">';
							echo += '			<a href="' + $.encoder.encodeForHTMLAttribute('alt', value.info.image.href, true) + '" onClick="return $.foolslideui.' + value.info.image.onClick + '" ' + $.encoder.encodeForHTMLAttribute('title', value.info.image.title) + '><img src="' + $.encoder.encodeForHTMLAttribute('', value.info.image.src, true) + '" ' + $.encoder.encodeForHTMLAttribute('alt', value.info.image.alt) + '></a>';
							echo += '		</div>';
						}
						echo += '		<div class="text">' + $.encoder.encodeForHTML(value.info.text) + '</div>';
						echo +=	'	</div></li>';
					}
						
					if(typeof value.meta != "undefined")
					{
						echo += '	<li class="meta">';
						if(typeof value.meta.href != "undefined")
						{
							echo += '			<div class="text"><a href="' + $.encoder.encodeForHTMLAttribute('',value.meta.href, true) + '" ' + $.encoder.encodeForHTMLAttribute('title', value.meta.title) + '">' + $.encoder.encodeForHTML(value.meta.text) + '</a></div>';
						}
						else 
						{
							//already sanitized in array creation function
							echo += '			<div class="text a_fill">' + value.meta.text + '<div>';
						}
						echo +=	'	</li>';
					}
				
					if(typeof value.elements != "undefined") 
					{
						$.each(value.elements, function(i, v){
							echo += '	<li class="element">';
							if (typeof v.plus != "undefined") {
								echo += '		<div class="plus">';
								echo += '			<a href="' + $.encoder.encodeForHTMLAttribute('', v.plus.href, true) + '" onClick="return $.foolslideui.' + v.plus.onClick + '" ' + $.encoder.encodeForHTMLAttribute('title', v.plus.title) +'>+</a>';
								echo += '		</div>';
							}
							echo += '		<div class="text"><a href="' + $.encoder.encodeForHTMLAttribute('', v.href, true) + '" onClick="return $.foolslideui.' + v.onClick + '" ' + $.encoder.encodeForHTMLAttribute('title', v.title) + '>' + $.encoder.encodeForHTML(v.text) + '</a></div>';
							echo +=	'	</li>';
						});
					}
					echo += '</ul>';
				});
				
				
				$(this).find("#dynamic_sidebar").html(echo);
				
				$(this).css({
					top:"0",
					right:-$(this).width()
				});
				$(this).animate({
					right:"0"
				});
			});

			// event
			plugin.settings.afterSidebarUpdate();
		}

		// inject and returns the sidebar components
		plugin.buildSidebar = function(elem) {
			var echo = '' +
			'<div class="layer1"></div>' +
			'<div class	="items">' +
			'	<div id="dynamic_sidebar">' +
			'	</div>' +
			'<div class="handle" onClick="$.foolslideui.sidebar.toggleSidebar()"></div>' +
			'</div>';
			if(typeof elem != "undefined")
			{
				$(elem).addClass("foolslideui_sidebar");
				$(elem).html(echo);
			}
			return echo;
		}
			
		plugin.buildContent = function(elem) {
			var echo = '';
			echo += '<div class="layer1">' +
			'</div>' +
			'<div id="dynamic_content">' +
			'</div>';
			if(typeof elem != "undefined")
			{
				$(elem).addClass("foolslideui_content");
				$(elem).html(echo);
			}
		}

		// fire up the plugin!
		// call the "constructor" method
		plugin.init();

	}

	// add the plugin to the jQuery.fn object
	$.fn.foolslideui = function(options) {
		return this.each(function() {
			if (undefined == $(this).data('foolslideui')) {
				var plugin = new $.foolslideui(this, options);
				$(this).data('foolslideui', plugin);
				$.foolslideui = plugin;
			}
		});
	}

})(jQuery);


jQuery(document).ready(function(){
	var settings = {
		slideUrls:[slideUrl],
		sidebarElement: "#sidebar",
		contentElement: "#main",
		history: true,
		fromUrl: true
	}
	if(typeof googleAnalyticsCode != "undefined" && googleAnalyticsCode != "")
	{
		settings.googleAnalyticsCode = googleAnalyticsCode;
	}
	
	$('#container').foolslideui(settings);
});
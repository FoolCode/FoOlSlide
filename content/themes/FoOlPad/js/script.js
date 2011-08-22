(function($) {

	// here we go!
	$.foolslide = function(element, options) {

		var defaults = {

			slideUrls: []
		//onFoo: function() {}

		}

		var plugin = this;


		plugin.settings = {}

		var $element = $(element),
		element = element;

		// the "constructor" method that gets called when the object is created
		plugin.init = function() {
			plugin.settings = $.extend({}, defaults, options);
		}
		
		var loadedComics = {};
		var loadedChapters = {};
		var loadedPages = {};
		var loadedTeams = {};
		var loadedJoints = {};
		var loadedUsers = {};
		var loadedMembers = {};

		plugin.cleanCache = function() {
			loadedComics = {};
			loadedChapters = {};
			loadedPages = {};
			loadedTeams = {};
			loadedJoints = {};
			loadedUsers = {};
			loadedMembers = {};
		}


		/**
		 * Put an array of slideUrls/comics in the cache
		 */
		var processComics = function(obj) {
			$.each(obj, function(index, value){
				$.each(value.comics, function(i, v){
					if(typeof loadedComics[v.id + "_" + index] == "undefined" || dateTimeToDate(v.updated) > dateTimeToDate(loadedComics[v.id + "_" + index].updated)) {
						loadedComics[v.id + "_" + index] = v;
						loadedComics[v.id + "_" + index].slideUrl = index;
					}
				});
			});
		}
		
		/**
		 * Put an array of slideUrls/chapters in the cache
		 */
		var processChapters = function(obj) {
			// cycle through the slideUrls
			$.each(obj, function(index, value){
				// cycle through each chapter data
				$.each(value.chapters, function(i, v){
					
					// check if there's the comic entry in this chapter and in case load if new
					if(typeof v.comic != "undefined" || typeof loadedComics[v.comic.id + "_" + index] == "undefined" || dateTimeToDate(v.comic.updated) > dateTimeToDate(loadedComics[v.comic.id + "_" + index].updated)) {
						loadedComics[v.comic.id + "_" + index] = v.comic;
						loadedComics[v.comic.id + "_" + index].slideUrl = index;
						if(typeof loadedComics[v.comic.id + "_" + index].href == "undefined")
						{
							loadedComics[v.comic.id + "_" + index].href = loadedComics[v.comic.id + "_" + index].slideUrl + "/reader/comic/" +  loadedComics[v.comic.id + "_" + index].stub;
						}
					}
					
					// check if there's an array of teams in this chapter and in case load if any are new
					if(typeof v.teams != "undefined") {
						// teams always come as an array
						$.each(v.teams, function(j,t){
							if (typeof loadedTeams[t.id + "_" + index] == "undefined" ||  (typeof loadedTeams[t.id + "_" + index] != "undefined" && dateTimeToDate(t.updated) > dateTimeToDate(loadedTeams[t.id + "_" + index].updated))) {
								loadedTeams[t.id + "_" + index] = t;
								loadedTeams[t.id + "_" + index].slideUrl = index;
								if(typeof loadedTeams[t.id + "_" + index].href == "undefined")
								{
									loadedTeams[t.id + "_" + index].href = loadedTeams[t.id + "_" + index].slideUrl + "/reader/team/" + loadedTeams[t.id + "_" + index].stub;
								}
							}
						});
					}
					
					// store those joints
					if(v.teams.length > 1) {
						loadedJoints[v.chapter.joint_id + "_" + index] = {
							id: v.joint_id, 
							slideUrl: index, 
							teams:[]
						};
						$.each(v.teams, function(j,t){
							loadedJoints[v.chapter.joint_id + "_" + index].teams.push(t.id);
						});
					}
					
					// almost surely we have a chapter
					if(typeof v.chapter != "undefined" && (typeof loadedChapters[v.chapter.id + "_" + index] == "undefined" || dateTimeToDate(v.chapter.updated) > dateTimeToDate(loadedChapters[v.chapter.id + "_" + index].updated))) {
						loadedChapters[v.chapter.id + "_" + index] = v.chapter;
						loadedChapters[v.chapter.id + "_" + index].slideUrl = index;
						if(typeof loadedChapters[v.chapter.id + "_" + index].href == "undefined")
						{
							// compatibility for FoOlSlide 0.7.6 and less
							loadedChapters[v.chapter.id + "_" + index].href = loadedChapters[v.chapter.id + "_" + index].slideUrl + "/reader/read/";
							
							// check that we already have the comic
							if(typeof loadedComics[v.chapter.comic_id + "_" + index] == "undefined")
							{
								var comicStub = plugin.readerComic({
									id: v.chapter.comic_id
									}).comics[0].stub;
								if(typeof comicStub == "undefined")
								{
									console.log("Impossible to fetch comic stub when creating href to chapter. Chapter was ID:" + v.chapter.id + " and comic was ID:" + v.chapter.comic_id);
								}
								else
								{
									loadedChapters[v.chapter.id + "_" + index].href += comicStub;
								}
							}
							else
							{
								loadedChapters[v.chapter.id + "_" + index].href += loadedComics[v.chapter.comic_id + "_" + index].stub;
							}
							
							loadedChapters[v.chapter.id + "_" + index].href += "/" + loadedChapters[v.chapter.id + "_" + index].language + "/" + loadedChapters[v.chapter.id + "_" + index].volume + "/" + loadedChapters[v.chapter.id + "_" + index].chapter + "/" + loadedChapters[v.chapter.id + "_" + index].subchapter + "/";
							
							// @todo add some check through the future membersTeam()
							if(loadedChapters[v.chapter.id + "_" + index].team_id > 0)
							{
								loadedChapters[v.chapter.id + "_" + index].href += loadedTeams[loadedChapters[v.chapter.id + "_" + index].team_id + "_" + index].stub + "/";
							}
							else 
							{
								loadedChapters[v.chapter.id + "_" + index].href += "0/" + loadedChapters[v.chapter.id + "_" + index].joint_id + "/";
							}							
						}
					}
					
					// does the chapter comes with the array of pages? load them
					if(typeof v.pages != "undefined") {
						// if we have no pages for this chapter, set zeroPages to true, otherwise to false
						if(v.pages.length == 0)
						{
							loadedChapters[v.chapter.id + "_" + index].zeroPages == true;
						}
						else
						{
							loadedChapters[v.chapter.id + "_" + index].zeroPages == false;
						}
						$.each(v.pages, function(j, p){
							if (typeof loadedPages[p.id + "_" + index] == "undefined" ||  (typeof loadedPages[p.id + "_" + index] != "undefined" && dateTimeToDate(p.updated) > dateTimeToDate(loadedPages[p.id + "_" + index].updated))) {
								loadedPages[p.id + "_" + index] = p;
								loadedPages[p.id + "_" + index].slideUrl = index;
							}
						});
						
					}
				});
			});
			
			// the unchanged object
			return obj;
		}
		
		
		/**
		 * Get the comics
		 * 
		 * @return object {comics:[]}
		 */
		plugin.readerComics = function(opt) {
			var def = {
				direction: "asc",
				orderby: "name",
				per_page: 40,
				page: 1
			};
			var options = $.extend({}, def, options);
			
			var parameters = "/orderby/" + options.direction + "_" + options.orderby + "/per_page/" + options.per_page + "/page/" + options.page;
			processComics(get("/reader/comics" + parameters));
			var arr = orderBy(loadedComics, opt.orderby, (options.direction == "desc"))
			arr = arrayPage(arr, def.page, options.per_page);
			return {
				comics: arr
			};
		}
		
		
		/**
		 *	Returns an array of chapters
		 *	
		 *	@return array {chapters:[]}
		 */
		plugin.readerChapters = function(opt) {
			var def = {
				direction: "asc",
				orderby: "created",
				per_page: 40,
				page: 1	
			}
			var opt = $.extend({}, def, opt);
			
			var parameters = "/orderby/" + opt.direction + "_" + opt.orderby + "/per_page/" + opt.per_page + "/page/" + opt.page;
			
			processChapters(get("/reader/chapters" + parameters));
			var arr = orderBy(loadedChapters, opt.orderby, (opt.direction == "desc"))
			arr = arrayPage(arr, def.page, opt.per_page);
			return {
				chapters: arr
			};
		}
		
		
		plugin.readerComic = function(opt) {
			var def = {
				id: 0,
				stub: "",
				uniqid: "",
				orderby: "chapter",
				direction: "asc",
				slideUrl: plugin.settings.slideUrls[0],
				forceChapters: false,
				forceCache: false
			}
			var opt = $.extend({}, def, opt);
			if(opt.id > 0)
			{
				var check = checkComic(opt);
				if(check !== false)
				{
					var toReturn = check;
					var arrChapters = orderBy(toReturn.chapters, opt.orderby, (opt.direction == "desc"));
					toReturn.chapters = arrChapters;
					return toReturn;
				}
				
				// from this point and on is via ajax. did we ask for cache forcing?
				if(opt.forceCache)
				{
					// ah, but we already tried to get from cache, so here we say the requested resource doesn't exist
					// there can't be chapters without team or comic, so this is safe
					return false;
				}
				
				var parameters = "/id/" + opt.id + "/";
			}
			if(opt.stub != "") {
				
				$.each(loadedComics, function(index,value){
					if(
						(isNaN(parseInt(opt.stub)) || value.stub == parseInt(opt.stub)) &&
						(isNaN(parseInt(opt.slideUrl)) || value.slideUrl == parseInt(opt.slideUrl))
						)
						{
						alert(value.id);
						opt.id = value.id;
						return false;
					}
				});
				if(opt.id > 0)
				{
					return plugin.readerComic(opt);
				}
				
				var parameters = "/stub/" + opt.stub + "/";
				if(def.uniqid != "") {
					// compatibility up to FoOlSlide 0.7.6 - may God help this function
					parameters += "uniqid/" + opt.uniqid;
				}
			}
			
			var result = get("/reader/comic" + parameters, opt.slideUrl);
			// one comic to insert, especially if there was no chapter available
			if(typeof loadedComics[result[opt.slideUrl].comic.id + "_" + opt.slideUrl] == "undefined" || dateTimeToDate(result[opt.slideUrl].comic.updated) > dateTimeToDate(loadedComics[result[opt.slideUrl].comic.id + "_" + opt.slideUrl].updated)) {
				loadedComics[result[opt.slideUrl].comic.id + "_" + opt.slideUrl] = result[opt.slideUrl].comic;
				loadedComics[result[opt.slideUrl].comic.id + "_" + opt.slideUrl].slideUrl = opt.slideUrl;
			}

			if (result[opt.slideUrl].chapters.length == 0) {
				loadedComics[result[opt.slideUrl].comic.id + "_" + opt.slideUrl].zeroChapters = true;
			}
			else
			{					
				loadedComics[result[opt.slideUrl].comic.id + "_" + opt.slideUrl].zeroChapters = false;
				processChapters(result);
			}

			opt.id = result[opt.slideUrl].comic.id;
			opt.forceCache = true;
			return plugin.readerComic(opt);
		}
		
		var checkComic = function(opt) {
			// variable to check if something is in fact missing
			var missing = false;
			
			// suppose the comic is loaded
			if(typeof loadedComics[opt.id + "_" + opt.slideUrl] != "undefined")
			{
				
				var chapters = [];
				var result = {
					comics:[loadedComics[opt.id + "_" + opt.slideUrl]], 
					chapters:[]
				};
				
				// loop through each chapter to check they all have the team and comic
				$.each(loadedChapters, function(index, value){
					if(value.slideUrl == opt.slideUrl && value.comic_id == opt.id)
					{
						var currentChapter = checkChapter({
							id: value.id,
							slideUrl: opt.slideUrl,
							forcePages: false
						});
						if(currentChapter === false)
						{
							missing = true;
							return false;
						}
						else
						{			
							$.merge(result.chapters, currentChapter.chapters);
						}
					}
				});
				
				if(!missing)
				{
					return result;
				}
			}
			
			return false;
		}
		
		plugin.readerChapter = function(opt) {
			var def = {
				// if ID used, the rest of search values is ignored
				id: 0,
				comic_id: "",
				volume: "",
				chapter: "",
				subchapter: "",
				team_id: "",
				joint_id: "",
				forcePages: false,
				forceCache: false,
				slideUrl: plugin.settings.slideUrls[0]
			};
			
			var opt = $.extend({}, def, opt);

			var parameters = "";
			if(opt.id > 0)
			{
				// let's see if we have it in cache
				var check = checkChapter(opt)
				if(check !== false)
				{
					return check;
				}

				// from this point and on is via ajax. did we ask for cache forcing?
				if(opt.forceCache)
				{
					// ah, but we already tried to get from cache, so here we say the requested resource doesn't exist
					// there can't be chapters without team or comic, so this is safe
					return false;
				}
				parameters += "/id/" + opt.id;
			}
			else
			{
				
				// scary search function
				// if the user just didn't define enough data and this outputs, it's same as server as in coherence
				$.each(loadedChapters, function(index,value){
					if(
						(isNaN(parseInt(opt.comic_id)) || value.comic_id == parseInt(opt.comic_id)) &&
						(isNaN(parseInt(opt.volume)) || value.volume == parseInt(opt.volume)) &&
						(isNaN(parseInt(opt.chapter)) || value.chapter == parseInt(opt.chapter)) &&
						(isNaN(parseInt(opt.subchapter)) || value.subchapter == parseInt(opt.subchapter)) &&
						(isNaN(parseInt(opt.team_id)) || value.team_id == parseInt(opt.team_id)) &&
						(isNaN(parseInt(opt.joint_id)) || value.joint_id == parseInt(opt.joint_id)) &&
						(isNaN(parseInt(opt.slideUrl)) || value.slideUrl == parseInt(opt.slideUrl))
						)
						{
						opt.id = value.id;
						return false;
					}
				});
				if(opt.id > 0) 
				{
					return plugin.readerChapter(opt);
				}
						
				if(!isNaN(parseInt(opt.comic_id)))
					parameters += "/comic_id/" + opt.comic_id;
				if(!isNaN(parseInt(opt.volume)))
					parameters += "/volume/" + opt.volume;
				if(!isNaN(parseInt(opt.chapter)))
					parameters += "/chapter/" + opt.volume;
				if(!isNaN(parseInt(opt.subchapter)))
					parameters += "/subchapter/" + opt.volume;
				if(!isNaN(parseInt(opt.team_id)))
					parameters += "/team_id/" + opt.team_id;
				if(!isNaN(parseInt(opt.joint_id)))
					parameters += "/joint_id/" + opt.joint_id;
			}
			
			// adapt for processChapters()
			var result = get("/reader/chapter" + parameters, def.slideUrl);
			result[opt.slideUrl].chapters = [{
				comic: result[opt.slideUrl].comic,
				chapter: result[opt.slideUrl].chapter,
				teams: result[opt.slideUrl].teams,
				pages: result[opt.slideUrl].pages
			}];

			processChapters(result);
			opt.id = result[opt.slideUrl].chapter.id;
			opt.forceCache = true;
			// now just use the caching function to retrieve the value
			return plugin.readerChapter(opt);
			
		}
		
		/**
		 * Check if the chapter and its components are cached and return the complete array
		 */
		var checkChapter = function(opt){
			/*
			 * opt = {id, slideUrl, forcePages, forceCache}
			 */
			if(typeof loadedChapters[opt.id + "_" + opt.slideUrl] != "undefined")
			{
				var missing = false;
					
				// check if we have the comic
				if (typeof loadedComics[loadedChapters[opt.id + "_" + opt.slideUrl].comic_id + "_" + opt.slideUrl] == "undefined")
				{
					missing = true;
				}
					
				// check if we have the teams
				if(!missing && loadedChapters[opt.id + "_" + opt.slideUrl].team_id > 0 && typeof loadedTeams[loadedChapters[opt.id + "_" + opt.slideUrl].team_id + "_" + opt.slideUrl] == "undefined")
				{
					missing = true;								
				}
				
				// if we have teams, put it in a teams array
				if(!missing && loadedChapters[opt.id + "_" + opt.slideUrl].team_id > 0)
				{
					var teams = [loadedTeams[loadedChapters[opt.id + "_" + opt.slideUrl].team_id + "_" + opt.slideUrl]];
				}
					
				// check if we have the joint
				if(!missing && loadedChapters[opt.id + "_" + opt.slideUrl].joint_id > 0 && typeof loadedJoints[loadedChapters[opt.id + "_" + opt.slideUrl].joint_id + "_" + opt.slideUrl] == "undefined")
				{
					missing = true;								
				}
				
				// let's grab the teams in the joint, if it's a joint at all
				if(!missing && loadedChapters[opt.id + "_" + opt.slideUrl].joint_id > 0)
				{
					var teams = [];
					$.each(loadedJoints[loadedChapters[opt.id + "_" + opt.slideUrl].joint_id + "_" + opt.slideUrl].teams, function(index, value){
						if(typeof loadedTeams[value + "_" + opt.slideUrl] == "undefined")
						{
							// not found one of the teams, we need to reload this chapter
							missing = true;
							return false;
						}
						else
						{
							// found the team
							teams.push(loadedTeams[value + "_" + opt.slideUrl]);
						}
					});
				}
				
				// time to grab the pages, if there's any
				if(!missing)
				{
					// if forcePages is true, set missing false unless we find pages
					// if we already know there's no pages, then don't bother making ajax requests
					if((loadedChapters[opt.id + "_" + opt.slideUrl].zeroPages !== true) && opt.forcePages) {
						missing = true;
					}
					var pages = [];
					$.each(loadedPages, function(index, value){
						if(value.id == opt.id && value.slideUrl == opt.slideUrl) 
						{
							missing = false;
							pages.push(value);
						}
					});
				}
				
				// we've found all the needed! make the array now
				if(!missing)
				{
					var result = {
						comics: [loadedComics[loadedChapters[opt.id + "_" + opt.slideUrl].comic_id + "_" + opt.slideUrl]],
						chapters: [loadedChapters[opt.id + "_" + opt.slideUrl]],
						teams: teams
					};
					if(opt.forcePages)
					{
						result.pages = pages;
					}
					return result;
				}
				
			}
			
			// if we didn't return yet, it means mission failed
			return false;
		}
		
		
		plugin.readerTeam = function() {
		}
		
		
		plugin.readerJoint = function() {
		}


		
		
		
		/**
		 * Homebuilt makeArray because the jQuery one would give some messy stuff with id_url indexes
		 */
		var makeArray = function(obj) {
			var arr = [];
			$.each(obj, function(index, value){
				arr.push(value);
			});
			return arr;
		}
		
		/**
		 * converts MySQL DateTime to unix time.
		 */
		var dateTimeToDate = function(timestamp) {
			//function parses mysql datetime string and returns javascript Date object
			//input has to be in this format: 2007-06-05 15:26:02
			var regex=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
			var parts=timestamp.replace(regex,"$1 $2 $3 $4 $5 $6").split(' ');
			return new Date(parts[0],parts[1]-1,parts[2],parts[3],parts[4],parts[5]);
		}
		
		/**
		 * Get current unix time
		 */
		var unixtime = function(){
			return (new Date().getTime() * 0.001)|0;
		}
		
		
		/**
		 * beginning of functions to reorder the objects
		 */
		
		var orderBy = function(array, order, inverted) {
			switch(order) {
				case "name":
					var arr = orderbyName(array, inverted);
					break;
				case "edited":
					var arr = orderbyEdited(array, inverted);
					break;
				case "created":
					var arr = orderbyCreated(array, inverted);
					break;
				case "chapter":
					var arr = orderbyChapter(array, inverted);
					break;
				default:
					return false;
			}
			return arr;
		}
		
		/**
		 * Returns the right slice of the array by knowing page and per_page
		 */
		var arrayPage = function(arr, page, per_page) {
			var result = [];
			var pagemin = (page * per_page) - per_page;
			var pagemax = (page * per_page);
			var j = 0;
			for(var i = pagemin; i < pagemax && i < arr.length; i++) {
				result.push(arr[i]);
			}
			return result;
		}
		
		var orderbyEdited = function(obj, desc) {
			var arr = makeArray(obj);
			arr.sort(function (a,b) {
				return dateTimeToDate(a.updated) - dateTimeToDate(b.updated)
			});
			if(desc) arr.reverse();
			return arr;
		}
		
		var orderbyCreated = function(obj, desc) {
			var arr = makeArray(obj);
			arr.sort(function (a,b) {
				return dateTimeToDate(a.created) - dateTimeToDate(b.created)
			});
			if(desc) arr.reverse();
			return arr;
		}
		
		var orderbyName = function(obj, desc) {
			var arr = makeArray(obj);
			arr.sort(function (a,b) {
				return a.name < b.name
			});
			if(desc) arr.reverse();
			return arr;
		}
		
		var orderbyChapter = function(arr, desc) {
			arr.sort(function(a,b){
				function addNumbers(val) {
					if (val < 10)
						return "0000" + val;
					if (val < 100)
						return "000" + val;
					if (val < 1000)
						return "000" + val;
					if (val < 10000)
						return "0" + val;
				}
				var aa = {};
				var bb = {};
				aa.volume = addNumbers(parseInt(a.volume));
				bb.volume = addNumbers(parseInt(b.volume));
				aa.chapter = addNumbers(parseInt(a.chapter));
				bb.chapter = addNumbers(parseInt(b.chapter));
				aa.subchapter = addNumbers(parseInt(a.subchapter));
				bb.subchapter = addNumbers(parseInt(b.subchapter));
				if(aa.volume + "/" + aa.chapter + "/" + aa.subchapter < bb.volume + "/" + bb.chapter + "/" + bb.subchapter)
					return -1;
				if(aa.volume + "/" + aa.chapter + "/" + aa.subchapter > bb.volume + "/" + bb.chapter + "/" + bb.subchapter)
					return 1;
				return 0;
			});
			if(desc) arr.reverse();
			return arr;
		}
		

		/**
		 * Loop over each selected FoOlSlide and return them in an object indexed
		 * by the URL of the site
		 */
		var get = function(apiRequest, slideUrl) {
			var result = {};
			if(typeof slideUrl != "undefined") {
				var def = {
					slideUrls : [slideUrl]
				}
			}
			else
			{
				var def = {
					slideUrls : plugin.settings.slideUrls
				}
			}
			$.each(def.slideUrls, function(index, value){
				$.ajax({
					url: value + "/api" + apiRequest,
					async: false,
					dataType: "json",
					success: function(data){
						result[value] = data;
					}
				});
			});
			return result;
		}

		// fire up the plugin!
		// call the "constructor" method
		plugin.init();

	}

	// add the plugin to the jQuery.fn object
	$.fn.foolslide = function(options) {

		// iterate through the DOM elements we are attaching the plugin to
		return this.each(function() {

			// if plugin has not already been attached to the element
			if (undefined == $(this).data('foolslide')) {

				// create a new instance of the plugin
				// pass the DOM element and the user-provided options as arguments
				var plugin = new $.foolslide(this, options);

				// in the jQuery version of the element
				// store a reference to the plugin object
				// you can later access the plugin and its methods and properties like
				// element.data('pluginName').publicMethod(arg1, arg2, ... argn) or
				// element.data('pluginName').settings.propertyName
				$(this).data('foolslide', plugin);

			}

		});

	}

})(jQuery);

(function($) {
	$.foolslideui = function(element, options) {

		var defaults = {
			slideUrls: [],
			activateSidebar: true,
			activateCenter: true,
			standAlone: false,
			afterSidebarUpdate: function() {}
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
		}
		
		plugin.getLatest = function(){
			var latest = foolslide.readerChapters({
				direction:"desc"
			});
			var current_comic_id = 0;
			var current_comic = {};
			var result = [];
			var preresult = {};
			$.each(latest.chapters, function(index, value){
				
				if(value.comic_id != current_comic_id)
				{
					current_comic_id = value.comic_id
					result.push(preresult);
					preresult = {};
					preresult.elements = [];
					current_comic = foolslide.readerComic({
						id: value.comic_id
					}).comics[0];
					preresult.group = {
						href: current_comic.href,
						text: current_comic.name,
						title: current_comic.name,
						onClick: "displayComic(this, " + current_comic.id + ")"
					};
					
					preresult.group.plus = {
						href: current_comic.href,
						title: current_comic.name,
						onClick: "infoComic(this, " + current_comic.id + ")"
					};
				}
				
				preresult.elements.push({
					text: value.title,
					href: value.href,
					title: value.title,
					onClick: "displayChapter(this, " + value.id + ")"
				});				
			});
			
			result.push(preresult)
			
			updateSidebar(result);
			return false;
		}
		
		var updateSidebar = function(arr) {
			$("#sidebar .items").animate({
				position: "relative",
				top: "130%"
			}, ($("#dynamic_sidebar").html().length > 8?1000:0), 
				function(){
					var echo = '';
					$.each(arr, function(index, value){
						if (typeof value.group != "undefined")
						{
							echo += '<ul>';
							echo += '	<li class="group">';
							if (typeof value.group.plus != "undefined") {
								echo += '		<div class="plus">';
								echo += '			<a href="' + value.group.plus.href + '" onClick="$.foolslideui.' + value.group.plus.onClick + '" title="' + value.group.plus.title +'">+</a>';
								echo += '		</div>';
							}
							echo += '		<div class="text"><a href="' + value.group.href + '" onClick="$.foolslideui.' + value.group.onClick + ';return false;" title="' + value.group.title + '">' + value.group.text + '</a></div>';
							echo +=	'	</li>';
						}
					
						if(typeof value.info != "undefined")
						{
							echo += '	<li class="info">';
							if(typeof value.info.image != "undefined")
							{
								echo += '		<div class="image">';
								echo += '			<a href="' + value.info.image.href + '" onClick="$.foolslideui.' + value.info.image.onClick + '" title="' + value.info.image.title +'">+</a>';
								echo += '		</div>';
							}
							echo += '		<div class="text">' + value.info.text + '</div>';
							echo +=	'	</li>';
						}
				
						if(typeof value.elements != "undefined") 
						{
							$.each(value.elements, function(i, v){
								echo += '	<li class="element">';
								if (typeof v.plus != "undefined") {
									echo += '		<div class="plus">';
									echo += '			<a href="' + v.plus.href + '" onClick="$.foolslideui.' + v.plus.onClick + '" title="' + v.plus.title +'">+</a>';
									echo += '		</div>';
								}
								echo += '		<div class="text"><a href="' + v.href + '" onClick="$.foolslideui.' + v.onClick + '" title="' + v.title + '">' + v.text + '</a></div>';
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
	$('#container').foolslideui({
		slideUrls:[slideUrl]
	});
	
	var foolslideui = jQuery('#container').data('foolslideui');
	
	$.foolslideui.getLatest();
});
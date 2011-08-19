(function($) {

	// here we go!
	$.foolslide = function(element, options) {

		var defaults = {

			slideUrls: ["http://localhost/slide"]
		//onFoo: function() {}

		}

		var plugin = this;


		plugin.settings = {}

		var $element = $(element),
		element = element;

		// the "constructor" method that gets called when the object is created
		plugin.init = function() {
			plugin.settings = $.extend({}, defaults, options);

		// code goes here

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
		
		var processChapters = function(obj) {
			// cycle through the slideUrls
			$.each(obj, function(index, value){
				// cycle through each chapter data
				$.each(value.chapters, function(i, v){
					
					// check if there's the comic entry in this chapter and in case load if new
					if(typeof v.comic != "undefined" || typeof loadedComics[v.comic.id + "_" + index] == "undefined" || dateTimeToDate(v.comic.updated) > dateTimeToDate(loadedComics[v.comic.id + "_" + index].updated)) {
						loadedComics[v.comic.id + "_" + index] = v.comic;
						loadedComics[v.comic.id + "_" + index].slideUrl = index;
					}
					
					// check if there's an array of teams in this chapter and in case load if any are new
					if(typeof v.teams != "undefined") {
						// teams always come as an array
						$.each(v.teams, function(j,t){
							if (typeof loadedTeams[t.id + "_" + index] == "undefined" ||  (typeof loadedTeams[t.id + "_" + index] != "undefined" && dateTimeToDate(t.updated) > dateTimeToDate(loadedTeams[t.id + "_" + index].updated))) {
								loadedTeams[t.id + "_" + index] = t;
								loadedTeams[t.id + "_" + index].slideUrl = index;
							}
						});
					}
					
					// store those joints
					if(v.teams.length > 1) {
						loadedJoints[v.joint_id + "_" + index] = {
							id: v.joint_id, 
							slideUrl: index, 
							teams:[]
						};
						$.each(v.teams, function(j,t){
							loadedJoints[v.joint_id + "_" + index].teams.push(t.id);
						});
					}
					
					// almost surely we have a chapter
					if(typeof v.chapter != "undefined" && (typeof loadedChapters[v.chapter.id + "_" + index] == "undefined" || dateTimeToDate(v.chapter.updated) > dateTimeToDate(loadedChapters[v.chapter.id + "_" + index].updated))) {
						loadedChapters[v.chapter.id + "_" + index] = v.chapter;
						loadedChapters[v.chapter.id + "_" + index].slideUrl = index;
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
			if(options.orderby == "name")
				var arr = orderbyName(loadedComics, (options.direction == "desc"));
			if(options.orderby == "edited")
				var arr = orderbyEdited(loadedComics, (options.direction == "desc"));
			if(options.orderby == "created")
				var arr = orderbyCreated(loadedComics, (options.direction == "desc"));
			arr = arrayPage(arr, def.page, options.per_page);
			return {
				comics: arr
			};
		}
		
		
		plugin.readerChapters = function(opt) {
			var def = {
				direction: "asc",
				orderby: "name",
				per_page: 40,
				page: 1	
			}
			var opt = $.extend({}, def, opt);
			
			var parameters = "/orderby/" + opt.direction + "_" + opt.orderby + "/per_page/" + opt.per_page + "/page/" + opt.page;
			
			processChapters(get("/reader/chapters" + parameters));
			
			if(opt.orderby == "name")
				var arr = orderbyName(loadedChapters, (opt.direction == "desc"));
			if(opt.orderby == "edited")
				var arr = orderbyEdited(loadedChapters, (opt.direction == "desc"));
			if(opt.orderby == "created")
				var arr = orderbyCreated(loadedChapters, (opt.direction == "desc"));
			arr = arrayPage(arr, def.page, opt.per_page);
			return arr;
		}
		
		
		plugin.readerComic = function(opt) {
			var def = {
				id: 0,
				stub: "",
				uniqid: "",
				direction: "asc",
				slideUrl: plugin.settings.slideUrls[0],
				forceChapters: false,
				forceCache: false
			}
			var opt = $.extend({}, def, opt);
			if(def.id > 0)
			{
				var check = checkComic(opt);
				if(check !== false)
				{
					return check;
				}
				
				var parameters = "/id/" + opt.id + "/";
			}
			if(def.stub != "") {
				var parameters = "/stub/" + opt.id + "/";
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

			if (result[opt.slideUrl]) {
    
			}
			processChapters(result);


			var arrChapters = orderbyChapter(loadedComics, (opt.direction == "desc"));
			return {
				comics: [], 
				chapters: arrChapters
			}
		}
		
		var checkComic = function(opt) {
			if(typeof loadedComics[opt.id + "_" + opt.slideUrl] != "undefined")
			{
				
		}
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
					console.log(loadedTeams);
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
					$.each(loadedJoints, function(index, value){
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
			arr.sort(byEdited);
			if(desc) arr.reverse();
			return arr;
		}
		
		var orderbyCreated = function(obj, desc) {
			var arr = makeArray(obj);
			arr.sort(byCreated);
			if(desc) arr.reverse();
			return arr;
		}
		
		var orderbyName = function(obj, desc) {
			var arr = makeArray(obj);
			arr.sort(byName);
			if(desc) arr.reverse();
			return arr;
		}
		
		var orderbyChapter = function(obj, desc) {
			var arr = makeArray(obj);
			arr.sort(byChapter);
			if(desc) arr.reverse();
			return arr;
		}
		
		/**
		 * Some specific comparison functions follow
		 */
		
		var byEdited = function(a,b)
		{
			return dateTimeToDate(a.updated) < dateTimeToDate(b.updated)
		}
		
		var byCreated = function(a,b)
		{
			return dateTimeToDate(a.created) < dateTimeToDate(b.created)
		}
		
		var byName = function(a,b)
		{
			return a.name < b.name;
		}
		
		var byChapter = function(a,b)
		{
			return a.volume < b.volume && a.chapter < b.chapter && a.subchapter < b.subchapter;
		}
		/**
		 * End of functions to reorder objects
		 */
		

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


jQuery(document).ready(function(){
	var foolslide = new $.foolslide();
	var comics = foolslide.readerComics();
	console.log(comics);
	
	var latest = foolslide.readerChapters();
	console.log(latest);
	
	var chapter = foolslide.readerChapter({
		comic_id:1,
		chapter:23,
		subchapter:0
	});
	console.log(chapter);
});
// jQuery Plugin Boilerplate
// A boilerplate for jumpstarting jQuery plugins development
// version 1.1, May 14th, 2011
// by Stefan Gabos

// remember to change every instance of "pluginName" to the name of your plugin!
(function($) {

	// here we go!
	$.foolslide = function(element, options) {

		// plugin's default options
		// this is private property and is  accessible only from inside the plugin
		var defaults = {

			slideUrls: ["http://localhost/slide"]

			// if your plugin is event-driven, you may provide callback capabilities for its events.
			// execute these functions before or after events of your plugin, so that users may customize
			// those particular events without changing the plugin's code
			//onFoo: function() {}

		}

		// to avoid confusions, use "plugin" to reference the current instance of the object
		var plugin = this;

		// this will hold the merged default, and user-provided options
		// plugin's properties will be available through this object like:
		// plugin.settings.propertyName from inside the plugin or
		// element.data('pluginName').settings.propertyName from outside the plugin, where "element" is the
		// element the plugin is attached to;
		plugin.settings = {}

		var $element = $(element),  // reference to the jQuery version of DOM element the plugin is attached to
		element = element;        // reference to the actual DOM element

		// the "constructor" method that gets called when the object is created
		plugin.init = function() {

			// the plugin's final properties are the merged default and user-provided options (if any)
			plugin.settings = $.extend({}, defaults, options);

			// code goes here

		}
		
		var loadedComics = {};
		var loadedChapters = {};
		var loadedTeams = {};
		var loadedJoints = {};
		var loadedUsers = {};
		var loadedMembers = {};

		var unixtime = function(){
			return (new Date().getTime() * 0.001)|0;
		}

		plugin.cleanCache = function() {
			loadedComics = {};
			loadedChapters = {};
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
						loadedJoints[v.joint_id + "_" + index] = {id: v.joint_id, slideUrl: index, teams:[]};
						$.each(v.teams, function(j,t){
							loadedJoints[v.joint_id + "_" + index].teams.push(t.id);
						});
					}
					
					// almost surely we have a chapter
					if(typeof v.chapter != "undefined" && typeof loadedChapters[v.chapter.id + "_" + index] == "undefined" || dateTimeToDate(v.chapter.updated) > dateTimeToDate(loadedComics[v.chapter.id + "_" + index].updated)) {
						loadedChapters[v.chapter.id + "_" + index] = v.chapter;
						loadedChapters[v.chapter.id + "_" + index].slideUrl = index;
					}
				});
			});
			
			// the unchanged object
			return obj;
		}
		
		plugin.readerComics = function(options) {
			var defaults = {
				direction: "asc",
				orderby: "name",
				per_page: 40,
				page: 1
			};
			var options = $.extend({}, defaults, options);
			
			var parameters = "/orderby/" + options.direction + "_" + options.orderby + "/per_page/" + options.per_page + "/page/" + options.page;
			processComics(get("/reader/comics" + parameters));
			if(options.orderby == "name")
				var arr = orderbyName(loadedComics, (options.direction == "desc"));
			if(options.orderby == "edited")
				var arr = orderbyEdited(loadedComics, (options.direction == "desc"));
			if(options.orderby == "created")
				var arr = orderbyCreated(loadedComics, (options.direction == "desc"));
			arr = arrayPage(arr, defaults.page, options.per_page);
			return {comics: arr};
		}
		
		plugin.readerComic = function(options) {
			var defaults = {
				id: 0,
				stub: "",
				uniqid: "",
				direction: "asc",
				slideUrl: defaults.slideUrls[1]
			}
			var options = $.extend({}, defaults, options);
			if(defaults.id != 0)
				var parameters = "/id/" + options.id + "/";
			if(defaults.stub != "") {
				var parameters = "/stub/" + options.id + "/";
				if(defaults.uniqid != "") {
					// compatibility for FoOlSlide 0.7.6 may God help this function
					parameters += "uniqid/" + options.uniqid;
				}
			}
			var result = processChapters(get("/reader/comic" + parameters, slideUrl));
			
			// one comic to insert, especially if there was no chapter available
			$.each(result, function(index, value){
				if(typeof loadedComics[value.comic.id + "_" + index] == "undefined" || dateTimeToDate(value.comic.updated) > dateTimeToDate(loadedComics[value.comic.id + "_" + index].updated)) {
					loadedComics[value.comic.id + "_" + index] = value.comic;
					loadedComics[value.comic.id + "_" + index].slideUrl = index;
				}
			});
			var arrChapters = orderbyChapter(loadedComics, (options.direction == "desc"));
			return {comics: [], chapters: arrChapters}
		}
		
		
		plugin.readerChapters = function(options) {
			var defaults = {
				direction: "asc",
				orderby: "name",
				per_page: 40,
				page: 1	
			}
			var options = $.extend({}, defaults, options);
			
			var parameters = "/orderby/" + options.direction + "_" + options.orderby + "/per_page/" + options.per_page + "/page/" + options.page;
			
			processChapters(get("/reader/chapters" + parameters));
			
			if(options.orderby == "name")
				var arr = orderbyName(loadedChapters, (options.direction == "desc"));
			if(options.orderby == "edited")
				var arr = orderbyEdited(loadedChapters, (options.direction == "desc"));
			if(options.orderby == "created")
				var arr = orderbyCreated(loadedChapters, (options.direction == "desc"));
			arr = arrayPage(arr, defaults.page, options.per_page);
			return arr;
		}
		
		plugin.readerChapter = function() {
		}
		
		
		plugin.readerTeam = function() {
		}
		
		
		plugin.readerJoint = function() {
		}


		
		
		/**
		 * beginning of functions to reorder the objects
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
		
		var makeArray = function(obj) {
			var arr = [];
			$.each(obj, function(index, value){
				arr.push(value);
			});
			return arr;
		}
		
		var dateTimeToDate = function(timestamp) {
			//function parses mysql datetime string and returns javascript Date object
			//input has to be in this format: 2007-06-05 15:26:02
			var regex=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
			var parts=timestamp.replace(regex,"$1 $2 $3 $4 $5 $6").split(' ');
			return new Date(parts[0],parts[1]-1,parts[2],parts[3],parts[4],parts[5]);
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
				var defaults = {
					slideUrls : [slideUrl]
				}
			}
			$.each(defaults.slideUrls, function(index, value){
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
});
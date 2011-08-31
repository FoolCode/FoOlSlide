(function($) {

	$.foolproofr = function(element, options) {

		var defaults = {
		}

		var plugin = this;

		plugin.settings = {
			fitImage: false,
			updateUrl: ""
		}
		
		/*
		  
		Should have an array of things that are to be sync'd
		Should download the updates automatically in case someone else is working on
		The array should be intact until the server gives a successful response
		
		Update will actually be a sync, so there must be a single PHP function routing everything
		Every update we're downloading 
		 
		 
		addComment()
		Add a proofreader's/translators comment
		
		updateTranslation()
		A translation is just a special kind of comment, versioned just like comments, but tagged as translation
		
		removeBox()
		createBox()
		
		Array sent:
		{
			timestamp: // just to retrieve the updates since the latest sync
			comments: [
						{
							type: // comment/translation
							timestamp - use server
							user_id - use server
							parent_id: // if 0 it means this is a new comment/translation
							top:
							left:
							width:
							height: // version the sizes in a new translation with parent_id
							image_width // 
							image_height // version these, because later we'll grab the %
							
						}
					]
		}
		 
		   
		 */

		var $element = $(element),
		element = element;
		
		var currentId = 0;

		plugin.init = function() {
			plugin.settings = $.extend({}, defaults, options);
			
			$element.css({
				position: "relative"
			});
			if(plugin.settings.fitImage)
			{
				$element.css({
					width: $element.find("img").width() + "px",
					height: $element.find("img").height() + "px"
				});
			}
			$element.find("img").mousedown(function(e){
				e.preventDefault()
			});
			
			toggleMousedown(true);
			
		}
		
		var toggleMousedown = function(bool) {
			if(bool)
			{
				$(document).unbind('mouseup');
				$element.unbind('mousemove');
				$element.mousedown(function(e){ 
					
					
					if($(e.target).hasClass("foolproofr_dragger"))
					{
						e.preventDefault();
						draggingDragger(e);
						return false;
					}
					
					if($(e.target).hasClass("foolproofr_resizer"))
					{
						e.preventDefault();
						draggingResizer(e);
						return false;
					}
					
					if($(e.target).hasClass("foolproofr_box") || $(e.target).parents(".foolproofr_box").length > 0)
					{
						
					}
					else if(e.which == 1)
					{
						e.preventDefault(); 
						draggingBox(e);
						return false;
					}
				});
			}
			else
			{
				$element.unbind('mousedown');
			}
		}

		var draggingBox = function(e) {
			toggleMousedown(false);
			var offset = $element.offset();
			var urelativeX, urelativeY;
			var relativeX = (e.pageX - offset.left);
			var relativeY = (e.pageY - offset.top);
			var tempbox = $('<div class="foolproofr_tempbox">').css({
				position: "absolute",
				left: relativeX + "px",
				top: relativeY + "px",
				width:"30px",
				height:"30px",
				border: "1px dotted red"
			}).appendTo($element);
			
			$element.mousemove(function(u){
				urelativeX = (u.pageX - offset.left);
				urelativeY = (u.pageY - offset.top);
				
				// the bonduaries are touched easily and the mouse lag makes the box go out
				// let's put a hard limit to the width and height if they touch the border
				if(urelativeX > $element.width()-2)
				{
					urelativeX = $element.width() - 2;
				}
				if(urelativeY > $element.height()-2)
				{
					urelativeY = $element.height() - 2;
				}
				if(urelativeX - relativeX > 60 && urelativeY - relativeY > 60 && urelativeX < $element.width() && urelativeY < $element.height())
				{
					tempbox.css({
						width: (urelativeX - relativeX) + "px",
						height: (urelativeY - relativeY) + "px",
						border: "1px dashed #5BD84F"
					});
				}
				else
				{
					tempbox.css({
						width: "30px",
						height: "30px",
						border: "1px dashed red"
					});
				}
			});
			$(document).mouseup(function(u){
				toggleMousedown(true);
				tempbox.remove();
				if(urelativeX - relativeX > 60 && urelativeY - relativeY > 60)
				{
					createBox(relativeY, relativeX, urelativeX - relativeX, urelativeY - relativeY);
				}
			});
		}
		
		// the top bar for dragging the boxes
		var draggingDragger = function(e) {
			toggleMousedown(false);
			var elem = $(e.target);
			var elemBox = elem.parents(".foolproofr_box");
			var elemBoxTop = parseInt(elemBox.css('top').replace('px', ''));
			var elemBoxLeft = parseInt(elemBox.css('left').replace('px', ''));
			var offset = $element.offset();
			var urelativeX, urelativeY;
			var relativeX = (e.pageX - offset.left);
			var relativeY = (e.pageY - offset.top);
			
			$element.mousemove(function(u){
				urelativeX = (u.pageX - offset.left);
				urelativeY = (u.pageY - offset.top);
				var relTop = elemBoxTop + urelativeY - relativeY;
				var relLeft = elemBoxLeft + urelativeX - relativeX;
				if(relTop <= 0)
				{
					relTop = 0;
				}
				
				if(relTop + elemBox.height() > $element.height())
				{
					relTop = $element.height() - elemBox.height() - 2;
				}
				
				if(relLeft <= 0)
				{
					relLeft = 0;
				}
				
				if(relLeft + elemBox.width() > $element.width())
				{
					relLeft = $element.width() - elemBox.width() - 2;
				}
									
				elemBox.css({
					top: relTop + "px",
					left: relLeft + "px"
				});
			});
			
			$(document).mouseup(function(u){
				toggleMousedown(true);
			});
		}
		
		var draggingResizer = function(e){
			toggleMousedown(false);
			var elem = $(e.target);
			var elemBox = elem.parents(".foolproofr_box");
			var elemBoxWidth = elemBox.width();
			var elemBoxHeight = elemBox.height();
			var elemBoxTop = parseInt(elemBox.css('top').replace('px', ''));
			var elemBoxLeft = parseInt(elemBox.css('left').replace('px', ''));
			var offset = $element.offset();
			var urelativeX, urelativeY;
			var relativeX = (e.pageX - offset.left);
			var relativeY = (e.pageY - offset.top);
			
			$element.mousemove(function(u){
				urelativeX = (u.pageX - offset.left);
				urelativeY = (u.pageY - offset.top);
				var relHeight = elemBoxHeight + urelativeY - relativeY;
				var relWidth = elemBoxWidth + urelativeX - relativeX;
				
				if(relWidth <= 60)
				{
					relWidth = 60;
				}
				
				if(relHeight <= 60)
				{
					relHeight = 60;
				}
				
				if(relWidth + elemBoxLeft >= $element.width())
				{
					relWidth = $element.width() - elemBoxLeft - 2;
				}
				
				if(relHeight + elemBoxTop >= $element.height())
				{
					relHeight = $element.height() - elemBoxTop - 2;
				}
									
				elemBox.css({
					width: relWidth + "px",
					height: relHeight + "px"
				});
				
				elemBox.find(".foolproofr_textarea").css({
					height: (elemBox.height() - elemBox.find(".foolproofr_dragger").height() - 8) + "px"
				});
			});
			
			$(document).mouseup(function(u){
				toggleMousedown(true);
			});
		}
		
		var createBox = function(top, left, width, height){
			var boxElem = $("<div />").addClass('foolproofr_box').css({
				top: top + "px",
				left: left + "px",
				width: width + "px",
				height: height + "px"
			});
			var remover = $("<div />").addClass("foolproofr_remover").click(function(){
				
			}).html("X");
			var dragger = $("<div />").addClass("foolproofr_dragger").append(remover).append("Necrophantasia");
			var resizer = $("<div />").addClass("foolproofr_resizer");
			var textarea = $("<textarea />").addClass("foolproofr_textarea");
			boxElem.append(dragger).append(resizer).append(textarea);
			
			boxElem.appendTo($element);
			boxElem.find(".foolproofr_textarea").css({
				height: (boxElem.height() - dragger.height() - 8) + "px"
			});
			
			// update all textarea focus function
			$(".foolproofr_textarea").each(function(index, el) {
				$(el).focus(function(a){
					$(a.target).parents(".foolproofr_box").addClass('focused');
				});
			});
			
			// the focusout too
			$(".foolproofr_textarea").each(function(index, el) {
				$(el).focusout(function(a){
					$(a.target).parents(".foolproofr_box").removeClass('focused');
				});
			});
			
			// put the focus on the just created box
			boxElem.find(".foolproofr_textarea").focus();
		}
		
		var removeBox = function() {
			
		}

		plugin.init();

	}

	$.fn.foolproofr = function(options) {

		return this.each(function() {
			if (undefined == $(this).data('foolproofr')) {
				var plugin = new $.foolproofr(this, options);
				$(this).data('foolproofr', plugin);
			}
		});

	}

})(jQuery);
(function($) {

	$.foolproofr = function(element, options) {

		var defaults = {
		}

		var plugin = this;

		plugin.settings = {
			fitImage: false,
			updateUrl: "",
			chapter_id: 0,
			page_number: 0
		}

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
			
			sync();
			
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
				width:"40px",
				height:"40px",
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
				if(urelativeX - relativeX > 40 && urelativeY - relativeY > 40 && urelativeX < $element.width() && urelativeY < $element.height())
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
						width: "40px",
						height: "40px",
						border: "1px dashed red"
					});
				}
			});
			$(document).mouseup(function(u){
				toggleMousedown(true);
				tempbox.remove();
				if(urelativeX - relativeX > 40 && urelativeY - relativeY > 40)
				{
					var sendOpt = {
						user_id: plugin.settings.user_id,
						user_name: plugin.settings.user_name,
						type: 1,
						text: '',
						top: relativeY,
						left: relativeX,
						width: urelativeX - relativeX,
						height: urelativeY - relativeY,
						image_width: $element.find("img").width(),
						image_height: $element.find("img").height()
					}
					var boxElem = createBox(sendOpt);
					var ticket = sendTransproof(sendOpt);
					boxElem.data('ticket', ticket);
					
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
				
				if(relWidth <= 40)
				{
					relWidth = 40;
				}
				
				if(relHeight <= 40)
				{
					relHeight = 40;
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
				
			});
			
			$(document).mouseup(function(u){
				toggleMousedown(true);
			});
		}
		
		var createBox = function(opt){
			
			var def = {
				send: false,
				top: 0,
				left: 0,
				width: 0,
				height: 0
			}
				
			var pref = $.extend({}, def, opt);
			
			
			var boxElem = $("<div />").addClass('foolproofr_box').css({
				top: pref.top + "px",
				left: pref.left + "px",
				width: pref.width + "px",
				height: pref.height + "px"
			}).data('transproof', pref);
			
			var remover = $("<div />").addClass("foolproofr_remover").click(function(e){
				// prepare to send deletion to server
				var tp = $(e.target).parents(".foolproofr_box").data('transproof');
				var remPref = {
					related_transproof_id: tp.id,
					deleted: 1,
					type: 1
				}
				sendBox(remPref);
				removeBox(remPref);
			}).html("X");
			var dragger = $("<div />").addClass("foolproofr_dragger").append(remover).append(pref.user_name);
			var resizer = $("<div />").addClass("foolproofr_resizer");
			var textarea = $("<textarea />").addClass("foolproofr_textarea");
			boxElem.append(dragger).append(resizer).append(textarea);
			
			boxElem.appendTo($element);
			
			
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
			
			return boxElem;
		}
		
		var removeBox = function(objj) {
			//alert(objj.related_transproof_id);
			var el = findBoxByID(objj.related_transproof_id);
			$(el).hide();
		}
		
		var ticketCounter = 0;
		var newTransproofs = [];
		var sendTransproof = new function(objj) {
			objj.ticket = ticketCounter++;
			newTransproofs.push(objj);
			return ticketCounter;
		}
		
		var lastSync = 0;
		
		
		var sync = function() {
			var data = {
				//update: [objj],
				chapter_id: plugin.settings.chapter_id,
				pagenum: plugin.settings.page_number
			};
			
			$.each(newTransproofs, function(index, value){
				
				}); 
			
			$.ajax({
				type: 'POST',
				data: true,
				async: false,
				dataType: 'json',
				url: plugin.settings.updateUrl,
				success: function(data, textStatus, jqXHR) {
					$.each(data.sync, function(index, value){
						transproofs.push(value);
						processSync(value);
					});
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
				//alert(errorThrown);
				}
			});
		}
		
		var processSync = function(objj) {
			// here basically only box creation happens
			if(objj.related_transproof_id == 0)
			{
				
				createBox(objj);
				
				if(objj.transproofs instanceof Array)
				{
					// recursive is cool
					processSync(objj.transproofs);
				}
			}
			else // here we get modifications for the boxes, we won't deal with comments here
			{
				// we're getting transproofs arrays
				$.each(objj, function(index, value){
					if(value.deleted == 1)
					{
						removeBox(value);
					}
				});
				
			}
		}
		
		var findBoxByID = function(id) {
			var result;
			$(".foolproofr_box").each(function(index, el){
				var objj = $(el).data('transproof');
				if(objj.id == id)
				{
					result = el;
					return false;
				}
			});

			if(result != undefined)
				return result;
			return false;
		}
		
		var findBoxByTicket = function(ticket) {
			var result;
			$(".foolproofr_box").each(function(index, el){
				var objj = $(el).data('ticket');
				if(objj == ticket)
				{
					result = el;
					return false;
				}
			});

			if(result != undefined)
				return result;
			return false;
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
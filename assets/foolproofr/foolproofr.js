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
		
		
		
		
		
		
		
		
		/******************
		 *   USER-TRIGGERED
		 *	FUNCTIONS
		 ******************/
		
		
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
					var ticket = boxElem.data('ticket')
					ticket.push(sendTransproof(sendOpt));
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
		
		
		
		
		
		
		
		
		
		
		
		
		
		/******************
		 *   INTERFACE CREATION
		 *	FUNCTIONS
		 ******************/
		
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
				sendTransproof(remPref);
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
			var el = findTransproofByID(objj.related_transproof_id);
			$(el).hide();
		}
		
		var moveBox = function(objj) {
			var el = findTransproofByID(objj.related_transproof_id);
		}
		
		
		
		
			
		
		
		
		
		
		
		/******************
		 *   NETWORK
		 *	FUNCTIONS
		 ******************/
		
		var ticketCounter = 0;
		var newTransproofs = [];
		var sendTransproof = function(objj) {
			objj.ticket = ticketCounter++;
			newTransproofs.push(objj);
			return ticketCounter;
		}
		
		var tempSyncID = 0;
		var syncID = 0;		
		var compareSyncID = function(a){
			if(a > syncID)
			{
				tempSyncID = a;
				return true;
			}
			return false;
		}
		
		var updateSyncID = function()
		{
			 syncID = tempSyncID;
		}
		
		var sync = function(manual) {
			var data = {
				update: newTransproofs,
				chapter_id: plugin.settings.chapter_id,
				pagenum: plugin.settings.page_number
			};
			
			$.ajax({
				type: 'POST',
				data: data,
				async: false,
				dataType: 'json',
				url: plugin.settings.updateUrl,
				success: function(data, textStatus, jqXHR) {
					// let's try breaking the millisecond
					$.each(data.sync, function(index, value){
						processSync(value);
					});
					newTransproofs = [];
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					
				},
				complete: function(jqXHR, textStatus)
				{
					if(manual !== true)
						setTimeout(function(){
							sync()
						}, 3000);
				}
			});
		}
		
		var processSync = function(objj) {
			// here basically only box creation happens
			if(objj.related_transproof_id == 0)
			{
				if(compareSyncID(objj.id))
				{
					createBox(objj);
				}
				
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
					
					// if it's a translation
					if(value.type == 1 && compareSyncID(value.id))
					{
						// moving a translation box
						if(value.width > 0)
						{
							moveBox(value);
						}
						
						// change the translation
						if(value.text != "")
						{
							textBox(value);
						}
						
						if(value.deleted === 1)
						{
							removeBox(value);
						}
					}
					
					
					if(value.transproofs instanceof Array)
					{
						// recursive is cool
						processSync(value.transproofs);
					}
				});
				
			}
			
			updateSyncID();
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		/******************
		 *   TRASVERSING
		 *	FUNCTIONS
		 ******************/
		
		var findTransproofByID = function(id) {
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
		
		var findTransproofByTicket = function(ticket) {
			var result;
			$(".foolproofr_box").each(function(index, el){
				var objj = $(el).data('ticket');
				$.each(objj, function(i,v){
					if(v == ticket)
					{
						result = el;
						return false;
					}
				})
				if(result != undefined)
					return false;
			});

			if(result != undefined)
				return result;
			return false;
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
		
		var compareDateTime = function(a, b)
		{
			return dateTimeToDate(a.created) - dateTimeToDate(b.created)
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
<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed'); ?>

<div class="table">
	<?php if ($imagick_optimize): ?>
		<div style="margin:0 10px 15px 0;">
			<h3><?php echo _('Optimize thumbnails') ?></h3> <span class="label important">Important</span>
			<p><?php echo _('It has been detected that your server can use a better compression algorithm for the thumbnails. We suggest to optimize them, since it will reduce the bandwidth use by up to 10%, by making the thumbnails much smaller. Regardless of this action, from now on the thumbnails will be created with this algorithm.') ?></p>
			<span><a href="#" class="btn" data-keyboard="true" data-backdrop="true" data-controls-modal="modal-for-thumbnail-optimization" onClick="return getThumbNumber();"><?php echo _('Optimize thumbnails'); ?></a></span>

			<div id="modal-for-thumbnail-optimization" class="modal hide fade" style="display: none">
				<div class="modal-header">
					<a class="close" href="#">&times;</a>
					<h3><?php echo _('Optimize thumbnails'); ?></h3>
				</div>
				<div class="modal-body" style="text-align: center">
					<div id="modal-loading-optimize-thumbnails" class="loading" style="display:block;"><img src="<?php echo site_url() ?>assets/js/images/loader-18.gif"/></div>
					<div id="modal-optimize-thumbnails-count"><?php echo _('Pictures left to be processed:') ?> <span id="modal-optimize-thumbnails-current-count">0</span></div>
					<div id="modal-optimize-thumbnails-errors"></div>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn primary" onClick="return optimizeThumbnails(true)"><?php echo _('Optimize') ?></a>
					<a href="#" class="btn secondary" onClick="return stopOptimizeThumbnails()"><?php echo _('Stop optimization	') ?></a>
				</div>

				<script type="text/javascript">
									
									
					var stop = false;
									
					var stopOptimizeThumbnails = function() {
						stop = true;
					}
									
					var optimizeThumbnails = function(manual){
						if(manual === true)
						{
							stop = false;
						}
										
						if(!stop)
						{
							jQuery('#modal-loading-optimize-thumbnails').show();
							stop = false;
							jQuery.post('<?php echo site_url('admin/system/tools_optimize_thumbnails/10') ?>', function(data){
								if(data.error instanceof Array)
								{
									jQuery('#modal-loading-optimize-thumbnails').hide();
									jQuery.each(data.error, function(i,v){
										jQuery('#modal-optimize-thumbnails-errors').append('<div class="alert-message error fade in" data-alert="alert"><p>' + v.message + '</p></div>');
									});
									return false;
								}
												
								if(data.status == "done")
								{
									jQuery('#modal-optimize-thumbnails-count').html('<?php echo _('Done.') ?>');
									jQuery('#modal-loading-optimize-thumbnails').hide();
									return false;
								}
												
								var activeCount = jQuery('#modal-optimize-thumbnails-current-count');
								activeCount.text((parseInt(activeCount.html()) < 10)?0:parseInt(activeCount.html()) - 10);
								optimizeThumbnails();
							}, 'json');
						}
						else
						{
							jQuery('#modal-loading-optimize-thumbnails').hide();
						}
					}
									
					jQuery(document).ready(function(){
						jQuery('#modal-for-thumbnail-optimization').bind('show', function () {
							jQuery.post('<?php echo site_url('admin/system/tools_optimize_thumbnails') ?>', function(data){
								jQuery('#modal-loading-optimize-thumbnails').hide();
								jQuery('#modal-optimize-thumbnails-errors').empty();
								jQuery('#modal-optimize-thumbnails-current-count').text(data.count);
							}, 'json');
						});
										
						jQuery('#modal-for-thumbnail-optimization').bind('hide', function () {
							stop = true;
						});
					});
				</script>
			</div>
		</div>
	<?php endif; ?>

	<?php if ($database_backup): ?>
		<div style="margin:0 10px 15px 0;">
			<h3><?php echo _('Download database backup') ?></h3>
			<p><?php echo _('You should routinely download your database backups. You will still need to backup the FoOlSlide directory, because this file will be of little use in case of complete server failure.') ?></p>
			<span><a href="<?php echo site_url('admin/system/tools_database_backup') ?>" class="btn" data-keyboard="true" data-backdrop="true"><?php echo _('Download database backup'); ?></a></span>
		</div>
	<?php endif; ?>

	<?php if ($database_optimize): ?>
		<div style="margin:0 10px 15px 0;">
			<h3><?php echo _('Optimize database') ?></h3>
			<p><?php echo _('Optimizing your database from time to time will make your FoOlSlide sligtly faster.') ?></p>
			<span><a href="<?php echo site_url('admin/system/tools_database_backup') ?>" class="btn" data-keyboard="true" data-backdrop="true"><?php echo _('Optimize database'); ?></a></span>
		</div>
	<?php endif; ?>


	<div style="margin:0 10px 15px 0;">
		<h3><?php echo _('Check the logs') ?></h3>
		<p><?php echo _('FoOlSlide produces daily logs for the errors it can produce. These errors are mostly 404 notices and missing cookies, but also true errors due to bugs can be found here. This is especially useful when reporting bugs.') ?></p>
		<span><a href="#" class="btn" data-keyboard="true" data-backdrop="true" data-controls-modal="modal-for-log-display" onClick="return getLog();"><?php echo _('Check the logs'); ?></a></span>


		<div id="modal-for-log-display" class="modal hide fade" style="display: none">
			<div class="modal-header">
				<a class="close" href="#">&times;</a>
				<h3><?php echo _('Check the logs'); ?></h3>
			</div>
			<div class="modal-body" style="text-align: center">
				<div id="modal-loading-log-display" class="loading" style="display:block;"><img src="<?php echo site_url() ?>assets/js/images/loader-18.gif"/></div>
				<select id="modal-select-log" style="display:none; margin-bottom:10px;" onchange="getLog(this.value)"></select>
				<textarea id="log-display-output" style="min-height: 300px; font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace !important" readonly="readonly">
				</textarea>
				<div id="modal-log-display-errors" style="margin-top:10px;"></div>
			</div>
			<div class="modal-footer">
				<?php
				if (function_exists('curl_init'))
				{
					echo '<center><a class="btn" style="float: none" href="' . site_url("admin/system/pastebin") . '" onclick="return pastebinLog()">' . _('Pastebin It!') . '</a></center>';
				}
				?>
			</div>

			<script type="text/javascript">
				var getLog = function(date){
					jQuery('#modal-loading-log-display').show();
					
					if(date == undefined)
					{
						date = "";
					}
					
					jQuery.post('<?php echo site_url('admin/system/tools_logs_get/') ?>' + date, function(data){
						
						if(data.error != undefined)
						{
							jQuery('#modal-loading-log-display').hide();
							jQuery('#modal-log-display-errors').append('<div class="alert-message error fade in" data-alert="alert"><p>' + data.error + '</p></div>');
							return false;
						}
						
						var log_select = jQuery('#modal-select-log');
						if(log_select.text().length < 3)
						{
							var options = '';
							jQuery.each(data.dates, function(i,v){
								options = '<option value="' + v + '">' + v + '</option>' + options;
							});
							log_select.empty().html(options).show();
						}
						
						jQuery('#modal-loading-log-display').hide();
						jQuery('#log-display-output').val(data.log);
					}, 'json');
				}
				
				var pastebinLog = function() {
					var modalInfoOutput = jQuery("#modal-for-log-display");
					jQuery.post('<?php echo site_url("admin/system/pastebin") ?>', { output: modalInfoOutput.find("#log-display-output").val() }, function(result) {
						if (result.href != "") {
							modalInfoOutput.find(".modal-footer").html('<center><input value="' + result.href + '" style="text-align: center" onclick="select(this);" readonly="readonly" /><br/><?php echo _('Note: This paste expires in 1 hour.'); ?></center>');
						}
					}, 'json');
					return false;
				}
			</script>
		</div>

	</div>
</div>
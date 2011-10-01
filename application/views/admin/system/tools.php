<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed'); ?>

<div class="table">
	<?php if ($imagick_optimize): ?>
		<div style="margin:0 10px 15px 0;">
			<h3><?php echo _('Optimize Thumbnails') ?></h3>
			<p><span class="label important"><?php echo _('Important') ?></span> <?php echo _('FoOlSlide has detected that your server is able to use a better compression algorithm for thumbnail generation. This optimization will create small thumbnails and reduce up to 10% bandwidth usage. However, regardless of this action, all thumbnails will be generated with this new algorithmn.') ?></p>
			<span><a href="#" class="btn" data-keyboard="true" data-backdrop="true" data-controls-modal="modal-for-thumbnail-optimization" onClick="return getThumbNumber();"><?php echo _('Optimize Thumbnails'); ?></a></span>

			<div id="modal-for-thumbnail-optimization" class="modal hide fade" style="display: none">
				<div class="modal-header">
					<a class="close" href="#">&times;</a>
					<h3><?php echo _('Optimize Thumbnails'); ?></h3>
				</div>
				<div class="modal-body" style="text-align: center">
					<div id="modal-loading-optimize-thumbnails" class="loading" style="display:block;"><img src="<?php echo site_url() ?>assets/js/images/loader-18.gif"/></div>
					<div id="modal-optimize-thumbnails-count"><?php echo _('Pictures left to be processed:') ?> <span id="modal-optimize-thumbnails-current-count">0</span></div>
					<div id="modal-optimize-thumbnails-errors"></div>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn primary" onClick="return optimizeThumbnails(true)"><?php echo _('Optimize') ?></a>
					<a href="#" class="btn secondary" onClick="return stopOptimizeThumbnails()"><?php echo _('Stop Optimization') ?></a>
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
			<h3><?php echo _('Download Database Backup') ?></h3>
			<p><?php echo _('This will allow you to routinely download a copy of your FoOlSlide database. Furthermore, routine backups of the FoOlSlide directory is also required in case of a complete server failure for this file to be useful.') ?></p>
			<span><a href="<?php echo site_url('admin/system/tools_database_backup') ?>" class="btn" data-keyboard="true" data-backdrop="true"><?php echo _('Download Database Backup'); ?></a></span>
		</div>
	<?php endif; ?>

	<?php if ($database_optimize): ?>
		<div style="margin:0 10px 15px 0;">
			<h3><?php echo _('Optimize Database') ?></h3>
			<p><?php echo _('Performing database optimization from time to time will cause your FoOlSlide installation to be slightly faster.') ?></p>
			<span style=""><?php
	$CI = & get_instance();
	$CI->buttoner[] = array(
		'text' => _('Optimize Database'),
		'href' => site_url('admin/system/tools_database_optimize'),
		'plug' => _('Are you sure you want to optimize your FoOlSlide database?')
	);
	echo buttoner();
	$CI->buttoner = array();
		?></span>
		</div>
	<?php endif; ?>


	<div style="margin:0 10px 15px 0;">
		<h3><?php echo _('FoOlSlide Logs') ?></h3>
		<p><?php echo _('Daily logs are generated by FoOlSlide and contains information that will help developers debug your problems. If any actual errors are found, please report any serious errors to the FoOlSlide developers. However, do not report any 404 and missing cookie notices.') ?></p>
		<span><a href="#" class="btn" data-keyboard="true" data-backdrop="true" data-controls-modal="modal-for-log-display" onClick="return getLog();"><?php echo _('View Logs'); ?></a></span>
		<span style=""><?php
				$CI = & get_instance();
				$CI->buttoner[] = array(
					'text' => _('Prune Logs'),
					'href' => site_url('admin/system/tools_logs_prune'),
					'plug' => _('Are you sure you want to prune all FoOlSlide logs?'),
					'rel' => 'popover-right',
					'title' => _('Prune Logs'),
					'data-content' => _('FoOlSlide logs can often use a lot of space. This function will allow you to remove all logs to save space.') . '<br/><br/>' . _('Current Size') . ': ' . $logs_space . 'kb'
				);
				echo buttoner();
				?></span>

		<div id="modal-for-log-display" class="modal hide fade" style="display: none">
			<div class="modal-header">
				<a class="close" href="#">&times;</a>
				<h3><?php echo _('View Logs'); ?></h3>
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
					echo '<center><a class="btn" style="float: none" href="#" onclick="return pastebinLog()">' . _('Pastebin It!') . '</a></center>';
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
						jQuery("#modal-for-log-display").find(".modal-footer").html('<center><a class="btn" style="float: none" href="#" onclick="return pastebinLog()"><?php echo _('Pastebin It!') ?></a></center>');
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
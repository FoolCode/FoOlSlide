<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed'); ?>

<div class="table">
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
				<div id="modal-optimize-thumbnails-count"><?php echo _('Pictures to be processed:') ?> <span id="modal-optimize-thumbnails-current-count">0</span>/<span id="modal-optimize-thumbnails-total-count"></span></div>
				<div id="modal-optimize-thumbnails-errors"></div>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn primary" onClick="return optimizeThumbnails(true)"><?php echo _('Optimize') ?></a>
				<a href="#" class="btn secondary"><?php echo _('Cancel') ?></a>
			</div>

			<script type="text/javascript">
				
				
				var stop = false;
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
							if(data.error != undefined)
							{
								jQuery('#modal-optimize-thumbnails-errors').html('<div class="alert-message warning fade in"><a class="close" href="#"></a><p>Test</p></div>');
								return false;
							}
							
							var activeCount = jQuery('#modal-optimize-thumbnails-current-count');
							activeCount.text(parseInt(activeCount.html()) + 10);
							optimizeThumbnails();
						}, 'json');
					}
				}
				
				jQuery(document).ready(function(){
					jQuery('#modal-for-thumbnail-optimization').bind('show', function () {
						jQuery.post('<?php echo site_url('admin/system/tools_optimize_thumbnails') ?>', function(data){
							jQuery('#modal-loading-optimize-thumbnails').hide();
							jQuery('#modal-optimize-thumbnails-current-count').text(0);
							jQuery('#modal-optimize-thumbnails-total-count').text(data.count);
						}, 'json');
					});
					
					jQuery('#modal-for-thumbnail-optimization').bind('hide', function () {
						stop = true;
					});
				});
			</script>
		</div>
	</div>

</div>
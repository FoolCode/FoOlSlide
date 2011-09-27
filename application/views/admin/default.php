<!DOCTYPE html>
<html>
	<head>
		<title><?php echo get_setting('fs_gen_site_title'); ?> <?php echo _('Control panel') ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/bootstrap/style.css?v=<?php echo get_setting('fs_priv_version') ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/admin/style.css?v=<?php echo get_setting('fs_priv_version') ?>" />
		<style type="text/css">
			body {
				padding-top: 60px;
			}
		</style>
		<script type="text/javascript" src="<?php echo site_url() ?>assets/js/jquery.js"></script>
		<script type="text/javascript" src="<?php echo site_url() ?>assets/bootstrap/bootstrap.js"></script>
		<script type="text/javascript">
			function slideDown(item) { jQuery(item).slideDown(); }
			function slideUp(item) { jQuery(item).slideUp(); }
			function slideToggle(item) { jQuery(item).slideToggle(); }
			
			function confirmPlug(href, text, item)
			{
				if (text != "") {
					var modalContainer = jQuery("#modal-container");
					modalContainer.modal({show: true, closeOnEscape: true, backdrop: 'static', keyboard: true});
					modalContainer.find("#modal-text-desc").html(text);
					modalContainer.find("#modal-btn-no").click(function() {	modalContainer.modal('hide'); return false; });
					modalContainer.find("#modal-btn-yes").attr('href', href).click(function() {
						jQuery.post(href, function(result) {
							if (location.href == result.href) window.location.reload(true);
							location.href = result.href;
						}, 'json');
						return false;
					});
				}
				else {
					jQuery.post(href, function(result) {
						if (location.href == result.href) window.location.reload(true);
						location.href = result.href;
					}, 'json');
				}
			}
			
			function addField(e)
			{
				if (jQuery(e).val().length > 0)
				{
					jQuery(e).clone().val('').insertAfter(e);
					jQuery(e).attr('onKeyUp', '');
					jQuery(e).attr('onChange', '');
				}
			}
			
			jQuery(document).ready(function() {
				<?php
				$CI = & get_instance();
				if ($CI->agent->is_browser('MSIE'))
				{
				?>
					jQuery('[placeholder]').focus(function() {
						var input = jQuery(this);
						if (input.val() == input.attr('placeholder'))
						{
							input.val('');
							input.removeClass('placeholder');
						}
					}).blur(function() {
						var input = jQuery(this);
						if (input.val() == '' || input.val() == input.attr('placeholder'))
						{
							input.addClass('placeholder');
							input.val(input.attr('placeholder'));
						}
					}).blur().parents('forms').submit(function() {
						jQuery(this).find('[placeholder]').each(function() {
							var input = jQuery(this);
							if (input.val() == input.attr('placeholder')) {
								input.val('');
							}
						})
					});
				<?php } ?>
				// Auto-Focus on First Input
				jQuery(":input:first").focus(); // Focus on first input generated
				
				// Bootstrap jQuery
				jQuery("a[rel=twipsy]").twipsy({ live: true });
				jQuery("a[rel^='popover']").each(function() {
					var direction = $(this).attr('rel').replace("popover-", "");
					$(this).popover({ offset: 10, placement: direction });
				});
			});
		</script>
		<script type="text/javascript">jQuery().alert();</script>
	</head>
	
	<body>
		<div class="topbar" data-dropdown="dropdown">
			<div class="topbar-inner">
				<div class="container-fluid">
					<a class="brand" href="#"><?php echo get_setting('fs_gen_site_title'); ?> Slide - <?php echo _('Control Panel'); ?></a>
					<ul class="nav secondary-nav">
						<li><a href="<?php echo site_url(); ?>">
							<?php echo _("Reader") ?></a></li>
						<?php if ((isset($this->tank_auth) && $this->tank_auth->is_allowed()) || (isset($this->tank_auth) && $this->tank_auth->is_logged_in()))
						{ ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle"><?php echo $this->tank_auth->get_username(); ?></a>
							<ul class="dropdown-menu">
								<?php if (isset($this->tank_auth) && $this->tank_auth->is_allowed())
								{ ?><li><a href="<?php echo site_url('account'); ?>">
									<?php echo _("Your Profile") ?></a></li>
								<?php } ?>
								<?php if (isset($this->tank_auth) && $this->tank_auth->is_logged_in())
								{ ?><li><a href="<?php echo site_url('/account/auth/logout'); ?>">
									<?php echo _("Logout") ?></a></li>
								<?php } ?>
							</ul>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="container-fluid">
			<div class="sidebar">
				<div class="well">
					<?php echo $sidebar ?>
				</div>
			</div>
			
			<div class="content">
				<ul class="breadcrumb">
					<?php
						echo '<li>' . $controller_title . '</li>';
						if (isset($function_title))
							echo ' <span class="divider">/</span> <li>' . $function_title . '</li>';
						if (isset($extra_title) && !empty($extra_title))
						{
							$breadcrumbs = count($extra_title); $count = 1;
							foreach ($extra_title as $item) {
								echo ' <span class="divider">/</span> ';
								if ($count == $breadcrumbs)
									echo '<li class="active">' . $item . '</li>';
								else
									echo '<li>' . $item . '</li>';
							}
						}
					?>
				</ul>
				
				<div class="alerts">
					<?php
						if (isset($this->notices))
							foreach ($this->notices as $key => $value)
							{
								echo '<div class="alert-message ' . $value["type"] . ' fade in" data-alert="alert"><a class="close" href="#">&times;</a><p>' . $value["message"] . '</p></div>';
							}
						if (isset($this->tank_auth))
						{
							$flashdata = $this->session->flashdata('notices');
							if (!empty($flashdata))
								foreach ($flashdata as $key => $value)
								{
									echo '<div class="alert-message ' . $value["type"] . ' fade in" data-alert="alert"><a class="close" href="#">&times;</a><p>' . $value["message"] . '</p></div>';
								}
						}
					?>
				</div>
				
				<?php echo $main_content_view; ?>
			</div>
		</div>
		
		<footer style="position: relative; bottom: 0px; width: 100%">
			<p style="padding-left: 20px;">FoOlSlide Version <?php
			if (isset($this->tank_auth))
			{
				echo get_setting('fs_priv_version');
				if ($this->tank_auth->is_admin() && (get_setting('fs_priv_version') != get_setting('fs_cron_autoupgrade_version') && (get_setting('fs_cron_autoupgrade_version'))))
					echo ' – <a href="' . site_url('/admin/upgrade/upgrade/') . '">' . _('New upgrade available:') . ' ' . get_setting('fs_cron_autoupgrade_version') . '</a>';
			} ?></p>
		</footer>
		
		<!-- Modal Container for Admin Panel -->
		<div id="modal-container" class="modal hide fade">
			<div class="modal-header">
				<a href="#" class="close">&times;</a>
				<h3 id="modal-text-head">Warning!</h3>
			</div>
			<div class="modal-body">
				<p id="modal-text-desc"></p>
			</div>
			<div class="modal-footer">
				<a href="#" id="modal-btn-no" class="btn primary"><?php echo _('No'); ?></a>
				<a href="#" id="modal-btn-yes" class="btn secondary"><?php echo _('Yes'); ?></a>
			</div>
		</div>
	</body>
</html>
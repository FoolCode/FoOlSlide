<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $template['title']; ?></title>
		<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php echo $template['metadata']; ?>
		<?php echo link_tag('assets/js/jquery.mobile.css') ?>
		<?php echo link_tag('content/themes/mobile/style.css') ?>

	</head>
	<body>
		<div data-role="page" data-add-back-btn="true">


			<div data-role="header">
				<h1><?php echo $template['title'] ?></h1>
				<a href="<?php echo site_url('reader') ?>" data-icon="home" class="ui-btn-right"><?php echo _('Home') ?></a>
				<?php
				echo form_open("/reader/search/");
				echo form_input(array('name' => 'search', 'placeholder' => _('To search series, type and hit enter'), 'id' => 'searchbox'));
				echo form_close();
				?>
			</div>


			<?php echo $template['body']; ?>

			<div data-role="footer">
				<h4><?php echo get_setting('fs_gen_site_title') ?></h4>
			</div><!-- /footer -->


		</div><!-- /page -->
	</body>
</html>
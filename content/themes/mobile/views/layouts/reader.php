<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $template['title']; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php echo $template['metadata']; ?>
		<?php echo link_tag('assets/js/jquery.mobile.css') ?>
		<?php echo link_tag('content/themes/mobile/style.css') ?>

	</head>
	<body>
		<div data-role="page">


			<div data-role="header">
				<h1><?php echo _('Reading') ?></h1>
				<a href="<?php echo site_url('reader') ?>" data-icon="home" class="ui-btn-right"><?php echo _('Home') ?></a>
			</div>

			<?php echo $template['body']; ?>

			<div data-role="footer">
				<h4>FoOlSlide</h4>
			</div><!-- /footer -->


		</div><!-- /page -->
	</body>
</html>
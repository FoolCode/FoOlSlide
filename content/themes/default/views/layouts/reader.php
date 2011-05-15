<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $template['title']; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php echo $template['metadata']; ?>
		<?php echo link_tag('content/themes/default/style.css') ?>

	</head>
	<body>
		<div id="wrapper">
			<div id="header">

				<div id="navig">
					<ul>
						<li>
							<a href="<?php echo site_url('/reader/') ?>"><?php echo _('Home'); ?></a>
						</li>
						<li>
							<a href="<?php echo site_url('/reader/list') ?>"><?php echo _('Series list'); ?></a>
						</li>
						<li style="width:280px;">
							<?php
							echo form_open("/reader/search/");
							echo form_input(array('name' => 'search', 'placeholder' => _('To search, type and hit enter'), 'id' => 'searchbox'));
							echo form_close();
							?>
							<a href="<?php echo site_url('/reader/search/') ?>"><?php echo _('Search'); ?></a>
						</li>
						<div class="clearer"></div>
					</ul>
				</div>

				<a href="<?php echo site_url('/reader/') ?>"><div id="title"><?php echo get_setting('fs_gen_site_title') ?></div></a> 
				<?php echo'<div class="home_url"><a href="' . get_setting('fs_gen_back_url') . '">Go back to site &crarr;</a></div>'; ?>
				<div class="clearer"></div>	
			</div>




			<div id="content">
				<?php 
					if(!isset($is_reader) || !$is_reader) echo '<div class="panel">'.get_sidebar();

					echo $template['body'];
				
					if(!isset($is_reader) || !$is_reader) echo '</div>';
				?>

			</div>

			</div>
			<div id="footer">
				<div class="text">
					<?php echo get_setting('fs_gen_footer_text'); ?>
				</div>
			</div>


	</body>
</html>
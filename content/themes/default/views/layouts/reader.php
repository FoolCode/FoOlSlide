<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $template['title']; ?></title>
		<?php echo $template['metadata']; ?>
		<?php echo link_tag('content/themes/default/style.css') ?>

	</head>
	<body>
		<div id="wrapper">
			<div id="header">

				<div id="navig">
					<ul>
						<li>
							<a href="<?php echo site_url('/reader/') ?>">Home</a>
						</li>
						<li style="width:280px;">
							<?php
							echo form_open("/reader/search/");
							echo form_input(array('name' => 'search', 'placeholder' => 'To search, type and hit enter', 'id' => 'searchbox'));
							echo form_close();
							?>
							<a href="<?php echo site_url('/reader/search/') ?>">Search</a>
						</li>
						<div class="clearer"></div>
					</ul>
				</div>

				<a href="<?php echo ""; ?>"><div id="title"><?php echo get_setting('fs_gen_site_title') ?></div></a> 
				<?php echo'<div class="home_url"><a href="' . get_setting('fs_gen_back_url') . '">Go back to site &crarr;</a></div>'; ?>
				<div class="clearer"></div>	
			</div>




			<div id="content">

				<?php
				echo $template['body'];
				?>

			</div>

			<div class="clearfooter"></div>
			</div>
			<div id="footer">
				<div class="text">
					<?php echo get_setting('fs_gen_footer_text'); ?>
				</div>
			</div>


	</body>
</html>
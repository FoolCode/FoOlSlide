<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">


	<head>
		<title><?php echo _('FoOlSlide Administration') ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="<?= base_url() ?>assets/admin/style.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="<?php echo site_url() ?>assets/js/jquery.js"></script>
        <script type="text/javascript">
            function slideDown(item) { jQuery(item).slideDown(); }
            function slideUp(item) { jQuery(item).slideUp(); }
            function slideToggle(item) { jQuery(item).slideToggle(); }
            function confirmPlug(href, text)
            {
                var plug = confirm(text);
                if (plug)
                {
                    jQuery.post(href, function(result){
						if(location.href == result.href) window.location.reload();
						location.href = result.href;
					}, 'json');
                }
            }
			
            function addField(e)
            {
				if(jQuery(e).val().length > 0)
				{
					jQuery(e).clone().val('').insertAfter(e);
					jQuery(e).attr('onKeyUp', '');
					jQuery(e).attr('onChange', '');
				}
            }
        </script>

	</head>



	<body>

		<div class="wrapper">

			<div id="background">
				<img src="<?php echo base_url() ?>assets/admin/images/admin_background.png" />
			</div>

			<div id="header">
				<div class="logout">
					<?php
					if (logged_in()) {
						?>
						<a href="<?= site_url('auth/logout'); ?>">Logout <?php echo $this->ion_auth->get_user()->username; ?></a>
						<?php
					}
					?>
				</div>
				<div class="title"><?php echo get_setting('fs_gen_site_title'); ?> Slide - <?php echo _('control panel'); ?></div>

			</div>

			<div id="content_wrap">

				<div id="sidebar">
					<?= $sidebar ?>
				</div>

				<div class="spacer"></div>


				<div id="center">

					<div class="title"><?php
					echo $controller_title;
					if (isset($function_title))
						echo ' » ' . $function_title;
					if (isset($extra_title) && !empty($extra_title)) {
						foreach ($extra_title as $item)
							echo ' » ' . $item;
					}
					?></div>
					<div class="content">
						<div class="errors">
							<?php
							if (isset($this->notices))
								foreach ($this->notices as $key => $value) {
									if ($value["type"] == 'error')
										$color = 'red';
									if ($value["type"] == 'warn')
										$color = 'yellow';
									if ($value["type"] == 'notice')
										$color = 'green';
									echo '<div class="alert ' . $color . '">' . $value["message"] . '</div>';
								}
							$flashdata = $this->session->flashdata('notices');
							if (!empty($flashdata))
								foreach ($flashdata as $key => $value) {
									if ($value["type"] == 'error')
										$color = 'red';
									if ($value["type"] == 'warn')
										$color = 'yellow';
									if ($value["type"] == 'notice')
										$color = 'green';
									echo '<div class="alert ' . $color . '">' . $value["message"] . '</div>';
								}
							?>
						</div>

						<?= $main_content_view; ?>

					</div></div></div>
            <div class="push"></div>

		</div>

		<div id="footer"><div class="text">FoOlSlide Version <?php echo get_setting('fs_priv_version') ?></div></div>
	</body>

</html>
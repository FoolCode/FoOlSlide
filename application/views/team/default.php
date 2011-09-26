<!DOCTYPE html>
<html>
	<head>
		<title><?php echo get_setting('fs_gen_site_title'); ?> <?php echo _('Team panel') ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
		<!--[if lt IE 9]>
		  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

<!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/admin/style.css" /> -->
		<link rel="stylesheet" href="http://twitter.github.com/bootstrap/1.3.0/bootstrap.min.css">
		<style type="text/css">
			body {
				padding-top: 60px;
			}
		</style>
		<script type="text/javascript" src="<?php echo site_url() ?>assets/js/jquery.js"></script>
		<script type="text/javascript" src="<?php echo site_url() ?>assets/js/bootstrap.js"></script>
	</head>

	<body>
		<div class="topbar" data-dropdown="dropdown">
			<div class="fill">
				<div class="container">
					<a class="brand" href="#"><?php echo _('Team panel') ?></a>

					<ul class="nav secondary-nav">
						<li><a href="<?php echo site_url(); ?>">
								<?php echo _("Reader") ?></a></li><li class="dropdown">
								<a href="#" class="dropdown-toggle"><?php echo $this->tank_auth->get_username(); ?></a>
								<ul class="dropdown-menu">
									<li>
										<a href="<?php echo site_url('account'); ?>">
											<?php echo _("Your Profile") ?>
										</a>
									</li>
									<li>
										<a href="<?php echo site_url('/account/auth/logout'); ?>">
											<?php echo _("Logout") ?>
										</a>
									</li>
								</ul>
							</li>
					</ul>
				</div>
			</div>
		</div>


	</body>
</html>
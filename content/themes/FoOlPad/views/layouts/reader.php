<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<?php
		// prefetch the DNS entry of the load balancers
		$balancers = unserialize(get_setting('fs_balancer_clients'));
		if (is_array($balancers))
			foreach ($balancers as $item)
			{
				echo '<link rel="dns-prefetch" href="' . addslashes($item["url"]) . '" />
';
			}
		?>
		<title><?php echo $template['title']; ?></title>
		<meta name="description" content="<?php echo $template['title'] . ' ' . addslashes(get_setting('fs_gen_site_title')); ?>">
		<meta name="author" content="<?php echo get_home_team()->name ?>">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<link rel="stylesheet" href="<?php echo get_theme_dir() . 'style.css?v=' . get_setting('fs_priv_version') ?>">
		<link rel="sitemap" type="application/xml" title="Sitemap" href="<?php echo site_url() ?>sitemap.xml" />
		<link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo site_url() ?>rss.xml" />
		<link rel="alternate" type="application/atom+xml" title="Atom" href="<?php echo site_url() ?>atom.xml" />
		<link rel="shortcut icon" href="<?php echo get_theme_dir() ?>imgages/favicon.ico">
		<link rel="apple-touch-icon" href="<?php echo get_theme_dir() ?>images/apple-touch-icon.png">
		<meta http-equiv="imagetoolbar" content="false" />
		<meta name="application-name" content="FoOlSlide: <?php echo addslashes(get_setting('fs_gen_site_title')) ?>" />
		<meta name="msapplication-tooltip" content="<?php echo addslashes(get_setting('fs_gen_site_title')) ?>FoOlSlide panel." />
		<meta name="msapplication-starturl" content="<?php echo site_url() ?>?pinned=true" />
		<meta name="msapplication-navbutton-color" content="#8154A3" />
		<meta name="msapplication-window" content="width=1024;height=600" />
		<meta name="msapplication-task" content="name=Latest releases;action-uri=<?php echo site_url() ?>;icon-uri=" />
		<meta name="msapplication-task" content="name=Series list;action-uri=<?php echo site_url('reader/list') ?>;icon-uri=" />
		<?php
		// wrap up all the Facebook Open Graph, we need this very dynamic
		echo '<meta property="og:title" content="' . $template['title'] . '" />
		';
		echo '<meta property="og:site_name" content="' . addslashes(get_setting('fs_gen_site_title')) . '" />
		';

		if (isset($og_description) && $og_description)
			echo '<meta property="og:description" content="' . $og_description . '" />
		';
		if (isset($og_url) && $og_url)
			echo '<meta property="og:url" content="' . $og_url . '" />
		';
		if (isset($og_image) && $og_image)
			echo '<meta property="og:image" content="' . $og_image . '" />
		';

		// canonical url needs to be dynamic as well
		if (!isset($canonical_url))
		{
			$canonical_url = site_url();
		}
		echo '<link rel="canonical" href="' . $canonical_url . '" />
';
		?>
		<script src="<?php echo get_theme_dir() ?>js/libs/modernizr-2.0.6.min.js"></script>


		<link href='http://fonts.googleapis.com/css?family=Damion' rel='stylesheet' type='text/css'>
	</head>

	<body>

		<div id="container" class="foolslideUI">
			<header>

			</header>
			<aside id="sidebar">
				<div class="layer1">
				</div>
				<div class="items">
					<div id="dynamic_sidebar">
					</div>
				</div>
			</aside>
			<div id="main" role="main">
				<div class="layer1">
				</div>
				<div id="dynamic_content">
				</div>
			</div>
			<footer>

			</footer>
		</div> <!--! end of #container -->

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="js/libs/jquery-1.6.2.min.js"><\/script>')</script>

		<script defer src="<?php echo get_theme_dir() ?>js/plugins.js?v=<?php echo get_setting('fs_priv_version') ?>"></script>
		<script defer src="<?php echo get_theme_dir() ?>js/script.js?v=<?php echo get_setting('fs_priv_version') ?>"></script>

		<?php
		if (get_setting('fs_theme_google_analytics')):
			?>
			<script>
				window._gaq = [['_setAccount', '<?php echo get_setting('fs_theme_google_analytics') ?>'], ['_trackPageview'], ['_trackPageLoadTime']];
				Modernizr.load({
					load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js'
				});
			</script>
		<?php endif; ?>

		<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you want to support IE 6.
	 chromium.org/developers/how-tos/chrome-frame-getting-started -->
		<!--[if lt IE 7 ]>
		  <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
		  <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
		<![endif]-->

		<script type="text/javascript">
			var slideUrl = "<?php echo addslashes(site_url()); ?>";
		</script>

	</body>
</html>
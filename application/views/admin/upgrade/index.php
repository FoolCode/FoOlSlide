<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

$CI = & get_instance();

if ($new_versions) {
	if ($can_upgrade) {
		$CI->buttoner[] = array(
			'text' => _('Upgrade FoOlSlide automatically'),
			'href' => site_url('admin/upgrade/do_upgrade'),
			'plug' => _('Do you really want to upgrade to the latest version?')
		);
	}
	
	$CI->buttoner[] = array(
			'text' => _('Download latest version'),
			'href' => $new_versions[0]->download
		);
}

if (!$new_versions) {
	if ($can_upgrade) {
		$CI->buttoner[] = array(
			'text' => _('Repair FoOlSlide files'),
			'href' => site_url('admin/upgrade/do_upgrade'),
			'plug' => _('Do you really want to reinstall FoOlSlide?')
		);
	}
}
?>

<div class="table" style="padding-bottom: 10px">
	<h3 style="float: left"><?php echo _('Upgrade'); ?></h3>
	<span style="float: right; padding: 5px"><?php echo buttoner(); ?></span>
	<hr class="clear"/>
<?php
	echo _('Current Version') . ': ' . $current_version . '<br/>';
	echo _('Latest Version Available') . ': ' . ($new_versions ? ($new_versions[0]->version.'.'.$new_versions[0]->subversion.'.'.$new_versions[0]->subsubversion) : _('Your FoOlSlide is at the latest version.')).'<br/><br/>';
?>
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript" src="https://www.transifex.net/projects/p/foolslide/resource/defaultpot/chart/inc_js/"></script>
	<div id="transifex_chart" style="text-align: center">Loading chart...<br/></div>
	<?php echo _('Please, check the <a href="https://www.transifex.net/projects/p/foolslide/resource/defaultpot/">transifex translation project</a> in case you\'re using a non-English language for FoOlSlide. Make sure the % on your language isn\'t under 90% before upgrading. Contribute to the translation to get your language updated in the next localization update of FoOlSlide (normally within 48 hours).'); ?>
</div>

<br/>

<?php
if ($new_versions)
{
	echo '<div class="table" style="padding-bottom: 10px">';
	echo '<h3>' . _('Changelog') . '</h3><div class="changelog">';
	foreach ($new_versions as $version)
	{
		echo '<br/><div class="item">
			<div class="title">' ._('Changelog for Version') . ' ' . implode('.', array($version->version, $version->subversion, $version->subsubversion)) . '</div>
			<div class="description">' . nl2br($version->changelog) .'</div></div>';
	}
	echo '</div></div>';
}

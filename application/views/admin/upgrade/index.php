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

echo buttoner();
echo '<br/><br/>
	Current version: '.$current_version.'<br/>
	Latest version available: '.($new_versions?($new_versions[0]->version.'.'.$new_versions[0]->subversion.'.'.$new_versions[0]->subsubversion):_('Your FoOlSlide is up to date.')).'<br/><br/>';
?>
<?php echo _('Please, check the <a href="https://www.transifex.net/projects/p/foolslide/resource/defaultpot/">transifex translation project</a> in case you\'re using a non-English language for FoOlSlide. Make sure the % on your language isn\'t under 90% before upgrading. Contribute to the translation to get your language updated in the next localization update of FoOlSlide (normally within 48 hours).');
if($new_versions){
	echo '<div class="list">';
	foreach($new_versions as $version){
		echo '<div class="item">
                <div class="title">Changelog for version '.implode('.',array($version->version, $version->subversion, $version->subsubversion)).'</div><br/>'
					.nl2br($version->changelog).'<br/><br/>
				</div>';
	}
	echo '</div>';
}


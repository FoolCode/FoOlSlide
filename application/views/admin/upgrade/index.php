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

echo buttoner();
echo '<br/><br/>
	Current version: '.$current_version.'<br/>
	Latest version available: '.($new_versions?($new_versions[0]->version.'.'.$new_versions[0]->subversion.'.'.$new_versions[0]->subsubversion):_('Your FoOlSlide is up to date.')).'<br/><br/>';
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


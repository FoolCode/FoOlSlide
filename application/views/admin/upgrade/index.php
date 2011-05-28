<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

$CI = & get_instance();

if ($latest) {
	if ($can_upgrade) {
		$CI->buttoner[] = array(
			'text' => _('Upgrade FoOlSlide automatically'),
			'href' => site_url('admin/upgrade/do_upgrade'),
			'plug' => _('Do you really want to upgrade to the latest version?')
		);
	}
	
	$CI->buttoner[] = array(
			'text' => _('Download latest version'),
			'href' => $latest->download
		);
}

echo buttoner();
echo '<br/><br/>
	Current version: '.$version.'<br/>
	Latest version available: '.($latest?($latest->version.'.'.$latest->subversion.'.'.$latest->subsubversion):_('Your FoOlSlide is up to date.')).'<br/><br/>';
if($latest)
echo 'Changelog: '.$latest->changelog;

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$this->buttoner[] = array(
	'text' => _('Apply for membership'),
	'href' => site_url('/admin/members/apply_team/'.$team->stub)
);

echo buttoner();


echo form_open();
echo $table;
echo form_close();
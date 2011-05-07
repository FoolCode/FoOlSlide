<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

$CI = & get_instance();

if (!$this->tank_auth->is_team($team->id))
	$CI->buttoner[] = array(
		'text' => _('Apply for membership'),
		'href' => site_url('/admin/members/apply_team/' . $team->id),
		'plug' => _('Do you really want to apply for membership in this team?')
	);

echo buttoner();


echo form_open();
echo $table;
echo form_close();
?>
<br/><br/>
<div class="section">Members:</div><br/>
<?php
echo $members;
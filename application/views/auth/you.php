<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

$this->buttoner[] = array(
	'href'	=>	site_url('/admin/auth/change_password/'),
	'text'	=>	_('Reset password'),
);


$this->buttoner[] = array(
	'href'	=>	site_url('/admin/auth/change_email/'),
	'text'	=>	_('Change email'),
);


echo buttoner();

echo $table;?>

<br/><br/>

<?php
echo form_open();
echo $group;
echo form_close();
?>
<br/><br/>
Gravatar:
<br/>
<img src="<?php echo get_gravatar($user->email, 150); ?>" />
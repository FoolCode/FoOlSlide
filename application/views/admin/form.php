<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="table">
<?php
	if (isset($title)) echo '<h3 style="float: left">' . $title . '</h3>';
?>
	<span style="float: right; padding: 5px"><?php echo buttoner(); ?></span>
	<hr class="clear"/>
<?php
	echo buttoner();
	echo form_open_multipart("", array('class' => 'form-stacked'));
	echo $table;
	echo form_close();
?>
</div>
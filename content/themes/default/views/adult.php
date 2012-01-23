<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed'); ?>

<div class="large comic alert">
	<h1 class="title">
		<?php echo $comic->name; ?>
	</h1>
	<div class="info">
		<?php echo _('This series contains mature contents and is meant to be viewed by an adult audience.<br/>	If you are of legal age, click on continue.'); ?>
		<br/>
		<br/>
		<a href="<?php echo site_url() ?>">Back to index</a> or 
		<?php 
			echo form_open('','',array('adult' => 'true'));
			echo form_submit('', _('Continue'));
			echo form_close();
		?>
	</div>
</div>

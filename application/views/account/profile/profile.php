<div class="incontent panel">
	<?php
	$display_name = array(
		'name' => 'display_name',
		'id' => 'display_name',
		'value' => $user_display_name
	);
	$twitter = array(
		'name' => 'twitter',
		'id' => 'twitter',
		'value' => $user_twitter,
	);
	$bio = array(
		'name' => 'bio',
		'id' => 'bio',
		'lenght' => 140,
		'value' => $user_bio
	);
	?>
	<?php echo form_open($this->uri->uri_string()); ?>

	<div class="formgroup">
		<div><?php echo form_label(_('Display name (public)'), $display_name['id']); ?></div>
		<div><?php echo form_input($display_name); ?></div>
		<div style="color: red;"><?php echo form_error($display_name['name']); ?><?php echo isset($errors[$display_name['name']]) ? $errors[$display_name['name']] : ''; ?></div>
	</div>

	<div class="formgroup">
		<div><?php echo form_label(_('Twitter username (public)'), $twitter['id']); ?></div>
		<div><?php echo form_input($twitter); ?></div>
		<div style="color: red;"><?php echo form_error($twitter['name']); ?><?php echo isset($errors[$twitter['name']]) ? $errors[$twitter['name']] : ''; ?></div>
	</div>
	
	<div class="formgroup">
		<div><?php echo form_label(_('Bio (public)'), $bio['id']); ?></div>
		<div><?php echo form_textarea($bio); ?></div>
		<div style="color: red;"><?php echo form_error($bio['name']); ?><?php echo isset($errors[$bio['name']]) ? $errors[$bio['name']] : ''; ?></div>
	</div>
	
	
	<div class="formgroup">
		<div><?php echo form_submit('submit', _('Save')); ?></div>
	</div>
<?php echo form_close(); ?>


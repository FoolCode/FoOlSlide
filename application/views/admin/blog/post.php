<?php
$this->buttoner[] = array(
	'text' => _('Delete Series'),
	'href' => site_url('/admin/blog/delete/post/'.$post->id),
	'plug' => _('Do you really want to delete this series and its chapters?')
);
?>
<div class="table">
	<h3 style="float: left"><?php echo _('Series Information'); ?></h3>
	<span style="float: right; padding: 5px"><?php echo buttoner(); ?></span>
	<hr class="clear"/>
	<?php
		echo form_open_multipart("", array('class' => 'form-stacked'));
		echo $table;
		echo form_close();
	?>
</div>

<br/>

<?php
	$this->buttoner = array(
		array(
			'href' => site_url('/admin/series/add_new/'.$post->stub),
			'text' => _('Add Chapter')
		)
	);
	
	if($this->tank_auth->is_admin())
	{
		$this->buttoner[] = array(
			'href' => site_url('/admin/series/import/'.$post->stub),
			'text' => _('Import From Folder')
		);
	}
?>

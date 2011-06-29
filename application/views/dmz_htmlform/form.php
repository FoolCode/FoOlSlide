<?php
	if( ! isset($save_button))
	{
		$save_button = 'Save';
	}
	if( ! isset($reset_button))
	{
		$reset_button = FALSE; 
	}
	else
	{
		if($reset_button === TRUE)
		{
			$reset_button = 'Reset';
		}
	}
?>
<?php if( ! empty($object->error->all)): ?>
<div class="error">
	<p>There was an error saving the form.</p> 
	<ul><? foreach($object->error->all as $k => $err): ?>
		<li><?php echo $err ?></li>
		<? endforeach; ?>
	</ul>
</div>
<?php endif; ?>

<form action="<?php echo $this->config->site_url($url) ?>" method="post">
<table class="form">
<?php echo $rows ?>
	<tr class="buttons">
		<td colspan="2"><input type="submit" value="<?php echo $save_button ?>" /><?
			if($reset_button !== FALSE)
			{
				?> <input type="reset" value="<?php echo $reset_button ?>" /><?
			}		
		?></td>
	</tr>
</table>
</form>
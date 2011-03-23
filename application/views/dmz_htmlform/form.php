<?
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
<? if( ! empty($object->error->all)): ?>
<div class="error">
	<p>There was an error saving the form.</p> 
	<ul><? foreach($object->error->all as $k => $err): ?>
		<li><?= $err ?></li>
		<? endforeach; ?>
	</ul>
</div>
<? endif; ?>

<form action="<?= $this->config->site_url($url) ?>" method="post">
<table class="form">
<?= $rows ?>
	<tr class="buttons">
		<td colspan="2"><input type="submit" value="<?= $save_button ?>" /><?
			if($reset_button !== FALSE)
			{
				?> <input type="reset" value="<?= $reset_button ?>" /><?
			}		
		?></td>
	</tr>
</table>
</form>
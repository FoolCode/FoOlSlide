<?
	if(!isset($row_id))
	{
		if( ! empty($id))
		{
			$row_id = ' id="row_' . $id . '"';
		}
		else
		{
			$row_id = '';
		}
	}
	else
	{
		$row_id = ' id="' . $row_id .'"';
	}
	
	if(!isset($label_for))
	{
		if( ! empty($id))
		{
			$label_for = ' for="' . $id . '"';
		}
		else
		{
			$label_for = '';
		}
	}
	else
	{
		$label_for = ' for="' . $label_for .'"';
	}
	
	if( ! empty($row_class))
	{
		$row_class = ' ' . $row_class;
	}
	else
	{
		$row_class = '';
	}
	
	if( ! empty($error))
	{
		$row_class .= ' error';
	}
	
	if($required)
	{
		$row_class .= ' required';
	}

?>
	<tr class="row<?= $row_class ?>"<?= $row_id ?>>
		<td class="label"><label<?= $label_for ?>><?= $label ?>:</label></td>
		<td class="field">
			<?= $content ?>
			<? /*
			// Enable this section to print errors out for each row.
			if( ! empty($error)): ?>
			<span class="error"><?= $error ?></span>
			<? endif; */ ?>

		</td>
	</tr>

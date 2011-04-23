<div class='mainInfo'>
	
	<div id="infoMessage"><?php echo $message;?></div>
	
	<?php	
			echo buttoner();
            echo form_open();        
            echo $table;
			//echo form_close();
    
	//echo form_open();  
			?>
	<br/><br/>
	<br/>
	<table class="form">
		<tr>
			<td>Group</td>
			<td><?php echo $dropdown; ?></td>
		</tr>
		<tr>
			<td></td>
			<td><?php echo form_submit('submit', 'Save'); ?></td>
		</tr>
	</table>
	<?php
	echo form_close();
	?>
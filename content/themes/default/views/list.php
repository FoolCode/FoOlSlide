<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
?>

<div class="panel">
	<div class="list">
		<div class="title">
			<?php echo _('List of the available comics'); ?>
		</div>
		<?php
		foreach ($comics as $key => $comic) {
			echo '<div class="element">
					<div class="title">' . $comic->url() . '</div>
					<div class="meta_r">' . $comic->edit_url() . '</div>
				</div>';
		}
		
		echo prevnext('/reader/list/', $comics);
		?>
	</div>
</div>
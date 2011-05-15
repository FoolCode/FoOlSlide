<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed'); ?>

<div class="panel">
	<div class="large comic">
		<div class="title">
			<?php echo $comic->name; ?>
		</div>
		<?php if ($comic->get_thumb()): ?><div class="thumbnail">
				<img src="<?php echo $comic->get_thumb(); ?>" />
			</div><?php endif; ?>
		<div class="info">
			<ul>
				<li><?php echo '<b>'._('Title').'</b>: '.$comic->name; ?></li>
				<li><?php echo '<b>'._('Description').'</b>: '.$comic->description; ?></li>
			</ul>
		</div>
	</div>


	<div class="list">
		<div class="title">
			<?php echo _('All chapters available for') . ' ' . $comic->name; ?>
		</div>
		<?php
		foreach ($chapters as $key => $chapter) {
			echo '<div class="element">
					<div class="title">' . $chapter->url() . '</div>
					<div class="meta_r">' . _('by') . ' ' . $chapter->team_url() . ' ' . _('on') . ' ' . $chapter->date() . ' ' . $chapter->edit_url() . '</div>
				</div>';
		}
		?>
	</div>
</div>
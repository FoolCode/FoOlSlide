<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed'); ?>

	<div class="large comic">
		<h1 class="title">
			<?php echo $comic->name; ?>
		</h1>
		<?php if ($comic->get_thumb()): ?><div class="thumbnail">
				<img src="<?php echo $comic->get_thumb(); ?>" />
			</div><?php endif; ?>
		<div class="info">
			<ul>
				<li><?php echo '<b>'._('Title').'</b>: '.$comic->name; ?></li>
				<?php if ($comic-altname): ?>
				<li><?php echo '<b>'._('Alt. Title').'</b>: '.$comic->altname; ?><li>
				<?php endif; ?>
				<li><?php echo '<b>'._('Author').'</b>: '.$comic->author; ?></li>
				<?php if ($comic->artist): ?>
				<li><?php echo '<b>'._('Artist').'</b>: '.$comic->artist; ?></li>
				<?php endif; ?>
				<li><?php echo '<b>'._('Status').'</b>: '.$comic->status; ?></li>
				<li><?php echo '<b>'._('Description').'</b>: '.$comic->description; ?></li>
				<?php if ($comic->genres): ?>
				<li><?php echo '<b>'._('Genres').'</b>: '.$comic-genres; ?></li>
				<?php endif; ?>
				<li><?php echo '<b>'._('Published in').'</b>: '.$comic-publisher; ?></li>
				
			</ul>
		</div>
	</div>


	<div class="list">
		<div class="title">
			<?php echo _('All chapters available for') . ' ' . $comic->name; ?>
		</div>
		<?php
		foreach ($chapters as $key => $chapter) {
			echo '<div class="element">'.$chapter->download_url(NULL, 'fleft small').'
					<div class="title">' . $chapter->url() . '</div>
					<div class="meta_r">' . _('by') . ' ' . $chapter->team_url() . ', ' . $chapter->date() . ' ' . $chapter->edit_url() . '</div>
				</div>';
		}
		?>
	</div>

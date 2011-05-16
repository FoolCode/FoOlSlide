<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="list">
	<div class="title"><a href="<?php echo site_url('/reader/latest/') ?>"><?php echo _('Latest released chapters')?>:</a></div>
     <?php
		$current_comic = "";
		$current_comic_closer = "";
		
		
		// Let's loop over every chapter. The array is just $chapters because we used get_iterated(), else it would be $chapters->all
		foreach($chapters as $key => $chapter)
		{
			if ($current_comic != $chapter->comic_id)
			{
				if ($key > 0) echo '</div>';
				echo '<div class="group"><div class="title">'.$chapter->comic->url().'</div>';
				$current_comic = $chapter->comic_id;
			}
			
			echo '<div class="element">
					<div class="title">'.$chapter->url().'</div>
					<div class="meta_r">' . _('by') . ' ' . $chapter->team_url() . ' ' . _('on') . ' ' . $chapter->date() . ' ' . $chapter->edit_url() . '</div>
				</div>';
			
		}
		
		// Closing the last comic group
		echo '</div>';
	?>
</div>
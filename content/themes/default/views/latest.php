<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="panel">
	

				<div class="sidebar">
					<iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Ffoolrulez&amp;width=240&amp;colorscheme=light&amp;show_faces=false&amp;stream=true&amp;header=true&amp;height=427" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:240px; height:427px; background:#fff; background:rgba(255,255,255,0.7)" allowTransparency="true"></iframe>
				</div>	

<div class="list">
	<div class="title">Latest released chapters:</div>
     <?php
		$current_comic = "";
		$current_comic_closer = "";
		
		
		// Let's loop over every chapter. The array is just $chapters because we used get_iterated(), else it would be $chapters->all
		foreach($chapters as $key => $chapter)
		{
			if ($current_comic != $chapter->comic_id)
			{
				if ($key > 0) echo '</div>';
				echo '<div class="group"><div class="title">'.$chapter->comic_name.'</div>';
				$current_comic = $chapter->comic_id;
			}
			
			echo '<div class="element">
					<div class="title">'.$chapter->url().'</div>
					<div class="meta_r">'.$chapter->created.'</div>
				</div>';
	 
		}
		
		// Closing the last comic group
		echo '</div>';
	?>
</div>

	
	
</div>
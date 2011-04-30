<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="panel">
	

				<div class="sidebar">
					<iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Ffoolrulez&amp;width=240&amp;colorscheme=light&amp;show_faces=false&amp;stream=true&amp;header=true&amp;height=427" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:240px; height:427px; background:#fff; background:rgba(255,255,255,0.7)" allowTransparency="true"></iframe>
				</div>	

<div class="list">
	<div class="title">Latest released chapters:</div>
     <?php
	 
		// Create a "Chapter" object. It can contain more than one chapter!
		$chapters = new Chapter();
		
		// With each, get the comic they depends from
		$chapters->include_related('comic');
		
		// Lets group these 25 releases by comic, so it looks like less of a mess.
		$chapters->order_by_related('comic', 'name');
		
		// Select the latest 25 released chapters
		$chapters->order_by('created', 'DESC')->limit(25);
		
		// Get the chapters! Let's use get_iterated() instead of get() to save some RAM
		$chapters->get();
		
		
		
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
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="list">
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
		$chapters->get_iterated();
		
		
		
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
					<div class="title">Chapter '.$chapter->chapter .'</div>
					<div class="meta_r">'.$chapter->created.'</div>
				</div>';
	 
		}
		
		// Closing the last comic group
		echo '</div>';
	?>
</div>
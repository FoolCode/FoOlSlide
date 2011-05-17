<div data-role="content">	
	<ul data-role="listview" data-theme="c" data-dividertheme="b">
		
		<?php
		$current_comic = "";
		$current_comic_closer = "";

		// Let's loop over every chapter. The array is just $chapters because we used get_iterated(), else it would be $chapters->all
		foreach ($chapters as $key => $chapter) {
			if ($current_comic != $chapter->comic_id) {
				echo '<li data-role="list-divider">' . $chapter->comic->title() . '</li>';
				$current_comic = $chapter->comic_id;
			}

			echo '<li>' . $chapter->url() . '</li>';
		}

		?>
	</ul>
</div><!-- /content -->



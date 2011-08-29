<div data-role="content">
	<ul data-role="listview" data-theme="c" data-dividertheme="b">

		<?php

		// Let's loop over every chapter. The array is just $chapters because we used get_iterated(), else it would be $chapters->all
		foreach ($comics as $key => $comic) {
			echo '<li>';
			if($comic->get_thumb() )echo '<img src="'.$comic->get_thumb().'" class="ui-li-thumb"/>';
			echo $comic->url();
			echo '</li>';
		}
		?>
	</ul>
</div><!-- /content -->

<?php echo mobile_prevnext('/reader/list/', $comics); ?>
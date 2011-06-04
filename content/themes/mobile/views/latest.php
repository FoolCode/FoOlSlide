	<div data-role="navbar" data-theme="a">
		<ul>
			<li data-icon="star"><a href="<?php echo site_url('/reader/list/') ?>"><?php echo _("Go to series list") ?></a></li>
		</ul>
	</div><!-- /navbar -->
<div data-role="content">

	<ul data-role="listview" data-theme="c" data-dividertheme="b">

		<?php

		// Let's loop over every chapter. The array is just $chapters because we used get_iterated(), else it would be $chapters->all
		foreach ($chapters as $key => $chapter) {
			echo '<li><a href="'.$chapter->href().'"><h3>' . $chapter->comic->title() . '</h3><p>'.$chapter->title().'</p></a></li>';
		}
		?>
	</ul>
</div><!-- /content -->



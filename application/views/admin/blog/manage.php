<?php
$CI =& get_instance();
$CI->buttoner = array(
	array(
		'href' => site_url('/admin/blog/add_new/'),
		'text' => _('Add Post')
	)
);
?>

<div class="table" style="padding-bottom: 15px">
	<h3 style="float: left"><?php echo _('Posts Information'); ?></h3>
	<span style="float: right; padding: 5px">
		<div class="smartsearch">
		<?php
		echo form_open(site_url('/admin/blog/manage/'));
		echo form_input(array('name'=>'search', 'placeholder' => _('To search, write and hit enter')));
		echo form_close();
		?>
		</div>
	</span>
	<hr class="clear"/>
	<?php echo buttoner(); ?>

	<div class="list posts">
		<?php
		foreach ($posts as $post)
		{
			echo '<div class="item">
				<div class="title"><a href="'.site_url("admin/blog/post/".$post->stub).'">'.$post->name.'</a></div>
				<div class="smalltext">'._('Quick tools').':
					<a href="'.site_url("admin/blog/delete/post/".$post->id).'" onclick="confirmPlug(\''.site_url("admin/blog/delete/post/".$post->id).'\', \''._('Do you really want to delete this post?').'\'); return false;">'._('Delete').'</a> |
					<a href="'.site_url("blog/".$post->stub).'">'._('Read').'</a>
				</div>';
			echo '</div>';
		}
		?>
	</div>
<?php
	if ($posts->paged->total_pages > 1)
	{
?>
	<div class="pagination" style="margin-bottom: -5px">
		<ul>
		<?php
			if ($posts->paged->has_previous)
				echo '<li class="prev"><a href="' . site_url('admin/blog/manage/'.$posts->paged->previous_page) . '">&larr; ' . _('Prev') . '</a></li>';
			else
				echo '<li class="prev disabled"><a href="#">&larr; ' . _('Prev') . '</a></li>';

			$page = 1;
			while ($page <= $posts->paged->total_pages)
			{
				if ($posts->paged->current_page == $page)
					echo '<li class="active"><a href="#">' . $page . '</a></li>';
				else
					echo '<li><a href="' . site_url('admin/blog/manage/'.$page) .'">' . $page . '</a></li>';
				$page++;
			}

			if ($posts->paged->has_next)
				echo '<li class="next"><a href="' . site_url('admin/blog/manage/'.$posts->paged->next_page) . '">' . _('Next') . ' &rarr;</a></li>';
			else
				echo '<li class="next disabled"><a href="#">' . _('Next') . ' &rarr;</a></li>';
		?>
		</ul>
	</div>
<?php
	}
?>
</div>

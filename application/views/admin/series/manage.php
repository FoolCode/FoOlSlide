<div style="float: right;">
<?php
echo form_open(site_url('/admin/series/manage/'));
echo form_input(array('name'=>'search', 'placeholder' => _('To search, write and hit enter')));
echo form_close();
?>
</div>

<?php
$CI =& get_instance();
$CI->buttoner = array(
	array(
		'href' => site_url('/admin/series/add_new/'),
		'text' => _('Add serie')
	)
);
			
echo buttoner();
?>
<div class='list comics'>

<?php


    foreach ($comics as $item)
    {
        echo '<div class="item">
                <div class="title"><a href="'.site_url("admin/series/serie/".$item->stub).'">'.$item->name.'</a></div>';
                //echo '<div class="smalltext">'._('Quick tools').'</div>';
             echo '</div>';
    }

?>

<?php
	if ($comics->paged->total_pages > 1)
	{
?>
	<div class="pagination">
		<ul>
		<?php
			if ($comics->paged->has_previous)
				echo '<li class="prev"><a href="' . site_url('admin/series/manage/'.$comics->paged->previous_page) . '">&larr; ' . _('Prev') . '</a></li>';
			else
				echo '<li class="prev disabled"><a href="#">&larr; ' . _('Prev') . '</a></li>';

			$page = 1;
			while ($page <= $comics->paged->total_pages)
			{
				if ($comics->paged->current_page == $page)
					echo '<li class="active"><a href="#">' . $page . '</a></li>';
				else
					echo '<li><a href="' . site_url('admin/series/manage/'.$page) .'">' . $page . '</a></li>';
				$page++;
			}

			if ($comics->paged->has_next)
				echo '<li class="next"><a href="' . site_url('admin/series/manage/'.$comics->paged->next_page) . '">' . _('Next') . ' &rarr;</a></li>';
			else
				echo '<li class="next disabled"><a href="#">' . _('Next') . ' &rarr;</a></li>';
		?>
		</ul>
	</div>
<?php
	}
?>
</div>
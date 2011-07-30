<div class="smartsearch">
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
<div class='navi'>
<?php

    if($comics->paged->has_previous)
    {
        ?>
    <a href="<?php echo site_url('admin/series/manage/') ?>">«« <?php echo _('First') ?></a>
    <a href="<?php echo site_url('admin/series/manage/'.$comics->paged->previous_page) ?>">« <?php echo _('Prev') ?></a>
        <?php
    }
    if($comics->paged->has_next)
    {
        ?>
    <a href="<?php echo site_url('admin/series/manage/'.$comics->paged->next_page) ?>"><?php echo _('Next') ?> »</a>
    <a href="<?php echo site_url('admin/series/manage/'.$comics->paged->total_pages) ?>"><?php echo _('Last'); ?> »»</a>
        <?php
    }

?>

</div>

</div>
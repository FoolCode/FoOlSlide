<div class='list comics'>

<?php


    foreach ($comics as $item)
    {
        echo '<div class="item">
                <div class="title"><a href="'.site_url("admin/comics/comic/".$item->stub).'">'.$item->name.'</a></div>
                <div class="smalltext">Quick tools</div>';
             echo '</div>';
    }

?>
<div class='navi'>
<?php

    if($comics->paged->has_previous)
    {
        ?>
    <a href="<?= site_url('admin/comics/manage/') ?>">«« First</a>
    <a href="<?= site_url('admin/comics/manage/'.$comics->paged->previous_page) ?>">« Prev</a>
        <?
    }
    if($comics->paged->has_next)
    {
        ?>
    <a href="<?= site_url('admin/comics/manage/'.$comics->paged->next_page) ?>">Next »</a>
    <a href="<?= site_url('admin/comics/manage/'.$comics->paged->total_pages) ?>">Last »»</a>
        <?
    }

?>

</div>

</div>
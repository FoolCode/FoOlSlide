<?php

$this->buttoner[] = array(
	'text' => 'Delete chapter',
	'href' => site_url('/admin/comics/delete/chapter/'.$chapter->id),
	'plug' => 'Do you really want to delete this chapter and its pages?'
);

echo buttoner();

echo form_open();
echo $table;
echo form_close();

?>

<div class="section">Pages:</div>
<div class="smalltext"><a href="<?= site_url('admin/comics/'.$comic->stub.'/add_chapter'); ?>" onclick="slideToggle('#addnew_page'); return false;">Add new</a>
 | <a href="#" onclick="confirmPlug('<?php echo site_url('admin/comics/remove_pages/'.$comic->id.'/'.$chapter->id.'/all'); ?>', 'Are you sure that you want to remove all the images related to this chapter?'); return false;">Remove all pages</a>
</div>

<div id="addnew_page" style="display:none">
<?php
    echo form_open_multipart('admin/comics/upload/compressed_chapter');
    echo form_hidden('chapter_id', $chapter->id);
?>
    <table class="form">
     <tr>
        <td>Via compressed archive (ZIP only)</td>
        <td>
            <?php
                $data = array(
                    'name'        => 'userfile',
                    'id'          => 'chapter_userfile',
                );

                echo form_upload($data);
            ?>
        </td>
    </tr>
    <tr>
        <td><?php echo form_reset(array("value" => "Reset fields")) ?></td>
        <td><?php echo form_submit(array("value" => "Upload")); ?></td>
    </tr>
    </table>
</div>

<div class="list pages">
    <table>
        <tr>
<?php
    $count = 0;
    foreach ($pages as $item)
    {
        $count++;
        echo '<td>
                <a href="'.site_url().'"><div class="controls">Delete</div></a>
                <img src="'.$item["thumb_url"].'" />
             </td>';
        if ($count%4==0) echo '</tr><tr>';
    }

?>     </tr>
    </table>

</div>
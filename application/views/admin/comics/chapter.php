<div class="smalltext">
    <a href="<?php site_url('/admin/comics/'.$comic->stub.'/edit_comic'); ?>" onclick="slideToggle('#edit_chapter'); slideToggle('#info_chapter'); return false;">Edit chapter data</a>
    | <a href="#" onclick="confirmPlug('<?php echo site_url('admin/comics/remove/chapter/'.$chapter->id); ?>', 'Do you really want to delete this chapter and all its pages?'); return false;">Delete chapter</a>
</div>
<div id="info_chapter"><table class="form">
    <tr>
        <td>
            Title
        </td>
        <td>
            <?= $chapter->name != "" ? $chapter->name : "N/A" ?>
        </td>
    </tr>
    <tr>
        <td>
            Chapter
        </td>
        <td>
            <?= $chapter->chapter ?>
        </td>
    </tr>
    <tr>
        <td>
            Subchapter
        </td>
        <td>
            <?= $chapter->subchapter ?>
        </td>
    </tr>
    <tr>
        <td>
            Group(s)
        </td>
        <td>
            <?php
                foreach($teams as $item)
                {
                    echo $item->name."<br/>";
                }
            ?>
        </td>
    </tr>
    <tr>
        <td>
            Description
        </td>
        <td>
            <?= $chapter->description != "" ? $chapter->description : "N/A" ?>
        </td>
    </tr>
    <tr>
        <td>
            Hidden
        </td>
        <td>
            <?= $chapter->hidden == 1 ? "Yes" : "No"; ?>
        </td>
    </tr>

</table>
</div>

<div id="edit_chapter" style="display:none;">
<?php
    echo form_open('admin/comics/add/chapter', array("id" => "chapter_create"));
    echo form_hidden('chapter_id', $chapter->id);
?>
<table class="form">
    <tr>
        <td>Comic title</td>
        <td>
            <?php
                $data = array(
                    'name'        => 'name',
                    'id'          => 'comic_title',
                    'maxlength'   => '200',
                    "placeholder" => "required",
                    'value' => $comic->name
                );

                echo form_input($data);
            ?>
        </td>
    </tr>
    <tr>
        <td>Hidden from public</td>
        <td>
            <?php
                $data = array(
                    'name'        => 'hidden',
                    'id'          => 'comic_hidden',
                    'value'       => '1'
                );
                if ($comic->hidden == 1) $data["checked"] = "checked";

                echo form_checkbox($data);
            ?>
        </td>
    </tr>
    <tr>
        <td>Description</td>
        <td>
            <?php
                $data = array(
                    'name'        => 'description',
                    'id'          => 'comic_description',
                    'value'       => '',
                    'row'   => '2',
                    'columns' => '50',
                    'value' => $comic->description
                );

            echo form_textarea($data);
            ?>
        </td>
    </tr>
    <tr>
        <td>Thumbnail image</td>
        <td>
            <?php
                $data = array(
                    'name'        => 'title',
                    'id'          => 'comic_title',
                    'value'       => '',
                    'maxlength'   => '200',
                );

                echo form_upload($data);
            ?>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><?php echo form_submit(array("value" => "Submit changes")); ?></td>
    </tr>
</table>
<?php echo form_close(); ?>
</div>



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
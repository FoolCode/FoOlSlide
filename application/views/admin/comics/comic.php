<div class="smalltext">
    <a href="<?= site_url('admin/comics/'.$comic->stub.'/edit_comic'); ?>" onclick="slideToggle('#edit_comic'); slideToggle('#info_comic'); return false;">Edit comic data</a>
    | <a href="#" onclick="confirmPlug('<?php echo site_url('admin/comics/remove/comic/'.$comic->id); ?>', 'Do you really want to delete this comic and all its chapters and pages?'); return false;">Delete comic</a>
</div>

<div id="info_comic"><table class="form">
    <tr>
        <td>
            Description
        </td>
        <td>
            <?= $comic->description != "" ? $comic->description : "N/A" ?>
        </td>
    </tr>
    <tr>
        <td>
            Hidden
        </td>
        <td>
            <?= $comic->hidden == 1 ? "Yes" : "No"; ?>
        </td>
    </tr>
    <tr>
        <td>
            Thumbnail
        </td>
        <td>
            <?php echo ($comic->thumbnail == "" ? "N/A" : "<img src='".$comic->get_thumb()."' />"); ?>
        </td>
    </tr>

</table>
</div>
<div id="edit_comic" style="display:none;">
<?php
    echo form_open('admin/comics/add/comic', array("id" => "comic_create"));
    echo form_hidden('comic_id', $comic->id);
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










<div class="section">Chapters:</div>
<div class="smalltext"><a href="<?= site_url('/admin/comics/'.$comic->stub.'/add_chapter'); ?>" onclick="slideToggle('#addnew_chapter'); return false;">Add new</a></div>

<div id="addnew_chapter" style="display:none">
<?php
    echo form_open('admin/comics/add/chapter', array("id" => "comic_create"));
    echo form_hidden('comic_id', $comic->id);
?>
    <table class="form">
     <tr>
        <td>Chapter title</td>
        <td>
            <?php
                $data = array(
                    'name'        => 'name',
                    'id'          => 'comic_title',
                    'maxlength'   => '200',
                );

                echo form_input($data);
            ?>
        </td>
    </tr>
    <tr>
        <td>Chapter number</td>
        <td>
            <?php
                $data = array(
                    'name'        => 'number',
                    'id'          => 'comic_title',
                    'maxlength'   => '200',
                    "placeholder" => "required numeric",
                    'required' => ''
                );

                echo form_input($data);
            ?>
        </td>
    </tr>
    <tr>
        <td>Subchapter number</td>
        <td>
            <?php
                $data = array(
                    'name'        => 'subchapter',
                    'id'          => 'comic_title',
                    'maxlength'   => '200',
                );

                echo form_input($data);
            ?>
        </td>
    </tr>
    <tr>
        <td>Team(s)</td>
        <td>
            <?php
                $data = array(
                    'name'        => 'groups[]',
                    'id'          => 'comic_title',
                    'maxlength'   => '200',
                );

                echo form_input($data);
            ?>
            <br/>
            <div class="smalltext">Add more teams</div>
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
                );

            echo form_textarea($data);
            ?>
        </td>
    <tr>
        <td></td>
        <td><?php echo form_submit(array("value" => "Add chapter")); ?></td>
    </tr>
    </table>
</div>

<div class="list chapters">

<?php

    foreach ($chapters as $item)
    {
        echo '<div class="item">
                <div class="title"><a href="'.site_url("admin/comics/comic/".$comic->stub."/".$item->id).'">'. (($item->name != "") ? $item->name : "Chapter ".$item->chapter.".".$item->subchapter).'</a></div>
                <div class="smalltext info">
                    Chapter #'.$item->chapter.'
                    Sub #'.$item->subchapter.'
                    By <a href="'.site_url("/admin/users/team/".$item->team_stub).'">'.$item->team_name.'</a>
                </div>
                <div class="smalltext">
                    <a href="#" onclick="">Quick tools</a>
                </div>';
             echo '</div>';
    }

?>
    
</div>



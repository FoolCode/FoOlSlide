<?php echo form_open_multipart('admin/comics/add/comic', array("id" => "comic_create")); ?>
<table class="form">
    <tr>
        <td>Comic title</td>
        <td>
            <?php
                $data = array(
                    'name'        => 'name',
                    'id'          => 'comic_title',
                    'maxlength'   => '200',
                    "placeholder" => "required"
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
                    'value'       => '1',
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
                    'columns' => '50'
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
                    'name'        => 'userfile',
                    'id'          => 'comic_thumb',
                );

                echo form_upload($data);
            ?>
        </td>
    </tr>
    <tr>
        <td><?php echo form_reset(array("value" => "Reset fields")); ?></td>
        <td><?php echo form_submit(array("value" => "Submit")); ?></td>
    </tr>
</table>
<?php echo form_close(); ?>
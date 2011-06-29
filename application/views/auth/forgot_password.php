<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("auth/forgot_password");?>

<table class="form">
    <tr>
      	<td>Email:</td>
        <?php $email["placeholder"] = "required"; ?>
      	<td><?php echo form_input($email);?></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?php echo form_submit(NULL, 'Submit'); ?>
        </td>
    </tr>
</table>

<?php echo form_close();?>
<div class="smalltext">
    <a href="<?php echo site_url('auth/login'); ?>">Back to login</a>
</div>
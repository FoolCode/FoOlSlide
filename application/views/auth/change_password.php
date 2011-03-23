<h1>Change Password</h1>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("auth/change_password");?>
<table class="form">
    <tr>
        <td>Old password</td>
        <td><?php $old_password["placeholder"] = "required";
            form_input($old_password); ?>
        </td>
    </tr>
    <tr>
        <td>New password</td>
        <td><?php $new_password["placeholder"] = "required";
            form_input($new_password); ?>
        </td>
    </tr>
    <tr>
        <td>Confirm new password</td>
        <td><?php $new_password_confirm["placeholder"] = "required";
            form_input($new_password_confirm); ?>
        </td>
    </tr>
    <tr>
        <td><?= form_reset(); ?></td>
        <td><?php echo form_submit('submit', 'Change');?></td>
    </tr>
      
<?php echo form_close();?>
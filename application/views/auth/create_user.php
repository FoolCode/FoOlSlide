    <div class='mainInfo register_form'>

	<div id="infoMessage"><?php echo $message;?></div>
	
    <?php echo form_open("auth/create_user");?>

<table class="form">
    <tr>
        <td>Nickname</td>
        <td><?php $nickname["placeholder"] = "required";
                echo form_input($nickname); ?></td>
    </tr>
    <tr>
        <td>Email</td>
        <td><?php $email["placeholder"] = "required";
                echo form_input($email); ?></td>
    </tr>
    <tr>
        <td>Password</td>
        <td><?php $password["placeholder"] = "required";
                echo form_input($password); ?></td>
    </tr>
    <tr>
        <td>Confirm password</td>
        <td><?php $password_confirm["placeholder"] = "required";
                echo form_input($password_confirm); ?></td>
    </tr>
    <tr>
        <td><?php echo form_reset(NULL,'Reset fields') ?> </td>
        <td><?php echo form_submit('submit', 'Submit');?></td>
    </tr>
</table>

      
    <?php echo form_close();?>

</div>

<div class="smalltext">
    <a href="<?php echo site_url('auth/login'); ?>">Back to login</a>
</div>

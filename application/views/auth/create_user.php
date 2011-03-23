<div class='mainInfo'>

	<h1>Create User</h1>
	<p>Please enter the users information below.</p>
	
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
        <td><?= form_reset() ?> </td>
        <td><?php echo form_submit('submit', 'Create User');?></td>
    </tr>
</table>

      
    <?php echo form_close();?>

</div>

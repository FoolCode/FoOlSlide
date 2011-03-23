<div id="login" class='mainInfo'>

    <div class="pageTitleBorder"></div>
	
	<div id="infoMessage"><?php echo $message;?></div>
	
    <?php echo form_open("auth/login");?>
<table class="form">
    <tr>
      	<td>Username:</td>
        <?php $username["placeholder"] = "required"; ?>
      	<td><?php echo form_input($username);?></td>
    </tr>
    <tr>
      	<td>Password:</td>
        <?php $password["placeholder"] = "required"; ?>
      	<td><?php echo form_input($password);?></td>
    </tr>
    <tr>
	<td>Remember Me:</td>
	<td><?php echo form_checkbox('remember', '1', FALSE);?></td>
    </tr>
    <tr>
    <td></td>
    <td><?php echo form_submit('submit', 'Login');?></td>
    </tr>
</table>
      
    <?php echo form_close();?>

</div>

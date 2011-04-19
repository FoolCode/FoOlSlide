<div class='mainInfo'>	
	<div id="infoMessage"><?php echo $message;?></div>
	
	<table class="form">
                
                <?php
                    $rows = array();
                    $rows[] = array('Username', 'Email', 'Group', 'Status');
                    foreach ($users as $user)
                    {
                        $rows[] = array($user['username'], $user['email'], $user['group_description'], ($user['active']) ? anchor("auth/deactivate/".$user['id'], 'Active') : anchor("auth/activate/". $user['id'], 'Inactive'));
                    }
                    echo tabler($rows, TRUE, FALSE)
                ?>
	</table>
	
</div>

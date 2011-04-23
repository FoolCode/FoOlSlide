<div class='mainInfo'>	
	<div id="infoMessage"><?php echo $message;?></div>
	
                
                <?php
                    $rows = array();
                    $rows[] = array('Username', 'Email', 'Group', 'Status');
                    foreach ($users as $user)
                    {
                        $rows[] = array('<a href="'.site_url('/admin/users/user/'.$user['id']).'">'.$user['username'].'</a>', $user['email'], $user['group_description'], ($user['active']) ? anchor("auth/deactivate/".$user['id'], 'Active') : anchor("auth/activate/". $user['id'], 'Inactive'));
                    }
                    echo tabler($rows, TRUE, FALSE)
                ?>
	
</div>

<div class="incontent panel">
	<div class="left">
		<?php
		if (empty($teams))
			echo _("You aren't part of any team.");
		else
		{
			echo _("You're part of the following teams:");
			echo '<table>';
			foreach ($teams as $key => $team)
			{
				echo '<tr>';
				echo '<td>' . $team["name"] . '</td>';
				echo '<td style="text-align:right"><a href="'.site_url('/account/leave_team/'.$team["stub"]).'">'._("Leave").'</a></td>';
				echo '</tr>';
			}
			echo '</table>';
		}
		
		echo '<br/><br/>';
		
		if (!empty($teams_leaded))
		{
			echo _("You're the leader of the following teams:");
			echo '<table>';
			foreach ($teams_leaded as $key => $team)
			{
				echo '<tr>';
				echo '<td>' . $team["name"] . '</td>';
				echo '<td style="text-align:right"><a href="'.site_url('/account/leave_leadership/'.$team["stub"]).'">'._("Leave leadership").'</a></td>';
				echo '</tr>';
			}
			echo '</table>';
		}
		?>
	</div>
	<div class="right">
		<?php
		$team_name = array(
			'name' => 'team_name',
			'id' => 'team_name'
		);

		echo form_open();
		echo form_hidden('action', 'apply_with_team_name');
		echo _("Insert the exact name of the team you'd like to apply to:");
		?>
		<br/><br/>
		<div class="formgroup">
			<div><?php echo form_label(_('Team name (case sensitive)'), $team_name['id']); ?></div>
			<div><?php echo form_input($team_name); ?></div>
			<div style="color: red;"><?php echo form_error($team_name['name']); ?><?php echo isset($errors[$team_name['name']]) ? $errors[$team_name['name']] : ''; ?></div>
		</div>
		<div class="formgroup">
			<div><?php echo form_submit('submit', _('Apply')); ?></div>
		</div>
	</div>
</div>
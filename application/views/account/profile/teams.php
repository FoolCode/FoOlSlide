<div class="incontent panel">
	<div class="left">
	<?php
	if (empty($teams))
		echo _("You aren't part of any team.");
	else
	{
		echo "You're part of the following teams:";
		echo '<table>';
		foreach($teams as $key => $team)
		{
			echo '<tr>
					<td>'.$team["name"].'</td>
				</tr>';
		}
		echo '</table>';
	}
	?>
	</div>
	<div class="right"></div>
</div>
<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
?>

<div class="list">
	<div class="title">
		<?php echo '<a href="' . site_url('/reader/team/' . $team->stub) . '">' . _('Team\'s page') . ': ' . $team->name . '</a>'; ?>
	</div>

	<?php
	echo '<div class="group">
					<div class="title">' . _('Informations') . '</span></div>
				';
	echo '<div class="element">
					<div class="title">' . _("URL") . ': <a href="' . $team->url . '">' . $team->url . '</a></div></div>
						<div class="element">
					<div class="title">' . _("IRC") . ': <a href="' . parse_irc($team->irc) . '">' . $team->irc . '</a></div></div>
						<div class="element">
					<div class="title">' . _("Twitter") . ': <a href="http://twitter.com/' . $team->twitter . '">http://twitter.com/' . $team->twitter . '</a></div></div>
						<div class="element">
					<div class="title">' . _("Facebook") . ': <a href="' . $team->facebook . '">' . $team->facebook . '</a></div>
				</div></div>';


	echo '<div class="group">
					<div class="title">' . _('Team leaders') . '</span></div>
				';
	if (count($members) == 0) {
		echo '<div class="element">
					<div class="title">' . _("No leaders in this team") . '.</div>
				</div></div>';
	}
	else
		foreach ($members->all as $key => $member) {
			if (!$member->is_leader)
				continue;
			echo '<div class="element">
					<div class="title">' . get_gravatar($member->email, 50, NULL, NULL, TRUE) . ' ' . $member->username . '</div>
				</div></div>';
		}

	echo '<div class="group">
					<div class="title">' . _('Members') . '</span></div>
				';
	if (count($members) == 0) {
		echo '<div class="element">
					<div class="title">' . _("No members in this team") . '.</div>
				</div></div>';
	}
	else
		foreach ($members->all as $key => $member) {
			if ($member->is_leader)
				continue;
			echo '<div class="element">
					<div class="title">' . get_gravatar($member->email, 50, NULL, NULL, TRUE) . ' ' . $member->username . '</div>
				</div></div>';
		}
	?>
</div>

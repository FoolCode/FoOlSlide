<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Admin_Controller extends MY_Controller {

	public function __construct() {
		parent::__construct();

		$this->viewdata["sidebar"] = $this->sidebar();

		// Check if the database is upgraded to the the latest available
		if ($this->tank_auth->is_admin() && $this->uri->uri_string() != '/admin/database/upgrade' && $this->uri->uri_string() != '/admin/database/do_upgrade') {
			$this->config->load('migration');
			$config_version = $this->config->item('migration_version');
			$db_version = $this->db->get('migrations')->row()->version;
			if ($db_version != $config_version) {
				redirect('/admin/database/upgrade/');
			}
			$this->cron();
		}
	}

	function sidebar_val() {
		return $sidebar = array(
	"dashboard" => array(
		"name" => _("Dashboard"),
		"level" => "member",
		"default" => "index",
		"icon" => 91,
		"content" => array(
			"index" => array("level" => "member", "name" => "Dashboard"),
		)
	),
	"comics" => array(
		"name" => _("Comics"),
		"level" => "mod",
		"default" => "manage",
		"icon" => 43,
		"content" => array(
			"manage" => array("level" => "mod", "name" => _("Manage")),
			"add_new" => array("level" => "mod", "name" => _("Add new"))
		)
	),
	"members" => array(
		"name" => _("Members"),
		"level" => "member",
		"default" => "you",
		"icon" => 122,
		"content" => array(
			"members" => array("level" => "mod", "name" => _("Member list")),
			"you" => array("level" => "member", "name" => _("Your profile")),
			"teams" => array("level" => "member", "name" => _("Team list")),
			"home_team" => array("level" => "member", "name" => _("Home team")),
			"add_team" => array("level" => "mod", "name" => _("Add team"))
		)
	),
	"preferences" => array(
		"name" => _("Preferences"),
		"level" => "admin",
		"default" => "general",
		"icon" => 116,
		"content" => array(
			"general" => array("level" => "admin", "name" => _("General")),
			"reader" => array("level" => "admin", "name" => _("Reader")),
			"theme" => array("level" => "admin", "name" => _("Theme")),
			"registration" => array("level" => "admin", "name" => _("Registration")),
			"advertising" => array("level" => "admin", "name" => _("Advertising")),
		)
	),
	"upgrade" => array(
		"name" => _("Upgrade"),
		"level" => "admin",
		"default" => "upgrade",
		"icon" => 37,
		"content" => array(
			"upgrade" => array("level" => "admin", "name" => _("Upgrade")),
		)
	)
		);
	}

	public function sidebar() {
		if (!$this->tank_auth->is_logged_in())
			return false;
		$result = "";
		foreach ($this->sidebar_val() as $key => $item) {
			if ($this->uri->segment(2) == $key)
				$active = TRUE;
			else
				$active = FALSE;
			if (($this->tank_auth->is_admin() || $this->tank_auth->is_group($item["level"])) && !empty($item)) {
				$result .= '<div class="collection">';
				$result .= '<div class="group"><a href="' . site_url(array("admin", $key, $item["default"])) . '">
								<img class="icon off" src="' . site_url() . '/assets/glyphish/' . ($active ? 'on' : 'off') . '/' . $item["icon"] . '.png' . '" />
								<img class="icon on" src="' . site_url() . '/assets/glyphish/on/' . $item["icon"] . '.png' . '" />'
								. $item["name"] . '</a></div>';
				foreach ($item["content"] as $subkey => $subitem) {
					if ($active && $this->uri->segment(3) == $subkey)
						$subactive = TRUE;
					else
						$subactive = FALSE;
					if (($this->tank_auth->is_admin() || $this->tank_auth->is_group($subitem["level"]))) {
						//if($subitem["name"] == $_GET["location"]) $is_active = " active"; else $is_active = "";
						$is_active = "";
						$result .= '<a href="' . site_url(array("admin", $key, $subkey)) . '"><div class="element ' . ($subactive?'active':'') . '">' . $subitem["name"] . '</div></a>';
					}
				}
				$result .= '</div>';
			}
		}
		return $result;
	}

	public function cron() {
		if ($this->tank_auth->is_admin()) {
			$last_check = get_setting('fs_cron_autoupgrade');

			// check for updates hourly
			if (time() - $last_check > 3600) {
				$this->db->update('preferences', array('value' => time()), array('name' => 'fs_cron_autoupgrade'));
				$this->load->model('upgrade_model');
				$latest = $this->upgrade_model->check_latest();
				if ($latest[0]) {
					$this->db->update('preferences', array('value' => $latest[0]->version . '.' . $latest[0]->subversion . '.' . $latest[0]->subsubversion), array('name' => 'fs_cron_autoupgrade_version'));
				}
			}

			load_settings();
		}
	}

}
<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Admin_Controller extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->tank_auth->is_logged_in() or redirect('/account/auth/login');
		$this->tank_auth->is_allowed() or show_404();

		$this->viewdata["sidebar"] = $this->sidebar();

		// Check if the database is upgraded to the the latest available
		if ($this->tank_auth->is_admin() && $this->uri->uri_string() != '/admin/database/upgrade' && $this->uri->uri_string() != '/admin/database/do_upgrade')
		{
			$this->config->load('migration');
			$config_version = $this->config->item('migration_version');
			$db_version = $this->db->get('migrations')->row()->version;
			if ($db_version != $config_version)
			{
				redirect('/admin/database/upgrade/');
			}
			$this->cron();
		}
	}


	/*
	 * Non-dynamic sidebar array.
	 * Permissions are set inside
	 * 
	 * @author Woxxy
	 * @return sidebar array
	 */
	function sidebar_val()
	{

		$sidebar = array();

		if (get_setting('fs_balancer_master_url'))
		{
			$sidebar["members"] = array(
				"name" => _("Members"),
				"level" => "member",
				"default" => "members",
				"icon" => 122,
				"content" => array(
					"members" => array("level" => "mod", "name" => _("Member list")),
				)
			);
			$sidebar["balancer"] = array("name" => _("Load balancer"),
				"level" => "admin",
				"default" => "balancers",
				"icon" => 27,
				"content" => array(
					"client" => array("level" => "admin", "name" => _("Client")),
				)
			);
			
			return $sidebar;
		}


		$sidebar["series"] = array(
			"name" => _("Series"),
			"level" => "mod",
			"default" => "manage",
			"icon" => 43,
			"content" => array(
				"manage" => array("level" => "mod", "name" => _("Manage")),
				"add_new" => array("level" => "mod", "name" => _("Add new"))
			)
		);
		$sidebar["members"] = array(
			"name" => _("Members"),
			"level" => "member",
			"default" => "members",
			"icon" => 122,
			"content" => array(
				"members" => array("level" => "mod", "name" => _("Member list")),
				"teams" => array("level" => "member", "name" => _("Team list")),
				"home_team" => array("level" => "member", "name" => _("Home team")),
				"add_team" => array("level" => "mod", "name" => _("Add team"))
			)
		);
		$sidebar["preferences"] = array(
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
		);
		$sidebar["balancer"] = array("name" => _("Load balancer"),
			"level" => "admin",
			"default" => "balancers",
			"icon" => 27,
			"content" => array(
				"balancers" => array("level" => "admin", "name" => _("Master")),
				"client" => array("level" => "admin", "name" => _("Client")),
			)
		);
		$sidebar["sidebar"] = array("name" => _("Upgrade"),
			"level" => "admin",
			"default" => "upgrade",
			"icon" => 37,
			"content" => array(
				"upgrade" => array("level" => "admin", "name" => _("Upgrade")),
			)
		);

		return $sidebar;
	}


	/*
	 * Returns the sidebar code
	 * 
	 * @todo comment this
	 */
	public function sidebar()
	{
		// not logged in users don't need the sidebar
		if (!$this->tank_auth->is_logged_in())
			return false;

		$result = "";
		foreach ($this->sidebar_val() as $key => $item)
		{

			// segment 2 contains what's currently active so we can set it lighted up
			if ($this->uri->segment(2) == $key)
				$active = TRUE;
			else
				$active = FALSE;
			if (($this->tank_auth->is_admin() || $this->tank_auth->is_group($item["level"])) && !empty($item))
			{
				$result .= '<div class="collection">';
				$result .= '<div class="group"><a href="' . site_url(array("admin", $key, $item["default"])) . '">
								<img class="icon off" src="' . site_url() . '/assets/glyphish/' . ($active ? 'on' : 'off') . '/' . $item["icon"] . '.png' . '" />
								<img class="icon on" src="' . site_url() . '/assets/glyphish/on/' . $item["icon"] . '.png' . '" />'
						. $item["name"] . '</a></div>';
				foreach ($item["content"] as $subkey => $subitem)
				{
					if ($active && $this->uri->segment(3) == $subkey)
						$subactive = TRUE;
					else
						$subactive = FALSE;
					if (($this->tank_auth->is_admin() || $this->tank_auth->is_group($subitem["level"])))
					{
						//if($subitem["name"] == $_GET["location"]) $is_active = " active"; else $is_active = "";
						$is_active = "";
						$result .= '<a href="' . site_url(array("admin", $key, $subkey)) . '"><div class="element ' . ($subactive ? 'active' : '') . '">' . $subitem["name"] . '</div></a>';
					}
				}
				$result .= '</div>';
			}
		}
		return $result;
	}


	/*
	 * Controller for cron triggered by admin panel
	 * Currently defaulted crons:
	 * -check for updates
	 * 
	 * @author Woxxy
	 */
	public function cron()
	{
		if ($this->tank_auth->is_admin())
		{
			$last_check = get_setting('fs_cron_autoupgrade');

			// check for updates hourly
			if (time() - $last_check > 3600)
			{
				// update autoupgrade cron time
				$this->db->update('preferences', array('value' => time()), array('name' => 'fs_cron_autoupgrade'));

				// load model
				$this->load->model('upgrade_model');
				// check
				$versions = $this->upgrade_model->check_latest(TRUE);

				// if a version is outputted, save the new version number in database
				if ($versions[0])
				{
					$this->db->update('preferences', array('value' => $versions[0]->version . '.' . $versions[0]->subversion . '.' . $versions[0]->subsubversion), array('name' => 'fs_cron_autoupgrade_version'));
				}
				// reload the settings
				load_settings();
			}
		}
	}


}
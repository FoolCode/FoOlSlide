<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Upgrade extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();

		// only admins should do this
		$this->tank_auth->is_admin() or redirect('admin');

		// we need the upgrade module's functions
		$this->load->model('upgrade_model');

		// page title
		$this->viewdata['controller_title'] = "Upgrade FoOlSlide";
	}


	/*
	 * A page telling if there's an ugrade available
	 * 
	 * @author Woxxy
	 */
	function index()
	{

		// get current version from database
		$data["current_version"] = get_setting('fs_priv_version');

		// check if the user can upgrade by checking if files are writeable
		$data["can_upgrade"] = $this->upgrade_model->check_files();
		if (!$data["can_upgrade"])
		{
			// if there are not writeable files, suggest the actions to take
			$this->upgrade_model->permissions_suggest();
		}

		// look for the latest version available
		$data["new_versions"] = $this->upgrade_model->check_latest();

		// print out
		$this->viewdata["main_content_view"] = $this->load->view("admin/upgrade/index", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}


	/*
	 * This just triggers the upgrade function in the upgrade model
	 * 
	 * @author Woxxy
	 */
	function do_upgrade()
	{

		if (!isAjax())
		{
			return false;
		}

		// triggers the upgrade
		if (!$this->upgrade_model->do_upgrade())
		{
			// clean the cache in case of failure
			$this->upgrade_model->clean();
			// show some kind of error
			log_message('error', 'upgrade.php do_upgrade(): failed upgrade');
			flash_message('error', _('Upgrade failed: check file permissions.'));
		}
		
		// return an url
		echo json_encode(array('href' => site_url('admin/upgrade')));
	}


}
<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Upgrade extends Admin_Controller {

	function __construct() {
		parent::__construct();
		$this->tank_auth->is_logged_in() or redirect('/admin/auth/login');
		$this->tank_auth->is_admin() or redirect('admin');
		$this->load->library('curl');
		$this->load->library('unzip');
		$this->load->model('upgrade_model');
		$this->viewdata['controller_title'] = "Upgrade FoOlSlide";
	}

	function index() {
		$data["version"] = get_setting('fs_priv_version');
		$data["can_upgrade"] = $this->upgrade_model->check_files();
		$data["latest"] = $this->upgrade_model->check_latest();
		
		$this->viewdata["main_content_view"] = $this->load->view("admin/upgrade/index", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}
	
	function do_upgrade() {
		if(!$this->upgrade_model->do_upgrade())
		{
			$this->upgrade_model->clean();
			log_message('error', 'upgrade.php do_upgrade(): failed upgrade');
			flash_message('error', _('Upgrade failed: check file permissions.'));
		}
		redirect('admin/upgrade');
	}


}
<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Database extends Admin_Controller {

	function __construct() {
		parent::__construct();
		if (!$this->input->is_cli_request()) {
			$this->tank_auth->is_logged_in() or redirect('auth/login');
			$this->tank_auth->is_admin() or redirect('admin');
			$this->tank_auth->is_admin() or die(1);
		}
		$this->load->library('migration');
		$this->config->load('migration');
		$this->viewdata['controller_title'] = _("Database");
	}

	function upgrade() {
		if (!$this->tank_auth->is_admin()) {
			show_404();
			return FALSE;
		}

		$db_version = $this->db->get('migrations')->row()->version;
		$config_version = $this->config->item('migration_version');
		$ask_upgrade = FALSE;
		if ($db_version != $config_version) {
			$ask_upgrade = TRUE;
		}

		$this->viewdata['function_title'] = _('Upgrade');
		$data["db_version"] = $db_version;
		$data["config_version"] = $config_version;
		$data["ask_upgrade"] = $ask_upgrade;
		$data["CLI_code"] = 'php ' . FCPATH . 'index.php admin database do_upgrade';

		$this->viewdata["main_content_view"] = $this->load->view("admin/database/upgrade", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}

	function do_upgrade() {
		if (!isAjax())
			return FALSE;
		if (!$this->migration->current()) {
			log_message('error', 'database.php do_upgrade() failed');
			return FALSE;
		}
		echo json_encode(array('href' => site_url('admin/')));
		return TRUE;
	}

}
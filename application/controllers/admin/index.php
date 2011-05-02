<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Index extends Admin_Controller {

	function __construct() {
		parent::__construct();
		$this->tank_auth->is_logged_in() or redirect('/admin/auth/login');
		$this->viewdata['controller_title'] = 'Dashboard';
	}

	function index() {
		redirect('/admin/dashboard/');
	}

}

/* End of file index.php */
/* Location: ./application/controllers/admin/index.php */
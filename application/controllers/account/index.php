<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Index extends Account_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		if(!$this->tank_auth->is_logged_in())
		redirect('/account/');
		redirect('/account/auth/login/');
	}

}

/* End of file index.php */
/* Location: ./application/controllers/admin/index.php */
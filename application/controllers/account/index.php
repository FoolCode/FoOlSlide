<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Index extends Account_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		redirect('/account/auth/');
	}

}

/* End of file index.php */
/* Location: ./application/controllers/admin/index.php */
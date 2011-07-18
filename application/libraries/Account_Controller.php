<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Account_Controller extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->tank_auth->is_logged_in() or redirect('/account/auth/login');
	}
}
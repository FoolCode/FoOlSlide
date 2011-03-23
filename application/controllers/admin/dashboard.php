<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comics extends CI_Controller {

	function __construct()
	{
		parent::__construct();
                $this->ion_auth->logged_in() or redirect('auth/login');
        }
}
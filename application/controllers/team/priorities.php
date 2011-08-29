<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Priorities extends Team_Controller
{
	function __construct()
	{
		parent::__construct();
		

		// if this is a load balancer, let's not allow people in the series tab
		if (get_setting('fs_balancer_master_url'))
			redirect('/admin/members');

		$this->load->model('files_model');
		$this->load->library('pagination');
		$this->viewdata['controller_title'] = _("Series");
	}


	function index()
	{
		
	}

}
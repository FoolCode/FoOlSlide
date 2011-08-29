<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Team_Controller extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		// if this is a load balancer FoOlSlide, disable the team interface
		if (get_setting('fs_balancer_master_url'))
		{
			show_404();
		}
		
		// if the user isn't in any team, he shouldn't be here
		if (!($teams = $this->tank_auth->is_team()))
		{
			flash_notice('notice', 'You can\'t access the team panel because you aren\'t in any team');
			redirect('account');
		}
	}


	public function _remap($method, $params = array())
	{
		
	}


}
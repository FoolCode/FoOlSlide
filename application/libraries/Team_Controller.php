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
		if (!($this->teamc->teams = $this->tank_auth->is_team()))
		{
			flash_notice('notice', 'You can\'t access the team panel because you aren\'t in any team');
			redirect('account');
		}
	}


	public function _remap($method, $params = array())
	{
		// index means priorities page. you can be here only when uri is /team/
		if(($uri_team = $this->uri->segment(2)) === FALSE && $method == "index")
		{
			return call_user_func_array(array($this, $method), $params);
		}
		
		// find if any team is set in $method
		foreach($this->teamc->teams as $key => $team)
		{
			if($uri_team == $team->stub)
			{
				echo 'success '.$team->stub;
			}
			else
			{
				echo 'fail'.$uri_team;
			}
		}
		
	}


}
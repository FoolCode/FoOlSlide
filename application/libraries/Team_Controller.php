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
		
		// send to account system if not logged in
		if(!$this->tank_auth->is_logged_in())
		{
			redirect('account');
		}
		
		// if the user isn't in any team, he shouldn't be here
		if (!($this->teamc->teams = $this->tank_auth->is_team()))
		{
			flash_notice('notice', 'You can\'t access the team panel because you aren\'t in any team');
			redirect('account');
		}
	}


	/**
	 * We need to make a panel relative to the team stub, unless it's the index
	 * that gives a priorities panel across all teams
	 *
	 * @autor Woxxy
	 * @param string $method
	 * @param array $params
	 * @return bool 
	 */
	public function _remap($method, $params = array())
	{	
		// index means priorities page. you can be here only when uri is /team/
		if(($team_stub = $this->uri->segment(2)) === FALSE && $method == "index")
		{
			return call_user_func_array(array($this, $method), $params);
		}
		
		if($team_stub === FALSE)
		{
			show_404();
		}
		
		// if you're here it means there's a /team/group_name in the uri
		
		// check if the group_name in the uri is one of the teams
		foreach($this->teamc->teams as $key => $team)
		{
			if($team_stub == $team->stub)
			{
				$this->teamc->team = $team;
				return call_user_func_array(array($this, $method), $params);
			}
		}
		
	}


}
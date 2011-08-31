<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Priorities extends Team_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->viewdata['controller_title'] = _("Priorities");
          //$this->output->enable_profiler(TRUE);
	}


	/**
	 * Priorities index. Lists all the teams if there's no team set
	 */
	function index()
	{
		// if the team is not set, show all teams's priorities
		if(!isset($this->teamc->team) || !$this->teamc->team)
		{
			$data['teams'] = $this->tank_auth->is_team()->all;

			if($post = $this->input->post())
			{
				// check if the group_name is one of the teams
				foreach($data['teams'] as $team)
				{
					if($post['team'] == $team->stub)
						redirect('team/' . $team->stub);
				}
				redirect('team');
			}

			$this->viewdata["main_content_view"] = $this->load->view('team/teamlist.php', $data, TRUE);
		}
		else // else, show priorities of a single team
		{
			$this->viewdata["main_content_view"] = "Team selected: ".$this->teamc->team->name;
		}

		$this->load->view('team/default.php', $this->viewdata);
	}

}
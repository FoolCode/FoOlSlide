<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Priorities extends Team_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->viewdata['controller_title'] = _("Priorities");
	}


	/**
	 * Priorities index. Lists all the teams if there's no team set
	 */
	function index()
	{
		// if the team is not set, show all teams's priorities
		if(!isset($this->teamc->team) || !$this->teamc->team)
		{
			$this->output->set_output("General");
		}
		else // else, show priorities of a single team
		{
			$this->output->set_output("Team selected: ".$this->teamc->team->name);
		}
	}

}
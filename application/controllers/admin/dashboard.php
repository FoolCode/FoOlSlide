<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Dashboard extends Admin_Controller {

	function __construct() {
		parent::__construct();
		$this->tank_auth->is_logged_in() or redirect('/admin/auth/login');
		if($this->tank_auth->is_group('mod'))
		{
			echo 'success';
			die();
		}
				
		$this->viewdata["controller_title"] = _('Dashboard');
	}

	function index() {

		$applications = array();
		if($teams = $this->tank_auth->is_team_leader())
		{
			foreach($teams->all as $team)
			{
				$applicants = new Membership();
				$applicants->get_applications($team->id);
				foreach($applicants as $applicant)
				{
					$application[] = array('team' => $team->name, 'team_id'=> $team->id, 'username' => $applicant->username, 'user_id' => $applicant->id);
				}
			}
		}
		
		print_r($applications);
				
		//$this->viewdata["main_content_view"] = $this->load->view("admin/dashboard/index", NULL, TRUE);
		//$this->load->view("admin/default", $this->viewdata);
	}

}
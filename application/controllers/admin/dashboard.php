<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Dashboard extends Admin_Controller {

	function __construct() {
		parent::__construct();
		$this->tank_auth->is_logged_in() or redirect('/admin/auth/login');
		$this->viewdata["controller_title"] = _('Dashboard');
	}

	function index() {

		$this->viewdata["main_content_view"] = $this->load->view("admin/dashboard/index", NULL, TRUE);

		$application = array();
		if ($teams = $this->tank_auth->is_team_leader()) {
			foreach ($teams->all as $team) {
				$applicants = new Membership();
				$users = $applicants->get_applications($team->id);
				foreach ($applicants as $key => $applicant) {
					$application[] = array(
						$users->all[$key]->username,
						$team->name,
						$users->all[$key]->email,
						array(
							'display' => 'buttoner',
							'href' => site_url('/admin/members/accept_application/' . $team->id . '/' . $users->all[$key]->id),
							'text' => _('Accept')
						),
						array(
							'display' => 'buttoner',
							'href' => site_url('/admin/members/reject_application/' . $team->id . '/' . $users->all[$key]->id),
							'text' => _('Reject')
						)
					);
				}
			}

			$list = tabler($application, TRUE, FALSE);
			$data = array('application' => $list);
			$this->viewdata['main_content_view'] .= $this->load->view("admin/dashboard/applicants", $data, TRUE);
		}

		$this->load->view("admin/default", $this->viewdata);
	}

}
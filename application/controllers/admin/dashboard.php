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

		// print out the join request forms
		$this->_get_memberships();
		$this->_get_requests();
		$this->load->view("admin/default", $this->viewdata);
	}

	/*
	 * Creates the form to show the people applying to a team
	 * Gives the ability to team leaders to accept or reject applicants
	 * 
	 * @author Woxxy
	 */
	function _get_memberships() {
		$application = array();
		$members = new Membership();
		
		// if there are any applications
		if ($members->get_applications()) {
			foreach ($members->all as $key => $applicant) {
				$application[] = array(
					$applicant->user->username,
					$applicant->team->name,
					$applicant->user->email,
					array(
						'display' => 'buttoner',
						'href' => site_url('/admin/members/accept_application/' . $applicant->team->id . '/' . $applicant->user->id),
						'text' => _('Accept'),
						'plug' => _('Do you really want to accept this applicant?')
					),
					array(
						'display' => 'buttoner',
						'href' => site_url('/admin/members/reject_application/' . $applicant->team->id . '/' . $applicant->user->id),
						'text' => _('Reject'),
						'plug' => _('Do you really want to reject this applicant?')
					)
				);
			}

			// put the array in a form
			$list = tabler($application, TRUE, FALSE);
			
			// add it to the content without printing
			$data = array('application' => $list, 'section' => _('Pending applicants'));
			$this->viewdata['main_content_view'] .= $this->load->view("admin/dashboard/applicants", $data, TRUE);
		}
	}

	/*
	 * Shows a form giving the ability to accept requests from teams to join them
	 * 
	 * @author Woxxy
	 */
	function _get_requests() {
		$application = array();
		$members = new Membership();
		
		// are there any requests?
		if ($members->get_requests()) {
			foreach ($members->all as $key => $applicant) {
				$application[] = array(
					$applicant->team->name,
					$applicant->is_leader,
					array(
						'display' => 'buttoner',
						'href' => site_url('/admin/members/accept_application/' . $applicant->team->id),
						'text' => _('Accept'),
						'plug' => _('Do you really want to join this team?')
					),
					array(
						'display' => 'buttoner',
						'href' => site_url('/admin/members/reject_application/' . $applicant->team->id),
						'text' => _('Reject'),
						'plug' => _('Do you really want to reject the request to join this team?')
					)
				);
			}

			// put the array in a form
			$list = tabler($application, TRUE, FALSE);
			
			// add it to the content without printing
			$data = array('application' => $list, 'section' => _('Pending requests'));
			$this->viewdata['main_content_view'] .= $this->load->view("admin/dashboard/applicants", $data, TRUE);
		}
	}

}
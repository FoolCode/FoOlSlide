<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Members extends REST_Controller {

	/*
	 * Returns the teams, paged by 100
	 * 
	 * @param int page
	 */
	function teams_get() {
		if (!$this->get('page') || !is_numeric($this->get('page')) || $this->get('page') < 1)
			$page = 1;
		else
			$page = (int) $this->get('page');

		$page = ($page * 100) - 100;

		$teams = new Team();
		$teams->limit(100, $page)->get();

		if ($teams->result_count() > 0) {
			$result = array();
			foreach($teams->all as $team){
				$result[] = $team->to_array();
			}
			$this->response($result, 200); // 200 being the HTTP response code
		} else {
			$this->response(array('error' => _('Teams could not be found')), 404);
		}
	}
	
	/*
	 * Returns the team
	 * 
	 * @param int id
	 */
	function team_get() {
		if (!$this->get('id') || !is_numeric($this->get('id'))) {
			$this->response(NULL, 400);
		}

		$team = new Team();
		$team->where('id', $this->get('id'))->limit(1)->get();

		if ($team->result_count() == 1) {
			$result = $team->to_array();
			$members = new Membership();
			$memb = $members->get_members($team->id);
			foreach($memb->all as $key => $mem) {
				$result['members'][$key] = $mem->to_array(array('id','username'));
				$result['members'][$key]['display_name'] = $mem->profile_display_name;
				$result['members'][$key]['twitter'] = $mem->profile_twitter;
				$result['members'][$key]['bio'] = $mem->profile_bio;
			}
			$this->response($result, 200); // 200 being the HTTP response code
		} else {
			$this->response(array('error' => _('Team could not be found')), 404);
		}
	}
	
	/*
	 * Returns the teams related to the joint
	 * 
	 * @param int id
	 */
	function joint_get() {
		if (!$this->get('id') || !is_numeric($this->get('id'))) {
			$this->response(NULL, 400);
		}

		$team = new Team();
		$teams = $team->get_teams(0, $this->get('id'));

		if (count($teams) > 0) {
			$result = array();
			foreach($teams as $item) {
				$result[] = $item->to_array();
			}
			$this->response($result, 200); // 200 being the HTTP response code
		} else {
			$this->response(array('error' => _('Team could not be found')), 404);
		}
	}

}
<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Membership extends DataMapper {

	var $has_one = array();
	var $has_many = array();
	var $validation = array(
		'team_id' => array(
			'rules' => array('is_int'),
			'label' => 'Team ID',
		),
		'user_id' => array(
			'rules' => array('is_int'),
			'label' => 'User ID'
		),
		'is_leader' => array(
			'rules' => array(),
			'label' => 'Is leader',
		),
		'accepted' => array(
			'rules' => array(),
			'label' => 'Accepted'
		),
		'requested' => array(
			'rules' => array(),
			'label' => 'Requested'
		),
		'applied' => array(
			'rules' => array(),
			'label' => 'Applied'
		)
	);

	function __construct($id = NULL) {
		parent::__construct($id);
	}

	function post_model_init($from_cache = FALSE) {
		
	}

	function check($team_id, $user_id) {
		$member = new Membership();
		$member->where('team_id', $team_id)->where('user_id', $user_id)->get();
		return ($member->result_count() == 1);
	}

	function apply($team_id, $user_id) {
		if ($this->check($team_id, $user_id))
			return false;
		$this->team_id = $team_id;
		$this->user_id = $user_id;
		$this->applied = 1;
		$this->save();
	}

	function request($team_id, $user_id) {
		if ($this->check($team_id, $user_id))
			return false;
		$this->team_id = $team_id;
		$this->user_id = $user_id;
		$this->request = 1;
		$this->save();
	}

	function accept($team_id, $user_id) {
		if (!$this->check($team_id, $user_id))
			return false;
		$this->where('team_id', $team_id)->where('user_id', $user_id)->get();
		$this->user_id = $user_id;
		$this->accepted = 1;
		$this->save();
	}

	/**
	 * 	Returns User that is applying for the team
	 * 
	 *  @author Woxxy
	 *  @param int $team_id 
	 * 	@return object User
	 */
	function get_applications($team_id) {
		$this->where('team_id', $team_id)->where('accepted', 0)->where('applied', 1)->get();
		$users = new User();
		foreach ($this->all as $applicant) {
			$users->or_where('id', $applicant->user_id);
		}
		$users->get();
		return $users;
	}

	/**
	 * Accepts applications, can be triggered by team leader only.
	 * 
	 * @param int $team_id
	 * @param int $user_id 
	 */
	function accept_application($team_id, $user_id) {
		$CI = & get_instance();
		if ($CI->tank_auth->is_team_leader($team_id)) {
			$this->where('team_id', $team_id)->where('user_id', $user_id)->get();
			$this->accepted = 1;
			$this->save();
			return true;
		}
		return false;
	}

	/**
	 * Rejects applications, can be triggered by team leader only.
	 * 
	 * @param int $team_id
	 * @param int $user_id 
	 */
	function reject_application($team_id, $user_id) {
		$CI = & get_instance();
		if ($CI->tank_auth->is_team_leader($team_id)) {
			$this->where('team_id', $team_id)->where('user_id', $user_id)->get();
			$this->delete();
			return true;
		}
		return false;
	}

	/**
	 * Returns an array of Users. Bonus point: it also returns $user->is_admin
	 * 
	 * @param int $team_id 
	 * @return object Users with ->is_admin
	 */
	function get_members($team_id) {
		$this->where('team_id', $team_id)->where('accepted', 1)->get();
		$members = new User();
		if($this->result_count() == 0) return $members;
		foreach ($this->all as $member) {
			$members->or_where('id', $member->user_id);
		}
		$members->get();

		foreach ($members->all as $key => $member) {
			$member->is_leader = ($this->all[$key]->is_leader == 1)?'1':'0';
		}
		return $members;
	}

}
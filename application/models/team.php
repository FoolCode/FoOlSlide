<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Team extends DataMapper {

	var $has_one = array();
	var $has_many = array('chapter');
	var $validation = array(
		'name' => array(
			'rules' => array('required', 'unique', 'max_length' => 256),
			'label' => 'Name',
			'type' => 'input'
		),
		'stub' => array(
			'rules' => array('required', 'stub', 'unique', 'max_length' => 256),
			'label' => 'Stub'
		),
		'url' => array(
			'rules' => array('max_length' => 256),
			'label' => 'URL',
			'type' => 'input'
		),
		'forum' => array(
			'rules' => array('max_length' => 256),
			'label' => 'Forum',
			'type' => 'input'
		),
		'irc' => array(
			'rules' => array('max_length' => 256),
			'label' => 'IRC',
			'type' => 'input'
		),
		'twitter' => array(
			'rules' => array(),
			'label' => 'Twitter username',
			'type' => 'input'
		),
		'facebook' => array(
			'rules' => array(),
			'label' => 'Facebook',
			'type' => 'input'
		),
		'facebookid' => array(
			'rules' => array('max_length' => 512),
			'label' => 'Facebook page ID',
			'type' => 'input'
		),
		'lastseen' => array(
			'rules' => array(),
			'label' => 'Lastseen'
		),
		'creator' => array(
			'rules' => array('required'),
			'label' => 'Creator'
		),
		'editor' => array(
			'rules' => array('required'),
			'label' => 'Editor'
		)
	);

	function __construct($id = NULL) {
		parent::__construct($id);
	}

	function post_model_init($from_cache = FALSE) {
		
	}

	public function add_team($name, $url = "", $forum = "", $irc = "", $twitter = "", $facebook = "", $facebookid = "") {
		$this->name = $name;
		$this->stub = $name;
		$this->url = $url;
		$this->forum = $forum;
		$this->irc = $irc;
		$this->twitter = $twitter;
		$this->facebook = $facebook;
		$this->facebookid = $facebookid;

		if (!$this->update_team()) {
			log_message('error', 'add_team: failed adding team');
			return false;
		}

		return true;
	}

	public function update_team($data = array()) {

		// Check if we're updating or creating a new entry by looking at $data["id"].
		// False is pushed if the ID was not found.
		if (isset($data["id"])) {
			$this->where("id", $data["id"])->get();
			if ($this->result_count() == 0) {
				set_notice('error', 'Failed to find the selected team\'s ID.');
				log_message('error', 'update_team_db: failed to find requested id');
				return false;
			}
		}
		else { // let's set the creator name if it's a new entry
			$this->creator = $this->logged_id();
		}

		// always set the editor name
		$this->editor = $this->logged_id();



		//
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}


		if (!isset($this->stub))
			$this->stub = $this->stub();

		// let's save and give some error check. Push false if fail, true if good.
		if (!$this->save()) {
			if (!$this->valid) {
				set_notice('error', 'One or more fields contained the wrong value types.');
				log_message('error', 'update_team: failed validation');
			}
			else {
				set_notice('error', 'Failed to update the team in the database for unknown reasons.');
				log_message('error', 'update_team: failed to save');
			}
			return false;
		}
		else {
			return true;
		}
	}

	public function remove_team($also_chapters = FALSE) {
		if ($this->result_count() != 1) {
			set_notice('error', 'Failed to remove the chapter directory. Please, check file permissions.');
			log_message('error', 'remove_team: id not found');
			return false;
		}

		if ($also_chapters) {
			$chapters = new Chapter();
			$chapters->where("team_id", $this->id)->get();
			foreach ($chapters->all as $chapter) {
				if (!$chapter->remove_chapter()) {
					set_notice('error', 'Failed removing the chapters while removing the team.');
					log_message('error', 'remove_team: failed removing chapter');
					return false;
				}
			}
		}

		$joint = new Joint();
		if (!$joint->remove_team_from_all($this->id)) {
			log_message('error', 'remove_team: failed removing traces of team in joints');
			return false;
		}

		if (!$this->delete()) {
			set_notice('error', 'Failed to delete the team for unknown reasons.');
			log_message('error', 'remove_team: failed removing team');
			return false;
		}

		return true;
	}

	// this works by inputting an array of names (not stubs)
	public function get_teams_id($array, $create_joint = FALSE) {
		if (count($array) < 1) {
			set_notice('error', 'There were no groups selected.');
			log_message('error', 'get_groups: input array empty');
			return false;
		}

		if (count($array) == 1) {
			$team = new Team();
			$team->where("name", $array[0])->get();
			if ($team->result_count() < 1) {
				set_notice('error', 'There\'s no team under this ID.');
				log_message('error', 'get_groups: team not found');
				return false;
			}
			$result = array("team_id" => $team->id, "joint_id" => 0);
			return $result;
		}

		if (count($array) > 1) {
			$id_array = array();
			foreach ($array as $key => $arra) {
				$team = new Team();
				$team->where('name', $arra[$key])->get();
				if ($team->result_count() < 1) {
					set_notice('error', 'There\'s no teams under this ID.');
					log_message('error', 'get_groups: team not found');
					return false;
				}
				$id_array[$key] = $team->id;
			}
			$joint = new Joint();
			if (!$joint->check_joint($id_array) && $create_joint) {
				if (!$joint->add_joint($id_array)) {
					log_message('error', 'get_groups: could not create new joint');
					return false;
				}
			}
			return array("team_id" => 0, "joint_id" => $joint->joint_id);
		}

		set_notice('error', 'There\'s no group found with this ID.');
		log_message('error', 'get_groups: no case matched');
		return false;
	}

	//////// UNFINISHED!

	public function get_teams_name($team_id, $joint_id = 0) {
		if ($joint_id > 0) {
			$joint = new Joint();
			$joint->where("joint_id", $joint_id)->get();
			if ($joint->result_count() < 1) {
				log_message('error', 'get_teams_name: joint -> joint not found');
				return false;
			}

			$teamarray = array();
			$team = new Team();
			foreach ($joint->all as $key => $join) {
				$team->where('id', $join->team_id);
				$team->get();
				$teamarray[] = $team->get_clone();
			}

			if ($team->result_count() < 1) {
				log_message('error', 'get_teams_name: joint -> no teams found');
				return false;
			}

			return $teamarray;
		}

		$team = new Team();
		$team->where("id", $team_id)->get();
		if ($team->result_count() < 1) {
			log_message('error', 'get_teams_name: team -> team not found');
			return false;
		}
		return array($team);
	}

}

/* End of file team.php */
/* Location: ./application/models/team.php */

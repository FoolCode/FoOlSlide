<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reader extends REST_Controller {
	/*
	 * Returns 100 comics from selected page
	 * 
	 * @param int page
	 */

	function comics_get() {
		if (!$this->get('page') || !is_numeric($this->get('page')) || $this->get('page') < 1)
			$page = 1;
		else
			$page = (int) $this->get('page');

		$page = ($page * 100) - 100;

		$comic = new Comic();
		$comic->limit(100, $page)->get();

		if ($comic->result_count() > 0) {
			$result = array();
			$result['comics'] = $comic->all_to_array();
			$this->response($result, 200); // 200 being the HTTP response code
		} else {
			$this->response(array('error' => _('Comics could not be found')), 404);
		}
	}

	/*
	 * Returns the comic
	 * 
	 * @param int id
	 */

	function comic_get() {
		if (!$this->get('id') || !is_numeric($this->get('id'))) {
			$this->response(NULL, 400);
		}

		$comic = new Comic();
		$comic->where('id', $this->get('id'))->limit(1)->get();

		if ($comic->result_count() == 1) {
			$chapters = new Chapter();
			$chapters->where('comic_id', $comic->id)->get();
			$chapters->get_teams();
			$result = array();
			$result["comic"] = $comic->to_array();
			$result["chapters"] = array();
			foreach ($chapters->all as $key => $chapter) {
				$result['chapters'][$key] = $chapter->to_array();
				foreach ($chapter->teams as $team) {
					$result['chapters'][$key]['teams'][] = $team->to_array();
				}
			}

			$this->response($result, 200); // 200 being the HTTP response code
		} else {
			$this->response(array('error' => _('Comic could not be found')), 404);
		}
	}

	/*
	 * Returns the chapter
	 * 
	 * @param int id
	 */

	function chapter_get() {
		if (!$this->get('id') || !is_numeric($this->get('id'))) {
			$this->response(NULL, 400);
		}

		$chapter = new Chapter();
		$chapter->where('id', $this->get('id'))->limit(1)->get();

		if ($chapter->result_count() == 1) {
			$chapter->get_comic();
			$chapter->get_teams();

			$result = array();
			$result['comic'] = $chapter->comic->to_array();
			$result['chapter'] = $chapter->to_array();
			$result['teams'] = array();
			foreach ($chapter->teams as $team) {
				$result['teams'][] = $team->to_array();
			}
			$result['pages'] = $chapter->get_pages();


			$this->response($result, 200); // 200 being the HTTP response code
		} else {
			$this->response(array('error' => _('Chapter could not be found')), 404);
		}
	}

	/*
	 * Returns 100 chapters per page from team ID
	 * Includes releases from joints too
	 * 
	 * This is not a method light enough to lookup teams. use api/members/team for that
	 * 
	 * @param int id team ID
	 * @param int page 
	 */

	function team_get() {
		if (!$this->get('id') || !is_numeric($this->get('id'))) {
			$this->response(NULL, 400);
		}

		if (!$this->get('page') || !is_numeric($this->get('page')) || $this->get('page') < 1)
			$page = 1;
		else
			$page = (int) $this->get('page');

		$page = ($page * 100) - 100;

		$team = new Team();
		$team->where('id', $this->get('id'))->limit(1)->get();

		if ($team->result_count() == 1) {
			$result = array();
			$result['team'] = $team->to_array();

			// get joints to get also the chapters from joints
			$joints = new Joint();
			$joints->where('team_id', $team->id)->get();


			$chapters = new Chapter();
			$chapters->where('team_id', $team->id);
			foreach ($joints->all as $joint) {
				$chapters->or_where('joint_id', $joint->joint_id);
			}
			$chapters->limit(100, $page)->get();
			$chapters->get_comic();

			$result['chapters'] = array();
			foreach ($chapters->all as $key => $chapter) {
				if (!$chapter->team_id) {
					$chapter->get_teams();
					foreach ($chapter->teams as $item) {
						$result['chapters'][$key]['teams'][] = $item->to_array();
					}
				} else {
						$result['chapters'][$key]['teams'][] = $team->to_array();
				}
				$result['chapters'][$key]['comic'] = $chapter->comic->to_array();
				$result['chapters'][$key]['chapter'] = $chapter->to_array();
			}

			$this->response($result, 200); // 200 being the HTTP response code
		} else {
			$this->response(array('error' => _('Team could not be found')), 404);
		}
	}

	/*
	 * Returns 100 chapters per page by joint ID
	 * Also returns the teams
	 * 
	 * This is not a method light enough to lookup teams. use api/members/team for that
	 * 
	 * @param int id team ID
	 * @param int page 
	 */

	function joint_get() {
		if (!$this->get('id') || !is_numeric($this->get('id'))) {
			$this->response(NULL, 400);
		}

		if (!$this->get('page') || !is_numeric($this->get('page')) || $this->get('page') < 1)
			$page = 1;
		else
			$page = (int) $this->get('page');

		$page = ($page * 100) - 100;

		$joint = new Joint();
		$joint->where('joint_id', $this->get('id'))->limit(1)->get();

		if ($joint->result_count() == 1) {

			$team = new Team();
			$teams = $team->get_teams(0, $this->get('id'));

			$result = array();
			foreach ($teams as $item) {
				$result['teams'][] = $item->to_array();
			}

			$chapters = new Chapter();
			$chapters->where('joint_id', $joint->joint_id);
			$chapters->limit(100, $page)->get();
			$chapters->get_comic();

			$result['chapters'] = array();
			foreach ($chapters->all as $key => $chapter) {
				$result['chapters'][$key]['comic'] = $chapter->comic->to_array();
				$result['chapters'][$key]['chapter'] = $chapter->to_array();
				$result['chapters'][$key]['teams'] = $result['teams'];
			}

			$this->response($result, 200); // 200 being the HTTP response code
		} else {
			$this->response(array('error' => _('Team could not be found')), 404);
		}
	}

}
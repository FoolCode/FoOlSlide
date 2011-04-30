<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Reader extends Public_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library('pagination');
		$this->load->library('template');
		$this->template->set_layout('reader');
	}

	public function index() {
		$this->template->title('Comic list');
		$this->template->build('list');
	}

	public function lista($page = 1) {
		$this->template->title('Comic list');
		$this->template->build('list');
	}

	public function read($comic, $language = 'en', $chapter = "", $subchapter = 0, $team = 0, $joint = 0, $pagetext = 'page', $page = 1) {
		$comice = new Comic();
		$comice->where('stub', $comic)->get();
		if ($comice->result_count() == 0) {
			set_notice('warn', 'This chapter doesn\'t exist.');
		}

		$chaptere = new Chapter();
		$chaptere->where('comic_id', $comice->id)->where('language', $language)->where('chapter', $chapter);

		if ($subchapter == 'page')
			$current_page = $team;
		else {
			$chaptere->where('subchapter', $subchapter);

			if ($team == 'page')
				$current_page = $joint;
			else {
				if ($team != 0) {
					$teame = new Team();
					$teame->where('stub', $team)->get();
					$chaptere->where('team_id', $teame->id);
				}

				if ($joint == 'page')
					$current_page = $pagetext;

				if ($joint != 0) {
					$chaptere->where('joint_id', $joint);
				}
			}
		}

		if (!isset($current_page)) {
			if ($page != 1)
				$current_page = $page;
			else
				$current_page = 1;
		}

		$chaptere->get();
		if ($chaptere->result_count() == 0) {
			set_notice('warn', 'This chapter doesn\'t exist.');
		}


		$pages = $chaptere->get_pages();
		$next_chapter = $chaptere->next();

		if ($current_page > count($pages))
			redirect($next_chapter);
		if ($current_page < 1)
			$current_page = 1;




		$this->template->set('chapter', $chaptere);
		$this->template->set('current_page', $current_page);
		$this->template->set('pages', $pages);
		$this->template->set('next_chapter', $next_chapter);
		$this->template->title($comice->name . ' :: Chapter ' . $chaptere->chapter);
		$this->template->build('read');
	}

	public function json($comic, $language = 'en', $chapter = "", $subchapter = 0, $team = 0, $joint = 0, $pagetext = 'page', $page = 1) {
		$comice = new Comic();
		$comice->where('stub', $comic)->get();
		if ($comice->result_count() == 0) {
			set_notice('warn', 'This chapter doesn\'t exist.');
		}

		$chaptere = new Chapter();
		$chaptere->where('comic_id', $comice->id)->where('language', $language)->where('chapter', $chapter);

		if ($subchapter == 'page')
			$current_page = $team;
		else {
			$chaptere->where('subchapter', $subchapter);

			if ($team == 'page')
				$current_page = $joint;
			else {
				if ($team != 0) {
					$teame = new Team();
					$teame->where('stub', $team)->get();
					$chaptere->where('team_id', $teame->id);
				}

				if ($joint == 'page')
					$current_page = $pagetext;

				if ($joint != 0) {
					$chaptere->where('joint_id', $joint);
				}
			}
		}

		if (!isset($current_page)) {
			if ($page != 1)
				$current_page = $page;
			else
				$current_page = 1;
		}


		$chaptere->get();
		if ($chaptere->result_count() == 0) {
			set_notice('warn', 'This chapter doesn\'t exist.');
		}

		$pages = $chaptere->get_pages();
		$next_chapter = $chaptere->next('json');

		if ($current_page > count($pages))
			redirect($next_chapter);
		if ($current_page < 1)
			$current_page = 1;

		$team = new Team();
		$teams = $team->get_teams($chaptere->team_id, $chaptere->joint_id);

		$teames = array();
		foreach ($teams as $key => $tea) {
			$teames[$key] = $tea->to_array();
		}

		$result['comic'] = $comice->to_array();
		$result['chapter'] = $chaptere->to_array();
		$result['next_chapter'] = $next_chapter;
		$result['teams'] = $teames;
		$result['pages'] = $pages;

		header('Content-type: application/json');
		echo json_encode($result);
	}

	/*
	  public function latest();
	  public function rss();

	  public function list_chapters($comic)
	  {

	  }

	  public function reader($comic, $chapter, $subchapter, $group, $version, $id)
	  {

	  }
	 * 
	 */
}
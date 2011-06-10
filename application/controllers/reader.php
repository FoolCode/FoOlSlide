<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Reader extends Public_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library('pagination');
		$this->load->library('template');
		$this->load->helper('reader');
		$this->template->set_layout('reader');
	}

	public function index() {
		$this->latest();
	}

	public function team($stub = NULL) {
		if (is_null($stub))
			show_404();
		$team = new Team();
		$team->where('stub', $stub)->get();
		if ($team->result_count() < 1)
			show_404();

		$memberships = new Membership();
		$members = $memberships->get_members($team->id);

		$this->template->title(_('Team'));
		$this->template->set('team', $team);
		$this->template->set('members', $members);
		$this->template->build('team');
	}

	public function lista($page = 1) {
		$this->template->title(_('Series list'));

		$comics = new Comic();
		$comics->order_by('name', 'ASC')->get_paged($page, 25);
		foreach ($comics->all as $comic) {
			$comic->latest_chapter = new Chapter();
			$comic->latest_chapter->where('comic_id', $comic->id)->order_by('created', 'DESC')->limit(1)->get();
		}

		$this->template->title(_('Series'));
		$this->template->set('comics', $comics);
		$this->template->build('list');
	}

	public function latest($page = 1) {
		$this->template->title(_('Series list'));
		// Create a "Chapter" object. It can contain more than one chapter!
		$chapters = new Chapter();

		// Select the latest 25 released chapters
		$chapters->order_by('created', 'DESC')->limit(15);

		// Get the chapters!
		$chapters->get();
		$chapters->get_teams();
		$chapters->get_comic();

		$this->template->set('chapters', $chapters);
		$this->template->title(_('Latest'));
		$this->template->build('latest');
	}

	public function read($comic, $language = 'en', $volume = 0, $chapter = "", $subchapter = 0, $team = 0, $joint = 0, $pagetext = 'page', $page = 1) {
		$comice = new Comic();
		$comice->where('stub', $comic)->get();
		if ($comice->result_count() == 0) {
			set_notice('warn', 'This comic doesn\'t exist.');
		}

		if ($chapter == "") {
			redirect('/reader/comic/' . $comic);
		}

		$chaptere = new Chapter();
		$chaptere->where('comic_id', $comice->id)->where('language', $language)->where('volume', $volume)->where('chapter', $chapter)->order_by('subchapter', 'ASC');

		if (!is_int($subchapter) && $subchapter == 'page') {
			$current_page = $team;
		}
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
			show_404();
		}


		$pages = $chaptere->get_pages();
		foreach($pages as $page) unset($page["object"]);
		$next_chapter = $chaptere->next();

		if ($current_page > count($pages))
			redirect($next_chapter);
		if ($current_page < 1)
			$current_page = 1;

		$chapters = new Chapter();
		$chapters->where('comic_id', $comice->id)->order_by('volume', 'desc')->order_by('chapter', 'desc')->order_by('subchapter', 'desc')->get_bulk();
		
		$comics = new Comic();
		$comics->order_by('name', 'DESC')->limit(100)->get();
		
		$this->template->set('is_reader', TRUE);
		$this->template->set('comic', $comice);
		$this->template->set('chapter', $chaptere);
		$this->template->set('chapters', $chapters);
		$this->template->set('comics', $comics);
		$this->template->set('current_page', $current_page);
		$this->template->set('pages', $pages);
		$this->template->set('next_chapter', $next_chapter);
		$this->template->title($comice->name . ' :: ' . _('Chapter') . ' ' . $chaptere->chapter);
		$this->template->build('read');
	}
	
	public function download($comic, $language = 'en', $volume = 0, $chapter = "", $subchapter = 0, $team = 0, $joint = 0, $pagetext = 'page', $page = 1) {
		if(!get_setting('fs_dl_enabled'))
			show_404();
		$comice = new Comic();
		$comice->where('stub', $comic)->get();
		if ($comice->result_count() == 0) {
			set_notice('warn', 'This comic doesn\'t exist.');
		}

		if ($chapter == "") {
			redirect('/reader/comic/' . $comic);
		}

		$chaptere = new Chapter();
		$chaptere->where('comic_id', $comice->id)->where('language', $language)->where('volume', $volume)->where('chapter', $chapter)->order_by('subchapter', 'ASC');

		if (!is_int($subchapter) && $subchapter == 'page') {
			$current_page = $team;
		}
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
			show_404();
		}
		
		$archive = new Archive();
		$url = $archive->compress($chaptere);
		redirect($url);
	}

	public function comic($stub = NULL) {
		if (is_null($stub))
			show_404();
		$comic = new Comic();
		$comic->where('stub', $stub)->get();
		if ($comic->result_count() < 1)
			show_404();

		$chapters = new Chapter();
		$chapters->where('comic_id', $comic->id)->order_by('volume', 'desc')->order_by('chapter', 'desc')->order_by('subchapter', 'desc')->get_bulk();

		$this->template->set('comic', $comic);
		$this->template->set('chapters', $chapters);
		$this->template->title($comic->name);
		$this->template->build('comic');
	}

	public function search() {
		if (!$this->input->post('search')) {
			$this->template->title(_('Search'));
			$this->template->build('search_pre');
			return TRUE;
		}

		$search = HTMLpurify($this->input->post('search'), 'unallowed');
		$this->template->title(_('Search'));

		$comics = new Comic();
		$comics->ilike('name', $search)->limit(20)->get();
		foreach ($comics->all as $comic) {
			$comic->latest_chapter = new Chapter();
			$comic->latest_chapter->where('comic_id', $comic->id)->order_by('created', 'DESC')->limit(1)->get()->get_teams();
		}


		$this->template->set('search', $search);
		$this->template->set('comics', $comics);
		$this->template->build('search');
	}

}
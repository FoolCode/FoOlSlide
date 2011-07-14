<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Api extends Public_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library('pagination');
		$this->load->library('template');
		$this->load->helper('reader');
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
		$teamarray = $team->all_to_array();
		$teamarray["members"] = $members->all_to_array(array("id", "username"));
		echo json_encode($teamarray);
	}

	public function lista($page = 1) {
		$this->template->title(_('Series list'));

		$comics = new Comic();
		$comics->order_by('name', 'ASC')->get_paged($page, 25);
		foreach ($comics->all as $comic) {
			$comic->latest_chapter = new Chapter();
			$comic->latest_chapter->where('comic_id', $comic->id)->order_by('created', 'DESC')->limit(1)->get();
		}

		echo json_encode($comics->all_to_array());
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

	}

	public function chapter($comic, $language = 'en', $volume = 0, $chapter = "", $subchapter = 0, $team = 0, $joint = 0, $pagetext = 'page', $page = 1) {
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
		$comics->order_by('name', 'ASC')->limit(100)->get();
                
                
                $result = array(
                    'comic' => $comice->to_array(), 
                    'chapter' => $chaptere->to_array(),
                    'next_chapter' => $next_chapter,
                    'pages' => $pages,
                    'comics' => $chapters->to_array(), 
                    'chapters' => $chapters->to_array()
                );
                
                echo json_encode($result);
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

                echo json_encode( 
                    array(
                        'comic' => $comic->to_array(),
                        'chapters' => $chapters->to_array()
                    )
                );
	}

	public function search() {
		if (!$this->input->post('search')) {
			$this->template->title(_('Search'));
			$this->template->build('search_pre');
			return TRUE;
		}

		$search = HTMLpurify($this->input->post('search'), 'unallowed');
		$this->template->title(_('Search'));

                $comice = array();
		$comics = new Comic();
		$comics->ilike('name', $search)->limit(20)->get();
		foreach ($comics->all as $comic) {
			$latest_chapter = new Chapter();
			$latest_chapter->where('comic_id', $comic->id)->order_by('created', 'DESC')->limit(1)->get()->get_teams();                        
                        $comix = $comic->to_array();
                        $comice[] = 
                        
                }
                $comice = $comics->to_array();
                

		echo json_encode(array('comics' => $comic->to_array()));
	}

}
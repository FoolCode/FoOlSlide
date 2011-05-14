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
		redirect('/reader/latest/');
	}

	public function lista($page = 1) {
		$this->template->title('Comic list');
		
		$comics = new Comic();
		$comics->get_paged($page, 15);
		
		$this->template->set('comics', $comics);
		$this->template->build('list');
	}
	
	public function latest($page = 1) {
		$this->template->title('Comic list');
		// Create a "Chapter" object. It can contain more than one chapter!
		$chapters = new Chapter();
		
		// With each, get the comic they depends from
		$chapters->include_related('comic');
		
		// Lets group these 25 releases by comic, so it looks like less of a mess.
		$chapters->order_by_related('comic', 'name');
		
		// Select the latest 25 released chapters
		$chapters->order_by('created', 'DESC')->limit(25);
		
		// Get the chapters! Let's use get_iterated() instead of get() to save some RAM
		$chapters->get();
		
		$this->template->set('chapters', $chapters);
		$this->template->build('latest');
	}

	public function read($comic, $language = 'en', $volume = 0, $chapter = "", $subchapter = 0, $team = 0, $joint = 0, $pagetext = 'page', $page = 1) {
		$comice = new Comic();
		$comice->where('stub', $comic)->get();
		if ($comice->result_count() == 0) {
			set_notice('warn', 'This comic doesn\'t exist.');
		}
		
		if ($chapter == "")
		{
			redirect('/reader/comic/'. $comic);
		}

		$chaptere = new Chapter();
		$chaptere->where('comic_id', $comice->id)->where('language', $language)->where('volume', $volume)->where('chapter', $chapter);

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
			show_404();
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
	
	
	public function comic($stub)
	{
		$comic = new Comic();
		$comic->where('stub', $stub)->get();
		
		$chapters = new Chapter();
		$chapters->where('comic_id', $comic->id)->order_by('volume', 'desc')->order_by('chapter', 'desc')->order_by('subchapter', 'desc')->get_bulk();
		
		$this->template->set('comic', $comic);
		$this->template->set('chapters', $chapters);
		$this->template->title($comic->name);
		$this->template->build('comic');
	}
	
}
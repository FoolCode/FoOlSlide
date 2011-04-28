<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Chapter extends DataMapper {

	var $has_one = array('comic', 'team', 'joint');
	var $has_many = array('page');
	var $validation = array(
		'name' => array(
			'rules' => array('max_length' => 256),
			'label' => 'Name',
			'type' => 'input'
		),
		'comic_id' => array(
			'rules' => array('is_int', 'required', 'max_length' => 256),
			'label' => 'Comic ID',
			'type' => 'hidden'
		),
		'team_id' => array(
			'rules' => array('is_int', 'max_length' => 256),
			'label' => 'Team ID'
		),
		'joint_id' => array(
			'rules' => array('is_int', 'max_length' => 256),
			'label' => 'Joint ID'
		),
		'stub' => array(
			'rules' => array('stub', 'required', 'max_length' => 256),
			'label' => 'Stub'
		),
		'chapter' => array(
			'rules' => array('is_int', 'required'),
			'label' => 'Chapter number',
			'type' => 'input',
			'placeholder' => 'required'
		),
		'subchapter' => array(
			'rules' => array('is_int'),
			'label' => 'Subchapter number',
			'type' => 'input'
		),
		'volume' => array(
			'rules' => array('is_int'),
			'label' => 'Volume number',
			'type' => 'input'
		),
		'language' => array(
			'rules' => array(),
			'label' => 'Language',
			'type' => 'language'
		),
		'uniqid' => array(
			'rules' => array('required', 'max_length' => 256),
			'label' => 'Uniqid'
		),
		'hidden' => array(
			'rules' => array('is_int'),
			'label' => 'Hidden',
			'type' => 'checkbox'
		),
		'description' => array(
			'rules' => array(),
			'label' => 'Description',
			'type' => 'textarea'
		),
		'thumbnail' => array(
			'rules' => array('max_length' => 512),
			'label' => 'Thumbnail'
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

	public function get($limit = NULL, $offset = NULL) {
		$CI = & get_instance();
		if (!$CI->ion_auth->is_admin())
			$this->where('hidden', 0);

		return parent::get($limit, $offset);
	}

	public function add_chapter($data) {
		$this->to_stub = $data['chapter'] . "_" . $data['subchapter'] . "_" . $data['name'];
		$this->uniqid = uniqid();
		$this->stub = $this->stub();

		$comic = new Comic;
		$comic->where("id", $data['comic_id'])->get();
		if ($comic->result_count() == 0) {
			set_notice('error', 'The comic ID you were adding the chapter to does not exist.');
			log_message('error', 'add_chapter: comic_id does not exist in comic database');
			return false;
		}
		$this->comic_id = $data['comic_id'];

		if (!$this->add_chapter_dir($comic->stub, $comic->uniqid)) {
			log_message('error', 'add_chapter: failed creating dir');
			return false;
		}
		if (!$this->update_chapter_db($data)) {
			$this->remove_chapter_dir($comic->stub, $comic->uniqid);
			return false;
		}

		return $comic;
	}

	public function remove_chapter() {
		$comic = new Comic();
		$comic->where("id", $this->comic_id)->get();
		if (!$this->remove_chapter_dir($comic->stub, $comic->uniqid)) {
			log_message('error', 'remove_chapter: failed to delete dir');
			return false;
		}

		if (!$this->remove_chapter_db()) {
			log_message('error', 'remove_chapter: failed to delete database entry');
			return false;
		}

		return $comic;
	}

	public function update_chapter_db($data = array()) {
		// Check if we're updating or creating a new entry by looking at $data["id"].
		// False is pushed if the ID was not found.
		if (isset($data["id"]) && $data['id'] != "") {
			$this->where("id", $data["id"])->get();
			if ($this->result_count() == 0) {
				set_notice('error', 'The chapter you were referring to does not exist.');
				log_message('error', 'update_chapter_db: failed to find requested id');
				return false;
			}
			$old_stub = $this->stub;
		}
		else { // let's set the creator name if it's a new entry	// let's also check that the related comic is defined, and exists
			if (!isset($this->comic_id)) {
				set_notice('error', 'You didn\'t select a comic to refer to.');
				log_message('error', 'update_chapter_db: comic_id was not set');
				return false;
			}

			$comic = new Comic();
			$comic->where("id", $this->comic_id)->get();
			if ($comic->result_count() == 0) {
				set_notice('error', 'The comic you were referring to does not exist.');
				log_message('error', 'update_chapter_db: comic_id does not exist in comic database');
				return false;
			}

			$this->creator = $this->logged_id();
		}

		// always set the editor name
		$this->editor = $this->logged_id();

		unset($data["creator"]);
		unset($data["editor"]);
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}

		if (!isset($this->uniqid))
			$this->uniqid = uniqid();
		if (!isset($this->stub))
			$this->stub = $this->stub();
		if (!isset($data['hidden']) || $data['hidden'] != 1)
			$this->hidden = 0;

		$this->stub = $this->chapter . '_' . $this->subchapter . '_' . $this->name;
		$this->stub = $this->stub();

		if (isset($old_stub) && $old_stub != $this->stub) {
			$comic = new Comic();
			$comic->where('id', $this->comic_id)->get();
			$dir_old = "content/comics/" . $comic->stub . "_" . $comic->uniqid . "/" . $old_stub . "_" . $this->uniqid;
			$dir_new = "content/comics/" . $comic->stub . "_" . $comic->uniqid . "/" . $this->stub . "_" . $this->uniqid;
			rename($dir_old, $dir_new);
		}


		if (is_array($data['team'])) {
			foreach ($data['team'] as $key => $value) {
				if ($value == "") {
					unset($data['team'][$key]);
				}
			}
		}

		if (count($data['team']) > 1) {
			$this->team_id = 0;
			$joint = new Joint();
			$this->joint_id = $joint->add_joint_via_name($data['team']);
		}
		else if (count($data['team']) == 1) {
			$this->joint_id = 0;
			$team = new Team();
			$team->where("name", $data['team'][0])->get();
			if ($team->result_count() == 0) {
				set_notice('error', 'The team you were referring this chapter to doesn\'t exist.');
				log_message('error', 'update_chapter_db: team_id does not exist in team database');
				return false;
			}
			$this->team_id = $team->id;
		}
		else {
			set_notice('error', 'You haven\'t selected any team related to this chapter.');
			log_message('error', 'update_chapter_db: team_id does not defined');
			return false;
		}


		// let's save and give some error check. Push false if fail, true if good.
		$success = $this->save();
		if (!$success) {
			if (!$this->valid) {
				log_message('error', $this->error->string);
				set_notice('error', 'One or more of the fields inputted had the wrong kind of values.');
				log_message('error', 'update_chapter_db: failed validation');
			}
			else {
				set_notice('error', 'Failed to save to database for unknown reasons.');
				log_message('error', 'update_chapter_db: failed to save');
			}
			return false;
		}
		else {
			return true;
		}
	}

	public function remove_chapter_db() {
		$pages = new Page();
		$pages->where('chapter_id', $this->id)->get_iterated();
		foreach ($pages as $page) {
			$page->remove_page_db();
		}

		$success = $this->delete();
		if (!$success) {
			set_notice('error', 'Failed to remove the chapter from the database for unknown reasons.');
			log_message('error', 'remove_chapter_db: id found but entry not removed');
			return false;
		}

		return true;
	}

	public function add_chapter_dir($comicstub, $uniqid) {
		$dir = "content/comics/" . $comicstub . "_" . $uniqid . "/" . $this->stub . "_" . $this->uniqid;
		if (!mkdir($dir)) {
			set_notice('error', 'Failed to create the chapter directory. Please, check file permissions.');
			log_message('error', 'add_chapter_dir: folder could not be created');
			return false;
		}

		return true;
	}

	public function remove_chapter_dir($comicstub, $uniqid) {
		$dir = "content/comics/" . $comicstub . "_" . $uniqid . "/" . $this->stub . "_" . $this->uniqid . "/";
		if (!delete_files($dir, TRUE)) {
			set_notice('error', 'Failed to remove the files inside the chapter directory. Please, check file permissions.');
			log_message('error', 'remove_chapter_dir: files inside folder could not be removed');
			return false;
		}
		else {
			if (!rmdir($dir)) {
				set_notice('error', 'Failed to remove the chapter directory. Please, check file permissions.');
				log_message('error', 'remove_chapter_dir: folder could not be removed');
				return false;
			}
		}

		return true;
	}

	public function remove_all_pages() {
		log_message('error', 'here');
		$page = new Page();
		$page->where('chapter_id', $this->id)->get_iterated();
		foreach ($page as $key => $item) {
			if (!$item->remove_page()) {
				log_message('error', 'remove_all_pages: page could not be removed');
			}
		}
		return true;
	}

	public function get_pages() {
		$comic = new Comic();
		$comic->where('id', $this->comic_id)->get();
		$pages = new Page();
		$pages->where('chapter_id', $this->id)->get();

		$return = array();

		foreach ($pages->all as $key => $item) {
			//$return[$key]['object'] = $item;
			$return[$key]['id'] = $item->id;
			$return[$key]['width'] = $item->width;
			$return[$key]['height'] = $item->height;
			$return[$key]['url'] = base_url() . "content/comics/" . $comic->stub . "_" . $comic->uniqid . "/" . $this->stub . "_" . $this->uniqid . "/" . $item->filename;
			$return[$key]['thumb_url'] = base_url() . "content/comics/" . $comic->stub . "_" . $comic->uniqid . "/" . $this->stub . "_" . $this->uniqid . "/" . $item->thumbnail . $item->filename;
		}
		$chapter->pages = $pages;
		return $return;
	}

	public function url() {
		return '<a href="' . $this->href() . '" title="' . $this->title() . '">' . $this->title() . '</a>';
	}

	public function title() {
		$echo = _('Chapter') . ' ' . $this->chapter;
		if ($this->subchapter)
			$echo .= '.' . $this->subchapter;
		if ($this->name != "")
			$echo .= ': ' . $this->name;

		return $echo;
	}

	public function href() {
		$comic = new Comic();
		$comic->where('id', $this->comic_id)->get();
		$chapter = new Chapter();


		$chaptere = new Chapter();
		$chaptere->where('comic_id', $comic->id)->where('chapter', $this->chapter)->where('language', $this->language)->where('subchapter', $this->subchapter)->get();

		$done = false;
		if ($chaptere->result_count() > 0) {
			foreach ($chaptere->all as $chap) {
				if ($chap->team_id == $this->team_id && $chap->joint_id == $this->joint_id) {
					$chapter = $chap;
					$done = true;
					break;
				}
			}

			if (!$done) {
				// This is a pretty random way to select the next chapter version, needs refinement
				$chapter = $chaptere->all['0'];
			}
		}
		else {
			$chapter = $chaptere;
		}

		$url = '/reader/read/' . $comic->stub . '/' . $chapter->language . '/' . $chapter->chapter . '/';

		if ($chapter->subchapter != 0) {
			$url .= $chapter->subchapter . '/';
			$subchapter = true;
		}

		if (isset($done) && $done == false) {
			if (!isset($subchapter) && !$subchapter) {
				$url .= $chapter->subchapter . '/';
			}

			if ($chapter->team_id != 0) {
				$team = new Team();
				$team->where('id', $team_id)->get();
				$url .= $team->stub . '/';
			}

			if ($chapter->joint_id != 0)
				$url .= '0/' . $chapter->joint_id . '/';
		}

		return site_url($url);
	}

	// This is meant to give the next chapter URL to the javascript
	public function next($type = "read") {
		$comic = new Comic();
		$comic->where('id', $this->comic_id)->get();
		$chapter = new Chapter();

		$chapter->where('comic_id', $comic->id)->where('chapter', $this->chapter)->where('language', $this->language)->having('subchapter >', $this->subchapter)->order_by('subchapter', 'asc')->limit(1)->get();
		if ($chapter->result_count() == 0) {
			$chapter = new Chapter();
			$chapter->where('comic_id', $comic->id)->having('chapter > ', $this->chapter)->where('language', $this->language)->order_by('chapter', 'asc')->limit(1)->get();
			if ($chapter->result_count() == 0) {
				if (!$short)
					return site_url('/' . $comic->stub);
				return site_url('/reader/' . $type . '/' . $comic->stub);
			}
		}

		$chaptere = new Chapter();
		$chaptere->where('comic_id', $comic->id)->where('chapter', $chapter->chapter)->where('language', $this->language)->where('subchapter', $chapter->subchapter)->get();

		$done = false;
		if ($chaptere->result_count() > 0) {
			foreach ($chaptere->all as $chap) {
				if ($chap->team_id == $this->team_id && $chap->joint_id == $this->joint_id) {
					$chapter = $chap;
					$done = true;
					break;
				}
			}

			if (!$done) {
				// This is a pretty random way to select the next chapter version, needs refinement
				$chapter = $chaptere->all['0'];
			}
		}
		else {
			$chapter = $chaptere;
		}


		$url = '/reader/' . $type . '/' . $comic->stub . '/' . $chapter->language . '/' . $chapter->chapter . '/';

		if ($chapter->subchapter != 0) {
			$url .= $chapter->subchapter . '/';
			$subchapter = true;
		}

		if (isset($done) && $done == false) {
			if (!isset($subchapter) && !$subchapter) {
				$url .= $chapter->subchapter . '/';
			}

			if ($chapter->team_id != 0) {
				$team = new Team();
				$team->where('id', $team_id)->get();
				$url .= $team->stub . '/';
			}

			if ($chapter->joint_id != 0)
				$url .= '0/' . $chapter->joint_id . '/';
		}

		return site_url($url);
	}

	public function next_page($page) {
		$url = current_url();
		if (!$post = strpos($url, '/page')) {
			return current_url() . '/page/' . ($page + 1);
		}
		return substr(current_url(), 0, $post) . '/page/' . ($page + 1);
	}

}

/* End of file chapter.php */
/* Location: ./application/models/chapter.php */

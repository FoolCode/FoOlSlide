<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Files_model extends CI_Model {

	function __construct() {
		// Call the Model constructor
		parent::__construct();
	}

	// This is just a plug to adapt the variable names for the comic_model
	public function page($data) {
		// $data["chapter_id"];
		// $data["raw_name"];
		$file["server_path"] = $data["full_path"];
		$file["name"] = $data["file_name"];
		$file["size"] = $data["file_size"];
		$file['overwrite'] = ($data["overwrite"] == 1);

		$page = new Page();
		if (!$page->add_page($file, $data["chapter_id"], 0, "")) {
			log_message('error', 'page: function add_page failed');
			return false;
		}
		return array($page);
	}

	// This is just a plug to adapt the variable names for the comic_model
	public function comic_thumb($comic, $data) {
		$file["server_path"] = $data["full_path"];
		$file["name"] = $data["file_name"];

		if (!$comic->add_comic_thumb($file)) {
			log_message('error', 'Model: files_model.php/comic_thumb: function add_comic_thumb failed');
			return false;
		}
		return true;
	}

	public function compressed_chapter($data) {
		$chapter = new Chapter();
		$chapter->where("id", $data["chapter_id"])->get();
		$uniqid = uniqid();
		$overwrite = ($data["overwrite"] == 1);
		if(is_dir($data["full_path"])) {
			$this->folder_chapter($data["full_path"], $chapter, $overwrite);
			return TRUE;
		}
		$cachedir = 'content/cache/' . $data["raw_name"] . "_" . $uniqid;
		if (!mkdir($cachedir)) {
			log_message('error', 'compressed_chapter: failed creating dir');
			return FALSE;
		}
		
		if(function_exists('rar_open') && strtolower($data["file_ext"]) == '.rar')
			$this->uncompress_rar($data["full_path"], $cachedir);
		
		if (strtolower($data["file_ext"]) == '.zip')
			$this->uncompress_zip($data["full_path"], $cachedir);

		$pages_added = $this->folder_chapter($cachedir, $chapter, $overwrite);

		// Let's delete all the cache
		if (!delete_files($cachedir, TRUE)) {
			log_message('error', 'compressed_chapter: files inside cache dir could not be removed');
			return FALSE;
		}
		else {
			if (!rmdir($cachedir)) {
				log_message('error', 'compressed_chapter: cache dir could not be removed');
				return FALSE;
			}
		}
		
		return $pages_added;
	}

	public function uncompress_rar($path, $cachedir) {
		$rar_file = rar_open($path);
		$entries = rar_list($rar_file);
		$allowed = array('.jpg', '.gif', '.png', 'jpeg');
		foreach ($entries as $entry) {
			if (in_array(substr($entry->getName(), -4), $allowed))
				$entry->extract($cachedir);
		}
		rar_close($rar_file);
	}
	
	public function uncompress_zip($path, $cachedir) {
		$this->load->library('unzip');
		$this->unzip->allow(array('png', 'gif', 'jpeg', 'jpg'));
		$this->unzip->extract($path, $cachedir);
	}

	public function folder_chapter($cachedir, $chapter, $overwrite) {
		// Get the filename
		$dirarray = get_dir_file_info($cachedir, FALSE);

		$this->db->reconnect();
		$pages_added = array();
		foreach ($dirarray as $key => $value) {
			$extentsion = "";
			$extension = pathinfo($value["server_path"], PATHINFO_EXTENSION);
			if ($extension && !in_array(strtolower($extension), array('jpeg', 'jpg', 'png', 'gif')))
				continue;
			$value['overwrite'] = $overwrite;
			$page = new Page();
			$error = false;
			if (!$page->add_page($value, $chapter->id, 0, "")) {
				log_message('error', 'compressed_chapter: one page in the loop failed being added');
				$error = true;
			}
			if ($error)
				set_notice('error', 'Some pages weren\'t uploaded');
			
			$pages_added[] = $page;
		}
		return $pages_added;
	}

	public function import_list($data) {

		function array_minus_array($a, $b) {
			$c = Array();
			foreach ($a as $key => $val) {
				$posb = array_search($val, $b);
				if (is_integer($posb)) {
					unset($b[$posb]);
				}
				else {
					$c[] = $val;
				}
			}
			return $c;
		}

		$dirinfo = get_dir_file_info($data['directory']);
		ksort($dirinfo);

		$archives = array();

		$matches = array();
		preg_match_all("/([\d! ]+)/", $data['comic']->name, $matches, PREG_PATTERN_ORDER);
		$title_nums = $matches[0];

		$count = 0;
		foreach ($dirinfo as $key => $file) {
			if(!is_dir($file['server_path']))
			{
				$extension = strtolower(pathinfo($file["server_path"], PATHINFO_EXTENSION));
				if($extension != 'rar' && $extension != 'zip')
					continue;
			}
			
			if(!is_dir($file['server_path']))
			{
				$not_dir = TRUE;
			}
			
			$archives[$count]['filename'] = $file['name'];
			$archives[$count]['server_path'] = $file['server_path'];
			$archives[$count]['relative_path'] = $file['relative_path'];
			$archives[$count]['comic_id'] = $data['comic']->id;

			$matches = array();
			preg_match_all("/\[([\w! ]+)\]/", $file['name'], $matches, PREG_PATTERN_ORDER);
			$archives[$count]['teams'] = $matches[0];

			$patches = array();
			preg_match_all("/([\d! ]+)/", $file['name'], $matches, PREG_PATTERN_ORDER);
			$archives[$count]['numbers'] = array_minus_array($matches[0], $title_nums);

			$count++;
		}
		unset($count);

		return $archives;
	}

	public function import_compressed() {

		$chapter = new Chapter();
		if (!$chapter->add($this->input->post())) {
			log_message('error', 'import_compressed(): Couldn\'t create chapter');
			return array('error' => "Couldn't create the chapter.");
		}
		$data['chapter_id'] = $chapter->id;
		$data['overwrite'] = 1;
		$data['full_path'] = $this->input->post('server_path');
		$data['raw_name'] = 'import_' . $chapter->id;
		if(!is_dir($data["full_path"]))
		$data['file_ext'] = '.'.end(explode('.', $this->input->post('server_path')));
		if (!$this->compressed_chapter($data)) {
			$chapter->remove();
			log_message('error', 'import_compressed(): Couldn\'t add the pages to the chapter');
			return array('error' => "Couldn't add the pages to the chapter.");
		}
		return array('success' => TRUE);
	}

}
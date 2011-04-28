<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Page DataMapper Model
 *
 * Use this basic model as a page for creating new models.
 * It is not recommended that you include this file with your application,
 * especially if you use a Page library (as the classes may collide).
 *
 * To use:
 * 1) Copy this file to the lowercase name of your new model.
 * 2) Find-and-replace (case-sensitive) 'Page' with 'Your_model'
 * 3) Find-and-replace (case-sensitive) 'page' with 'your_model'
 * 4) Find-and-replace (case-sensitive) 'pages' with 'your_models'
 * 5) Edit the file as desired.
 *
 * @license		MIT License
 * @category	Models
 * @author		Phil DeJarnett
 * @link		http://www.overzealous.com
 */
class Page extends DataMapper {

	var $has_one = array('chapter');
	var $has_many = array();
	var $validation = array(
		'chapter_id' => array(
			'rules' => array('required', 'max_length' => 256),
			'label' => 'Chapter ID'
		),
		'filename' => array(
			'rules' => array('required', 'max_length' => 256),
			'label' => 'Filename'
		),
		'hidden' => array(
			'rules' => array(),
			'label' => 'Hidden'
		),
		'description' => array(
			'rules' => array(),
			'label' => 'Description'
		),
		'thumbnail' => array(
			'rules' => array('required', 'max_length' => 512),
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
		),
		'width' => array(
			'rules' => array('required'),
			'label' => 'Width'
		),
		'height' => array(
			'rules' => array('required'),
			'label' => 'Height'
		),
		'mime' => array(
			'rules' => array('required'),
			'label' => 'Mime type'
		),
		'thumbwidth' => array(
			'rules' => array('required'),
			'label' => 'Thumbnail width'
		),
		'thumbheight' => array(
			'rules' => array('required'),
			'label' => 'Thumbnail height'
		),
		'size' => array(
			'rules' => array('required'),
			'label' => 'Size'
		),
		'thumbsize' => array(
			'rules' => array('required'),
			'label' => 'Thumbnail size'
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

	public function add_page($filedata, $chapter_id, $hidden = 0, $description = "") {
		if (!$imagedata = @getimagesize($filedata["server_path"])) {
			log_message('error', 'add_page: uploaded file doesn\'t seem to be an image');
			return false;
		}

		$this->chapter_id = $chapter_id;
		if ($hidden == 1)
			$this->hidden = 1; else
			$this->hidden = 0;
		$this->description = $description;
		$overwrite = $filedata["overwrite"];

		$chapter = new Chapter();
		$chapter->where("id", $chapter_id)->get();
		if ($chapter->result_count() == 0) {
			log_message('error', 'add_page: chapter_id does not exist in chapter database');
			return false;
		}

		$comic = new Comic();
		$comic->where("id", $chapter->comic_id)->get();
		if ($comic->result_count() == 0) {
			log_message('error', 'add_page: comic_id does not exist in comic database');
			return false;
		}


		if (!$this->add_page_file($comic->stub, $comic->uniqid, $chapter->stub, $chapter->uniqid, $filedata)) {
			log_message('error', 'add_page: failed creating file');
			return false;
		}

		$dir = "content/comics/" . $comic->stub . "_" . $comic->uniqid . "/" . $chapter->stub . "_" . $chapter->uniqid . "/";

		$imagedata = @getimagesize($filedata["server_path"]);
		$thumbdata = getimagesize($dir . "thumb_" . $filedata["name"]);

		$page = new Page();
		$page->where('chapter_id', $this->chapter_id)->where('filename', $filedata["name"])->get();
		if ($page->result_count() > 0) {
			$this->id = $page->id;
		}

		$this->filename = $filedata["name"];
		$this->thumbnail = "thumb_";
		$this->height = $imagedata["0"];
		$this->width = $imagedata["1"];
		$this->size = $filedata["size"];
		$this->mime = image_type_to_mime_type($imagedata["2"]);
		$this->thumbheight = $thumbdata["0"];
		$this->thumbwidth = $thumbdata["1"];
		$this->thumbsize = filesize($dir . "thumb_" . $filedata["name"]);

		if (!$this->update_page_db()) {
			log_message('error', 'add_page: failed writing to database');
			$this->remove_page_file($comic->stub, $comic->uniqid, $chapter->stub, $chapter->uniqid, $this->filename);
			return false;
		}

		return true;
	}

	public function remove_page() {
		if (!$this->remove_page_db()) {
			log_message('error', 'remove_page: failed to delete database entry');
			return false;
		}

		$chapter = new Chapter();
		$chapter->where("id", $this->chapter_id)->get();
		$comic = new Comic();
		$comic->where("id", $chapter->comic_id)->get();

		if (!$this->remove_page_file($comic->stub, $comic->uniqid, $chapter->stub, $chapter->uniqid)) {
			log_message('error', 'remove_page: failed to delete dir');
			return false;
		}

		return array("comic" => $comic, "chapter" => $chapter);
	}

	public function update_page_db($data = array()) {
		// Check if we're updating or creating a new entry by looking at $data["id"].
		// False is pushed if the ID was not found.
		if (isset($data["id"])) {
			$this->where("id", $data["id"])->get();
			if ($chapter->result_count() == 0) {
				set_notice('error', 'There isn\'t a page in the database related to this ID.');
				log_message('error', 'update_page_db: failed to find requested id');
				return false;
			}
		}
		else { // let's set the creator name if it's a new entry	// let's also check that the related comic is defined, and exists
			if (!isset($this->chapter_id)) {
				set_notice('error', 'There was no selected chapter.');
				log_message('error', 'update_page_db: chapter_id was not set');
				return false;
			}

			$chapter = new Chapter();
			$chapter->where("id", $this->chapter_id)->get();
			if ($chapter->result_count() == 0) {
				set_notice('error', 'The selected chapter doesn\'t exist.');
				log_message('error', 'update_page_db: chapter_id does not exist in comic database');
				return false;
			}

			$this->creator = $this->logged_id();
		}

		// always set the editor name
		$this->editor = $this->logged_id();

		//
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}

		// let's save and give some error check. Push false if fail, true if good.
		$success = $this->save();
		if (!$success) {
			if (!$this->valid) {
				set_notice('error', 'One or more fields had wrong types of value.');
				log_message('error', 'update_page_db: failed validation');
			}
			else {
				set_notice('error', 'Failed to write to database for unknown reasons.');
				log_message('error', 'update_page_db: failed to save');
			}
			return false;
		}
		else {
			return true;
		}
	}

	public function remove_page_db() {
		if (!$this->delete()) {
			set_notice('error', 'Failed to remove the page from the database.');
			log_message('error', 'remove_page_db: failed remove page entry');
			return false;
		}
		return true;
	}

	public function add_page_file($comicstub, $uniqid, $chapterstub, $uniqid2, $filedata) {
		$dir = "content/comics/" . $comicstub . "_" . $uniqid . "/" . $chapterstub . "_" . $uniqid2 . "/";
		if (!copy($filedata["server_path"], $dir . $filedata["name"])) {
			set_notice('error', 'Failed to add the page\'s file. Please, check file permissions.');
			log_message('error', 'add_page_file: failed to create/copy the image');
			return false;
		}

		$CI = & get_instance();
		$CI->load->library('image_lib');
		$img_config['image_library'] = 'GD2';
		$img_config['source_image'] = $filedata["server_path"];
		$img_config["new_image"] = $dir . "thumb_" . $filedata["name"];
		$img_config['maintain_ratio'] = TRUE;
		$img_config['width'] = 250;
		$img_config['height'] = 250;
		$img_config['maintain_ratio'] = TRUE;
		$img_config['master_dim'] = 'auto';
		$CI->image_lib->initialize($img_config);

		if (!$CI->image_lib->resize()) {
			set_notice('error', 'Failed to create the thumbnail of the page.');
			log_message('error', 'add_page_file: failed to create thumbnail');
			return false;
		}
		$CI->image_lib->clear();

		return true;
	}

	public function remove_page_file($comicname, $uniqid, $chaptername, $uniqid2) {
		$dir = "content/comics/" . $comicname . "_" . $uniqid . "/" . $chaptername . "_" . $uniqid2 . "/";
		if (!unlink($dir . $this->filename)) {
			set_notice('error', 'Failed to remove the page\'s file. Please, check file permissions.');
			log_message('error', 'remove_page_file: failed to delete image');
			return false;
		}

		if (!unlink($dir . "thumb_" . $this->filename)) {
			set_notice('error', 'Failed to remove the page\'s thumbnail. Please, check file permissions.');
			log_message('error', 'remove_page_file: failed to delete thumbnail');
			return false;
		}

		return true;
	}

	public function page_url($thumbnail = FALSE) {
		$chapter = new Chapter();
		$chapter->where('id', $this->chapter_id)->get();
		$comic = new Comic();
		$comic->where('chapter_id', $chapter->comic_id)->get();
		return base_url() . "content/comics/" . $comic->stub . "_" . $comic->uniqid . "/" . $chapter->stub . "_" . $chapter->uniqid . "/" . ($thumbnail ? $page->thumbnail : "") . $page->filename;
	}

}

/* End of file page.php */
/* Location: ./application/models/page.php */

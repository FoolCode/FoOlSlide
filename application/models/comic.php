<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Comic extends DataMapper {

	var $has_one = array();
	var $has_many = array('chapter');
	var $validation = array(
		'name' => array(
			'rules' => array('required', 'unique', 'max_length' => 256),
			'label' => 'Name',
			'type' => 'input',
			'placeholder' => 'required',
			'help' => 'Insert the English version of the name. Try using the most popular one in order to lock it to the master FoOlSlide repository.'
		),
		'stub' => array(
			'rules' => array('required', 'stub', 'unique', 'max_length' => 256),
			'label' => 'Stub'
		),
		'uniqid' => array(
			'rules' => array('required', 'max_length' => 256),
			'label' => 'Uniqid'
		),
		'hidden' => array(
			'rules' => array('is_int'),
			'label' => 'Hidden',
			'type' => 'checkbox',
			'help' => 'Hide this entire comic from the public.'
		),
		'description' => array(
			'rules' => array(),
			'label' => 'Description',
			'type' => 'textarea',
			'help' => 'Write the basics of the plot of this comic.'
		),
		'thumbnail' => array(
			'rules' => array('max_length' => 512),
			'label' => 'Thumbnail',
			'type' => 'upload',
			'display' => 'image',
			'help' => 'Upload an image, it will be stored in full quality and also resized for better public view.'
		),
		'lastseen' => array(
			'rules' => array(),
			'label' => 'Lastseen'
		),
		'creator' => array(
			'rules' => array(''),
			'label' => 'Creator'
		),
		'editor' => array(
			'rules' => array(''),
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

	public function add_comic($data = array()) {
		$this->to_stub = $data['name'];
		$this->uniqid = uniqid();
		$this->stub = $this->stub();


		if (!$this->add_comic_dir()) {
			log_message('error', 'add_comic: failed creating dir');
			return false;
		}
		if (!$this->update_comic_db($data)) {
			log_message('error', 'add_comic: failed writing to database');
			$this->remove_comic_dir();
			return false;
		}

		return true;
	}

	public function remove_comic() {
		if (!$this->remove_comic_dir()) {
			log_message('error', 'remove_comic: failed to delete dir');
			return false;
		}

		if (!$this->remove_comic_db()) {
			log_message('error', 'remove_comic: failed to delete database entry');
			return false;
		}

		return true;
	}

	public function update_comic_db($data = array()) {

		// Check if we're updating or creating a new entry by looking at $data["id"].
		// False is pushed if the ID was not found.
		if (isset($data["id"]) && $data['id'] != '') {
			$this->where("id", $data["id"])->get();
			if ($this->result_count() == 0) {
				set_notice('error', 'The ID of the comic you wanted to edit doesn\'t exist.');
				log_message('error', 'update_comic_db: failed to find requested id');
				return false;
			}
			$old_stub = $this->stub;
		}
		else { // let's set the creator name if it's a new entry
			$this->creator = $this->logged_id();
		}

		// always set the editor name
		$this->editor = $this->logged_id();

		unset($data["creator"]);
		unset($data["editor"]);
		//
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}

		if (!isset($this->uniqid))
			$this->uniqid = uniqid();
		if (!isset($this->stub))
			$this->stub = $this->stub();

		$this->stub = $this->name;
		$this->stub = $this->stub();
		if (!isset($data['hidden']) || $data['hidden'] != 1)
			$this->hidden = 0;

		if (isset($old_stub) && $old_stub != $this->stub) {
			$dir_old = "content/comics/" . $old_stub . "_" . $this->uniqid;
			$dir_new = "content/comics/" . $this->stub . "_" . $this->uniqid;
			rename($dir_old, $dir_new);
		}

		// let's save and give some error check. Push false if fail, true if good.
		$success = $this->save();
		if (!$success) {
			if (!$this->valid) {
				set_notice('error', 'One or more of the fields you inputted didn\'t respect the values required.');
				log_message('error', 'update_comic_db: failed validation');
			}
			else {
				set_notice('error', 'Failed saving the Comic to database for unknown reasons.');
				log_message('error', 'update_comic_db: failed to save');
			}
			return false;
		}

		return $this;
	}

	public function remove_comic_db() {
		if ($this->result_count() != 1) {
			set_notice('error', 'You tried removing a comic that doesn\'t exist');
			log_message('error', 'remove_comic_db: id not found, entry not removed');
			return false;
		}

		$chapters = new Chapter();
		$chapters->where("comic_id", $this->id)->get_iterated();
		foreach ($chapters as $chapter) {
			$chapter->remove_chapter_db();
		}

		$temp = $this->get_clone();
		$success = $this->delete();
		if (!$success) {
			set_notice('error', 'The comic couldn\'t be removed from the database for unknown reasons.');
			log_message('error', 'remove_comic_db: id found but entry not removed');
			return false;
		}

		return $temp;
	}

	public function add_comic_dir() {
		if (!mkdir("content/comics/" . $this->stub . "_" . $this->uniqid)) {
			set_notice('error', 'The directory could not be created. Please, check file permissions.');
			log_message('error', 'add_comic_dir: folder could not be created');
			return false;
		}
		return true;
	}

	public function remove_comic_dir() {
		$dir = "content/comics/" . $this->stub . "_" . $this->uniqid . "/";
		if (!delete_files($dir, TRUE)) {
			set_notice('error', 'The files inside the comic directory could not be removed. Please, check the file permissions.');
			log_message('error', 'remove_comic_dir: files inside folder could not be removed');
			return false;
		}
		else {
			if (!rmdir($dir)) {
				set_notice('error', 'The directory could not be removed. Please, check file permissions.');
				log_message('error', 'remove_comic_dir: folder could not be removed');
				return false;
			}
		}

		return true;
	}

	public function add_comic_thumb($filedata) {
		if ($this->thumbnail != "")
			$this->remove_comic_thumb();

		$dir = "content/comics/" . $this->stub . "_" . $this->uniqid . "/";
		if (!copy($filedata["server_path"], $dir . $filedata["name"])) {
			set_notice('error', 'Failed to create the thumbnail image for the comic. Check file permissions.');
			log_message('error', 'add_comic_thumb: failed to create/copy the image');
			return false;
		}
		$CI = & get_instance();
		$CI->load->library('image_lib');

		$image = "thumb_" . $filedata["name"];

		$img_config['image_library'] = 'GD2';
		$img_config['source_image'] = $filedata["server_path"];
		$img_config["new_image"] = $dir . $image;
		$img_config['maintain_ratio'] = TRUE;
		$img_config['width'] = 250;
		$img_config['height'] = 250;
		$img_config['maintain_ratio'] = TRUE;
		$img_config['master_dim'] = 'auto';
		$CI->image_lib->initialize($img_config);

		if (!$CI->image_lib->resize()) {
			set_notice('error', 'Failed to create the thumbnail image for the comic. Resize function didn\'t work');
			log_message('error', 'add_comic_thumb: failed to create thumbnail');
			return false;
		}
		$CI->image_lib->clear();

		$this->thumbnail = $filedata["name"];
		$this->save();

		return $filedata["name"];
	}

	public function remove_comic_thumb() {
		$dir = "content/comics/" . $this->stub . "_" . $this->uniqid . "/";
		if (!unlink($dir . $this->thumbnail)) {
			set_notice('error', 'Failed to remove the thumbnail\'s original image. Please, check file permissions.');
			log_message('error', 'Model: comic_model.php/remove_comic_thumb: failed to delete image');
			return false;
		}

		if (!unlink($dir . "thumb_" . $this->thumbnail)) {
			set_notice('error', 'Failed to remove the thumbnail image. Please, check file permissions.');
			log_message('error', 'Model: comic_model.php/remove_comic_thumb: failed to delete thumbnail');
			return false;
		}

		$this->thumbnail = "";
		if (!$this->save()) {
			set_notice('error', 'Failed to remove the thumbnail image from the database.');
			log_message('error', 'Model: comic_model.php/remove_comic_thumb: failed to remove from database');
			return false;
		}

		return true;
	}

	public function get_thumb($full = FALSE) {
		if ($this->thumbnail != "")
			return base_url() . "content/comics/" . $this->stub . "_" . $this->uniqid . "/" . ($full ? "" : "thumb_") . $this->thumbnail;
		return false;
	}

}

/* End of file comic.php */
/* Location: ./application/models/comic.php */

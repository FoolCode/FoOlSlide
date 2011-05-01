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

	/**
	 * Overwrite of the get() function to add filters to the search.
	 * Refer to DataMapper ORM for get() function details.
	 *
	 * @author	Woxxy
	 * @param	integer|NULL $limit Limit the number of results.
	 * @param	integer|NULL $offset Offset the results when limiting.
	 * @return	DataMapper Returns self for method chaining.
	 */
	public function get($limit = NULL, $offset = NULL) {
		// Get the CodeIgniter instance, since it isn't set in this file.
		$CI = & get_instance();

		// Check if the user is allowed to see protected chapters.
		if (!$CI->ion_extra->is_allowed())
			$this->where('hidden', 0);

		return parent::get($limit, $offset);
	}

	/**
	 * Overwrite of the get_iterated() function to add filters to the search.
	 * Refer to DataMapper ORM for get_iterated() function details.
	 *
	 * @author	Woxxy
	 * @param	integer|NULL $limit Limit the number of results.
	 * @param	integer|NULL $offset Offset the results when limiting.
	 * @return	DataMapper Returns self for method chaining.
	 */
	public function get_iterated($limit = NULL, $offset = NULL) {
		// Get the CodeIgniter instance, since it isn't set in this file.
		$CI = & get_instance();

		// Check if the user is allowed to see protected chapters.
		if (!$CI->ion_extra->is_allowed())
			$this->where('hidden', 0);

		/**
		 * @todo figure out why those variables don't get unset... it would be
		 * way better to use the iterated in almost all cases in FoOlSlide
		 */
		return parent::get_iterated($limit, $offset);
	}

	/**
	 * Comodity get() function that fetches extra data for the comic selected.
	 * It doesn't get the chapters.
	 * 
	 * CURRENTLY USELESS.
	 *
	 * @author	Woxxy
	 * @param	integer|NULL $limit Limit the number of results.
	 * @param	integer|NULL $offset Offset the results when limiting.
	 * @return	DataMapper Returns self for method chaining.
	 */
	public function get_bulk($limit = NULL, $offset = NULL) {
		// Call the get()
		$result = $this->get($limit, $offset);
		// Return instantly on false.
		if (!$result)
			return $result;

		// For each item we fetched, add the data, beside the pages
		foreach ($this->all as $item) {
			
		}

		return $result;
	}

	/**
	 * Function to create a new entry for a comic from scratch. It creates
	 * both a directory and a database entry, and removes them if something
	 * goes wrong.
	 *
	 * @author	Woxxy
	 * @param	array $data with the minimal values, or the function will return
	 * 			false and do nothing.
	 * @return	Returns true on success, false on failure.
	 */
	public function add($data = array()) {
		// For the comic, the stub is just the name.
		$this->to_stub = $data['name'];
		// Uniqid to prevent directory clash
		$this->uniqid = uniqid();
		// stub() checks for to_stub and makes a stub.
		$this->stub = $this->stub();

		// Check if dir is created. GUI errors in inner function.
		if (!$this->add_comic_dir()) {
			log_message('error', 'add_comic: failed creating dir');
			return false;
		}

		// Check if the comic database entry and remove dir in case it's not.
		// GUI errors are inner to the function
		if (!$this->update_comic_db($data)) {
			log_message('error', 'add_comic: failed writing to database');
			$this->remove_comic_dir();
			return false;
		}

		// Good job!
		return true;
	}

	/**
	 * Removes comic from database, all its pages, chapters, and its directory.
	 * There's no going back from this!
	 *
	 * @author	Woxxy
	 * @return	boolean true on success, false on failure
	 */
	public function remove() {

		// Remove the directory through function
		if (!$this->remove_comic_dir()) {
			log_message('error', 'remove_comic: failed to delete dir');
			return false;
		}

		// Remove database entry through function
		if (!$this->remove_comic_db()) {
			log_message('error', 'remove_comic: failed to delete database entry');
			return false;
		}

		return true;
	}

	/**
	 * Handles both creating of new comics in the database and editing old ones.
	 * It determines if it should update or not by checking if $this->id has
	 * been set. It can get the values from both the $data array and direct 
	 * variable assignation. Be aware that array > variables. The latter ones
	 * will be overwritten. Particularly, the variables that the user isn't
	 * allowed to set personally are unset and reset with the automated values.
	 * It's quite safe to throw stuff at it.
	 *
	 * @author	Woxxy
	 * @param	array $data contains the minimal data
	 * @return	boolean true on success, false on failure
	 */
	public function update_comic_db($data = array()) {

		// Check if we're updating or creating a new comic by looking at $data["id"].
		// False is returned if the chapter ID was not found.
		if (isset($data["id"]) && $data['id'] != '') {
			$this->where("id", $data["id"])->get();
			if ($this->result_count() == 0) {
				set_notice('error', 'The comic you wanted to edit doesn\'t exist.');
				log_message('error', 'update_comic_db: failed to find requested id');
				return false;
			}
			// Save the stub in a variable in case it gets changed, so we can change folder name
			$old_stub = $this->stub;
		}
		else {
			// let's set the creator name if it's a new entry
			$this->creator = $this->logged_id();
		}

		// always set the editor name
		$this->editor = $this->logged_id();

		// Unset sensible variables
		// Not even admins should touch these, for database stability.
		unset($data["creator"]);
		unset($data["editor"]);
		unset($data["uniqid"]);
		unset($data["stub"]);

		// Loop over the array and assign values to the variables.
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}

		// Double check that we have all the necessary automated variables
		if (!isset($this->uniqid))
			$this->uniqid = uniqid();
		if (!isset($this->stub))
			$this->stub = $this->stub();

		// Prepare a new stub.
		$this->stub = $this->name;
		// stub() is also able to restub the $this->stub. Already stubbed values won't change.
		$this->stub = $this->stub();
		
		/**
		 * @todo stubs with a number to safely allow multiple comics with same name
		 */
		
		/*
		$find_stub = false;
		$i = 0;
		while(!$find_stub)
		{
			$comic = new Comic();
			$comic->where
		}
		*/


		// This is necessary to make the checkbox work.
		/**
		 *  @todo make the checkbox work consistently across the whole framework
		 */
		if (!isset($data['hidden']) || $data['hidden'] != 1)
			$this->hidden = 0;

		// rename the folder if the stub changed
		if (isset($old_stub) && $old_stub != $this->stub) {
			$dir_old = "content/comics/" . $old_stub . "_" . $this->uniqid;
			$dir_new = "content/comics/" . $this->stub . "_" . $this->uniqid;
			rename($dir_old, $dir_new);
		}

		// let's save and give some error check. Push false if fail, true if good.
		$success = $this->save();
		if (!$success) {
			if (!$this->valid) {
				set_notice('error', 'Check that you have inputted all the required fields.');
				log_message('error', 'update_comic_db: failed validation');
			}
			else {
				set_notice('error', 'Failed saving the Comic to database for unknown reasons.');
				log_message('error', 'update_comic_db: failed to save');
			}
			return false;
		}

		// Good job!
		return true;
	}

	/**
	 * Removes the comic from the database, but before it removes all the 
	 * related chapters and their pages from the database (not the files).
	 *
	 * @author	Woxxy
	 * @return	object a copy of the comic that has been deleted
	 */
	public function remove_comic_db() {
		// Get all its chapters
		$chapters = new Chapter();
		$chapters->where("comic_id", $this->id)->get_iterated();

		// Remove all the chapters from the database. This will also remove all the pages
		foreach ($chapters as $chapter) {
			$chapter->remove_chapter_db();
		}

		// We need a clone if we want to keep the variables after deletion
		$temp = $this->get_clone();
		$success = $this->delete();
		if (!$success) {
			set_notice('error', 'The comic couldn\'t be removed from the database for unknown reasons.');
			log_message('error', 'remove_comic_db: id found but entry not removed');
			return false;
		}

		// Return the comic clone
		return $temp;
	}

	/**
	 * Creates the necessary empty folder for the comic
	 * 
	 * @author	Woxxy
	 * @return	boolean true if success, false if failure.
	 */
	public function add_comic_dir() {
		// Just create the folder
		if (!mkdir("content/comics/" . $this->directory())) {
			set_notice('error', 'The directory could not be created. Please, check file permissions.');
			log_message('error', 'add_comic_dir: folder could not be created');
			return false;
		}
		return true;
	}

	/**
	 * Removes the comic directory with all the data that was inside of it.
	 * This means chapters, pages and props too.
	 *
	 * @author	Woxxy
	 * @return	boolean true if success, false if failure.
	 */
	public function remove_comic_dir() {
		$dir = "content/comics/" . $this->directory() . "/";
		
		// Delete all inner files
		if (!delete_files($dir, TRUE)) {
			set_notice('error', 'The files inside the comic directory could not be removed. Please, check the file permissions.');
			log_message('error', 'remove_comic_dir: files inside folder could not be removed');
			return false;
		}
		else {
			// On success delete the directory itself
			if (!rmdir($dir)) {
				set_notice('error', 'The directory could not be removed. Please, check file permissions.');
				log_message('error', 'remove_comic_dir: folder could not be removed');
				return false;
			}
		}

		return true;
	}

	
	/**
	 * Creates the thumbnail and saves the original as well
	 *
	 * @author	Woxxy
	 * @param	array|$filedata a standard array coming from CodeIgniter's upload
	 * @return	boolean true on success, false on failure
	 */
	public function add_comic_thumb($filedata) {
		// If there's already one, remove it.
		if ($this->thumbnail != "")
			$this->remove_comic_thumb();

		// Get directory variable
		$dir = "content/comics/" . $this->directory() . "/";
		
		// Copy the full image over
		if (!copy($filedata["server_path"], $dir . $filedata["name"])) {
			set_notice('error', 'Failed to create the thumbnail image for the comic. Check file permissions.');
			log_message('error', 'add_comic_thumb: failed to create/copy the image');
			return false;
		}
		
		// Load the image library
		$CI = & get_instance();
		$CI->load->library('image_lib');

		// Let's setup the thumbnail creation and pass it to the image library
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

		// Resize! And return false of failure
		if (!$CI->image_lib->resize()) {
			set_notice('error', 'Failed to create the thumbnail image for the comic. Resize function didn\'t work');
			log_message('error', 'add_comic_thumb: failed to create thumbnail');
			return false;
		}
		
		// Whatever we might want to do later, we better clear the library now!
		$CI->image_lib->clear();

		// The thumbnail is actually the filename of the original for comic thumbnails
		// It's different from page thumbnails - those have "thumb_" in this variable!
		$this->thumbnail = $filedata["name"];
		
		// Save hoping we're lucky
		if (!$this->save()) {
			set_notice('error', 'Failed to save the thumbnail image in the database.');
			log_message('error', 'add_comic_thumb: failed to add to database');
			return false;
		}

		// Alright!
		return true;
	}

	/**
	 * Removes the thumbnail and its original image both from database and directory.
	 *
	 * @author	Woxxy
	 * @return	string true on success, false on failure.
	 */
	public function remove_comic_thumb() {

		// Get directory
		$dir = "content/comics/" . $this->directory() . "/";

		// Remove the full image
		if (!unlink($dir . $this->thumbnail)) {
			set_notice('error', 'Failed to remove the thumbnail\'s original image. Please, check file permissions.');
			log_message('error', 'Model: comic_model.php/remove_comic_thumb: failed to delete image');
			return false;
		}

		// Remove the thumbnail
		if (!unlink($dir . "thumb_" . $this->thumbnail)) {
			set_notice('error', 'Failed to remove the thumbnail image. Please, check file permissions.');
			log_message('error', 'Model: comic_model.php/remove_comic_thumb: failed to delete thumbnail');
			return false;
		}

		// Set the thumbnail variable to empty and save to database
		$this->thumbnail = "";
		if (!$this->save()) {
			set_notice('error', 'Failed to remove the thumbnail image from the database.');
			log_message('error', 'Model: comic_model.php/remove_comic_thumb: failed to remove from database');
			return false;
		}

		// All's good.
		return true;
	}

	/**
	 * Returns href to thumbnail. Uses load-balancer system.
	 *
	 * @author	Woxxy
	 * @param boolean|$full if set to true, the function returns the full image
	 * @return	string href to thumbnail.
	 */
	public function get_thumb($full = FALSE) {
		if ($this->thumbnail != "")
			return balance_url() . "content/comics/" . $this->stub . "_" . $this->uniqid . "/" . ($full ? "" : "thumb_") . $this->thumbnail;
		return false;
	}

	/**
	 * Returns directory name without slashes
	 *
	 * @author	Woxxy
	 * @return	string Directory name.
	 */
	public function directory() {
		return $this->stub . '_' . $this->uniqid;
	}

}

/* End of file comic.php */
/* Location: ./application/models/comic.php */
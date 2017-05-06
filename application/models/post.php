<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Post extends DataMapper
{

	static $cached = array();
	var $has_one = array('user');
	var $has_many = array();
	var $validation = array(
		'name' => array(
			'rules' => array('required', 'max_length' => 256),
			'label' => 'Name',
			'type' => 'input',
			'placeholder' => 'required',
		),
		'stub' => array(
			'rules' => array('stub', 'unique', 'max_length' => 256),
			'label' => 'URL Slug',
			'type' => 'input',
			'class' => 'uneditable-input jqslug'
		),
		'description' => array(
			'rules' => array(),
			'label' => 'Description',
			'type' => 'textarea',
		),
		'hidden' => array(
			'rules' => array('is_int'),
			'label' => 'Visibility',
			'type' => 'checkbox',
			'values' => array('0' => 'Visible', '1' => 'Hidden')
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

	function __construct($id = NULL)
	{
		// Set the translations
		$this->help_lang();

		parent::__construct(NULL);

		// We've overwrote some functions, and we need to use the get() from THIS model
		if (!empty($id) && is_numeric($id))
		{
			$this->where('id', $id)->get();
		}
	}


	function post_model_init($from_cache = FALSE)
	{

	}


	/**
	 * This function sets the translations for the validation values.
	 *
	 * @author Woxxy
	 * @return void
	 */
	function help_lang()
	{
		$this->validation['name']['label'] = _('Name');
		$this->validation['name']['help'] = _('Insert the title of the series.');
		$this->validation['description']['label'] = _('Description');
		$this->validation['description']['help'] = _('Insert a description.');
		$this->validation['hidden']['label'] = _('Visibility');
		$this->validation['hidden']['help'] = _('Hide the series from public view.');
		$this->validation['hidden']['text'] = _('Hidden');
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
	public function get($limit = NULL, $offset = NULL)
	{
		// Get the CodeIgniter instance, since it isn't set in this file.
		$CI = & get_instance();

		// Check if the user is allowed to see protected chapters.
		if (!$CI->tank_auth->is_allowed())
		{
			$this->where('hidden', 0);
		}

		$result = parent::get($limit, $offset);

		return $result;
	}


	/**
	 * Returns the series that have been already called before
	 *
	 * @author Woxxy
	 * @param int $id team_id
	 */
	public function get_cached($id)
	{
		foreach (self::$cached as $cache)
		{
			if ($cache->id == $id)
			{
				return $cache;
			}
		}
		return FALSE;
	}

	/**
	 * Gets the users (creator and editor)
	 *
	 * @author	DvaJi
	 * @return	bool true on success
	 */
	public function get_users()
	{
		$user = new User();
		$this->creator = $user->where("id", $this->creator)->get()->username;
		$this->editor = $user->where("id", $this->editor)->get()->username;

		foreach ($this->all as $item)
		{
			$user = new User();
			$item->creator = $user->where("id", $item->creator)->get()->username;
			$item->editor = $user->where("id", $item->editor)->get()->username;
			
		}

		// All good, return true.
		return true;
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
	public function get_iterated($limit = NULL, $offset = NULL)
	{
		// Get the CodeIgniter instance, since it isn't set in this file.
		$CI = & get_instance();

		// Check if the user is allowed to see protected chapters.
		if (!$CI->tank_auth->is_allowed())
			$this->where('hidden', 0);

		/**
		 * @todo figure out why those variables don't get unset... it would be
		 * way better to use the iterated in almost all cases in FoOlSlide
		 */
		return parent::get_iterated($limit, $offset);
	}


	/**
	 * Function to create a new entry for a series from scratch. It creates
	 * both a directory and a database entry, and removes them if something
	 * goes wrong.
	 *
	 * @author	Woxxy
	 * @param	array $data with the minimal values, or the function will return
	 * 			false and do nothing.
	 * @return	Returns true on success, false on failure.
	 */
	public function add($data = array())
	{
		// For the series, the stub is just the name.
		$this->to_stub = $data['name'];

		// in case the user specified a stub
		if (array_key_exists('has_custom_slug', $data) && $data['has_custom_slug'] == 1 
			&& isset($data['stub']) && $data['stub'] != '')
			$this->to_stub = $data['stub'];
		
		// stub() checks for to_stub and makes a stub.
		$this->stub = $this->stub();

		// Check if the series database entry and remove dir in case it's not.
		// GUI errors are inner to the function
		if (!$this->update_post_db($data))
		{
			log_message('error', 'add_post: failed writing to database');
			return false;
		}

		// Good job!
		return true;
	}


	/**
	 * Removes series from database, all its pages, chapters, and its directory.
	 * There's no going back from this!
	 *
	 * @author	Woxxy
	 * @return	boolean true on success, false on failure
	 */
	public function remove()
	{
		$result = array();

		// Remove database entry through function
		if (!$this->remove_post_db())
		{
			log_message('error', 'remove_post: failed to delete database entry');
			$result[] = false;
		}
		else
			$result[] = true;

		return (bool)array_product($result);
	}


	/**
	 * Handles both creating of new series in the database and editing old ones.
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
	public function update_post_db($data = array())
	{

		// Check if we're updating or creating a new series by looking at $data["id"].
		// False is returned if the chapter ID was not found.
		if (isset($data["id"]) && $data['id'] != '')
		{
			$this->where("id", $data["id"])->get();
			if ($this->result_count() == 0)
			{
				set_notice('error', _('The post you wanted to edit doesn\'t exist.'));
				log_message('error', 'update_post_db: failed to find requested id');
				return false;
			}
			// Save the stub in a variable in case it gets changed, so we can change folder name
			$old_stub = $this->stub;
			$old_name = $this->name;
		}
		else
		{
			// let's set the creator name if it's a new entry
			$this->creator = $this->logged_id();
		}

		// always set the editor name
		$this->editor = $this->logged_id();
		$input_stub = $data["stub"];
		$has_custom_slug = isset($data["has_custom_slug"]) && $data["has_custom_slug"] == 1;

		// Unset sensible variables
		unset($data["creator"]);
		unset($data["editor"]);
		unset($data["uniqid"]);
		unset($data["has_custom_slug"]);
		unset($data["stub"]);

		// Allow only admins and mods to arbitrarily change the release date
		$CI = & get_instance();
		if (!$CI->tank_auth->is_allowed())
			unset($data["created"]);
		if (!$CI->tank_auth->is_allowed())
			unset($data["edited"]);

		// Loop over the array and assign values to the variables.
		foreach ($data as $key => $value)
		{
			$this->$key = $value;
		}

		// Double check that we have all the necessary automated variables
		if (!isset($this->uniqid))
			$this->uniqid = uniqid();
		if (!isset($this->stub))
			$this->stub = $this->stub();

		// Create a new stub if the name has changed
		if (isset($old_name) && isset($old_stub) && ($old_name != $this->name))
		{
			// Prepare a new stub.
			$this->stub = $this->name;
			// stub() is also able to restub the $this->stub. Already stubbed values won't change.
			$this->stub = $this->stub();
		}

		// stub changed by user
		if ($has_custom_slug & $input_stub != "" && ($this->stub != $input_stub || (isset($old_stub) && $old_stub != $input_stub)))
		{
			$this->stub = $input_stub;
			$this->stub = $this->stub();
		}

		// Make so there's no intersecting stubs, and make a stub with a number in case of duplicates
		// In case this chapter already has a stub and it wasn't changed, don't change it!
		if ((!isset($this->id) || $this->id == '') || (isset($old_stub) && $old_stub != $this->stub))
		{
			$i = 1;
			$found = FALSE;

			$posts = new Post();
			$posts->where('stub', $this->stub)->get();
			if ($posts->result_count() == 0)
			{
				$found = TRUE;
			}

			while (!$found)
			{
				$i++;
				$pre_stub = $this->stub . '_' . $i;
				$posts = new Post();
				$posts->where('stub', $pre_stub)->get();
				if ($posts->result_count() == 0)
				{
					$this->stub = $pre_stub;
					$found = TRUE;
				}
			}
		}

		// This is necessary to make the checkbox work.
		/**
		 *  @todo make the checkbox work consistently across the whole framework
		 */
		if (!isset($data['hidden']) || $data['hidden'] != 1)
			$this->hidden = 0;

		// let's save and give some error check. Push false if fail, true if good.
		$success = $this->save();
		if (!$success)
		{
			if (!$this->valid)
			{
				set_notice('error', _('Check that you have inputted all the required fields.'));
				log_message('error', 'update_post_db: failed validation');
			}
			else
			{
				set_notice('error', _('Failed saving the post to database for unknown reasons.'));
				log_message('error', 'update_post_db: failed to save');
			}
			return false;
		}

		// Good job!
		return true;
	}


	/**
	 * Removes the series from the database, but before it removes all the
	 * related chapters and their pages from the database (not the files).
	 *
	 * @author	Woxxy
	 * @return	object a copy of the series that has been deleted
	 */
	public function remove_post_db()
	{
		$success = $this->delete();
		if (!$success)
		{
			set_notice('error', _('The post couldn\'t be removed from the database for unknown reasons.'));
			log_message('error', 'remove_post_db: id found but entry not removed');
			return false;
		}

		// Return the post clone
		return $temp;
	}


	public function check($repair = FALSE, $recursive = FALSE)
	{
		$dir = "content/comics/" . $this->directory() . "/";
		$errors = array();
		if (!is_dir($dir))
		{
			$errors[] = 'comic_directory_not_found';
			set_notice('warning', _('No directory found for:') . ' ' . $this->name . ' (' . $this->directory() . ')');
			log_message('debug', 'check: comic directory missing at ' . $dir);

			if ($repair)
			{
				// the best we can do is removing the database entry
				$this->remove_comic_db();
			}
		}
		else
		{
			// check that there are no unidentified files in the comic folder
			$map = directory_map($dir, 1);
			foreach ($map as $key => $item)
			{
				$item_path = $dir . $item;
				if (is_dir($item_path))
				{
					// gotta split the directory to get stub and uniqid
					$item_arr = explode('_', $item);
					$uniqid = end($item_arr);
					$stub = str_replace('_' . $uniqid, '', $item);
					$chapter = new Chapter();
					$chapter->where('stub', $stub)->where('uniqid', $uniqid)->get();
					if ($chapter->result_count() == 0)
					{
						$errors[] = 'comic_unidentified_directory_found';
						set_notice('warning', _('Unidentified directory found at:') . ' ' . $item_path);
						log_message('debug', 'check: unidentified directory found at ' . $item_path);
						if ($repair)
						{
							// you have to remove all the files in the folder first
							delete_files($item_path, TRUE);
							rmdir($item_path);
						}
					}
				}
				else
				{
					if ($item != $this->thumbnail && $item != 'thumb_' . $this->thumbnail)
					{
						$ext = strtolower(substr($item, -4));

						if (in_array($ext, array('.zip')))
						{
							$archive = new Archive();
							$archive->where('comic_id', $this->id)->where('filename', $item)->get();
							if ($archive->result_count())
							{
								continue;
							}
						}

						// if it's not the thumbnail image, it's an unidentified file
						$errors[] = 'comic_unidentified_file_found';
						set_notice('warning', _('Unidentified file found at:') . ' ' . $item_path);
						log_message('debug', 'check: unidentified file found at ' . $item_path);
						if ($repair)
						{
							unlink($item_path);
						}
					}
				}
			}
		}

		return $errors;
	}


	public function check_external($repair = FALSE, $recursive = FALSE)
	{
		$this->load->helper('directory');

		// check if all that is inside is writeable
		if (!$this->check_writable('content/comics/'))
		{
			return FALSE;
		}

		// check that every folder has a correpsonding comic
		$map = directory_map('content/comics/', 1);
		foreach ($map as $key => $item)
		{
			// gotta split the directory to get stub and uniqid
			$item_arr = explode('_', $item);
			$uniqid = end($item_arr);
			$stub = str_replace('_' . $uniqid, '', $item);
			$comic = new Comic();
			$comic->where('stub', $stub)->where('uniqid', $uniqid)->get();
			if ($comic->result_count() == 0)
			{
				$errors[] = 'comic_entry_not_found';
				set_notice('warning', _('No database entry found for:') . ' ' . $stub);
				log_message('debug', 'check: database entry missing for ' . $stub);
				if ($repair)
				{
					if (is_dir('content/comics/' . $item))
					{
						// you have to remove all the files in the folder first
						delete_files('content/comics/' . $item, TRUE);
						rmdir('content/comics/' . $item);
					}
					else
					{
						unlink('content/comics/' . $item);
					}
				}
			}
		}

		// check the database entries
		$comics = new Comic();
		$comics->get();
		foreach ($comics->all as $key => $comic)
		{
			$comic->check($repair);
		}

		// if recursive, this will go through a through (and long) check of all chapters
		if ($recursive)
		{
			$chapters = new Chapter();
			$chapters->get_iterated();
			foreach ($chapters as $chapter)
			{
				$chapter->check($repair);
			}

			// viceversa, check that all the database entries have a matching file
			$pages = new Page();
			$pages->get_iterated();
			foreach ($pages as $page)
			{
				$page->check($repair);
			}
		}
	}


	private function check_writable($path)
	{
		$map = directory_map($path, 1);
		foreach ($map as $key => $item)
		{
			if (is_dir($path . $item))
			{
				// check if even the dir itself is writable
				if (!is_writable($path . $item . '/'))
				{
					$errors[] = 'non_writable_directory';
					set_notice('warning', _('Found a non-writable directory.'));
					log_message('debug', 'check: non-writable directory found: ' . $item);
					return FALSE;
				}

				// use the recursive check function
				if (!$this->check_writable($path . $item . '/'))
				{
					return FALSE;
				}
			}
			else
			{
				if (!is_writable($path . $item))
				{
					$errors[] = 'comic_non_writable_file';
					set_notice('warning', _('Found a non-writable file.'));
					log_message('debug', 'check: non-writable file: ' . $item);
					return FALSE;
				}
			}
		}
		return TRUE;
	}


	/**
	 * Returns a ready to use html <a> link that points to the reader
	 *
	 * @author	Woxxy
	 * @return	string <a> to reader
	 */
	public function url()
	{
		return '<a href="' . $this->href() . '" title="' . $this->title() . '">' . $this->title() . '</a>';
	}


	/**
	 * Returns a nicely built title for a chapter
	 *
	 * @author	Woxxy
	 * @return	string the formatted title for the chapter, with chapter and subchapter
	 */
	public function title()
	{
		return $this->name;
	}


	/**
	 * Returns the href to the chapter editing
	 *
	 * @author	Woxxy
	 * @return	string href to chapter editing
	 */
	public function edit_href()
	{
		$CI = & get_instance();
		if (!$CI->tank_auth->is_allowed())
			return "";
		return site_url('/admin/blog/posts/' . $this->stub);
	}


	/**
	 * Returns the url to the chapter editing
	 *
	 * @author	Woxxy
	 * @return	string <a> to chapter editing
	 */
	public function edit_url()
	{
		$CI = & get_instance();
		if (!$CI->tank_auth->is_allowed())
			return "";
		return '<a href="' . $this->edit_href() . '" title="' . _('Edit') . ' ' . $this->title() . '">' . _('Edit') . '</a>';
	}


	/**
	 * Returns the href to the reader. This will create the shortest possible URL.
	 *
	 * @author	Woxxy
	 * @returns string href to reader.
	 */
	public function href()
	{
		return site_url('blog/' . $this->stub);
	}


	/**
	 * Overwrites the original DataMapper to_array() to add some elements
	 *
	 * @param array $fields
	 * @return array
	 */
	public function to_array($fields = '')
	{
		$result = parent::to_array($fields = '');
		$result["href"] = $this->href();
		return $result;
	}

}

/* End of file blogposts.php */
/* Location: ./application/models/blogposts.php */

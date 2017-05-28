<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Post extends DataMapper
{

	static $cached = array();
	var $has_one = array();
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

		// Check if the user is allowed to see protected posts.
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

		// Check if the user is allowed to see protected posts.
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
	 * Removes posts from database.
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
		if (isset($data["id"]) && $data['id'] != '') {
			$this->where("id", $data["id"])->get();
			if ($this->result_count() == 0) {
				set_notice('error', _('The post you wanted to edit doesn\'t exist.'));
				log_message('error', 'update_post_db: failed to find requested id');
				return false;
			}
			// Save the stub in a variable in case it gets changed, so we can change folder name
			$old_stub = $this->stub;
			$old_name = $this->name;
		} else {
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
	 * Removes the post from the database
	 *
	 * @author	Woxxy
	 * @return	object a copy of the posts that has been deleted
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
	 * Returns a nicely built title for a post
	 *
	 * @author	Woxxy
	 * @return	string the formatted title for the post
	 */
	public function title()
	{
		return $this->name;
	}


	/**
	 * Returns the href to the post editing
	 *
	 * @author	Woxxy
	 * @return	string href to post editing
	 */
	public function edit_href()
	{
		$CI = & get_instance();
		if (!$CI->tank_auth->is_allowed())
			return "";
		return site_url('/admin/blog/posts/' . $this->stub);
	}


	/**
	 * Returns the url to the post editing
	 *
	 * @author	Woxxy
	 * @return	string <a> to post editing
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

/* End of file post.php */
/* Location: ./application/models/post.php */
